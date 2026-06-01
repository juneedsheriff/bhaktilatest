<?php

/**
 * Import legacy abroad temple CSV export (39 columns, no header) into `abroad` table.
 */
function abroad_import_normalize_country_code(mysqli $db, $countryName)
{
    $countryName = trim((string) $countryName);
    if ($countryName === '') {
        return '';
    }

    static $aliases = [
        'USA' => 'US',
        'Usa' => 'US',
        'UK' => 'GB',
        'United Kingdom' => 'GB',
        'SWITZERLAND' => 'CH',
        'Switzerland' => 'CH',
        'CANADA' => 'CA',
        'Canada' => 'CA',
        'Australia' => 'AU',
        'New Zealand' => 'NZ',
        'Singapore' => 'SG',
        'SINGAPORE' => 'SG',
        'Netherlands' => 'NL',
        'NETHERLANDS' => 'NL',
        'Bangladesh' => 'BD',
        'BANGLADESH' => 'BD',
        'Germany' => 'DE',
        'GERMANY' => 'DE',
        'Malaysia' => 'MY',
        'MALAYSIA' => 'MY',
        'Sri Lanka' => 'LK',
        'Japan' => 'JP',
        'Pakistan' => 'PK',
        'PAKISTAN' => 'PK',
        'Poland' => 'PL',
        'POLAND' => 'PL',
        'Thailand' => 'TH',
        'THAILAND' => 'TH',
        'Fiji' => 'FJ',
        'FIJI' => 'FJ',
        'Ghana' => 'GH',
        'GHANA' => 'GH',
        'Guyana' => 'GY',
        'GUYANA' => 'GY',
        'Mauritius' => 'MU',
        'MAURITIUS' => 'MU',
        'Nigeria' => 'NG',
        'NIGERIA' => 'NG',
        'Uganda' => 'UG',
        'UGANDA' => 'UG',
        'Tanzania' => 'TZ',
        'TANZANIA' => 'TZ',
        'United Arab Emirates' => 'AE',
        'Nepal' => 'NP',
        'NEPAL' => 'NP',
        'Indonesia' => 'ID',
        'Brazil' => 'BR',
        'Hong Kong' => 'HK',
        'Vietnam' => 'VN',
        'Afghanistan' => 'AF',
        'Cambodia' => 'KH',
        'CAMBODIA' => 'KH',
        'Myanmar' => 'MM',
        'Kenya' => 'KE',
        'Botswana' => 'BW',
        'Laos' => 'LA',
        'South Africa' => 'ZA',
        'South Korea' => 'KR',
        'Suriname' => 'SR',
        'Trinidad &Tobago' => 'TT',
        'philippines' => 'PH',
    ];

    if (isset($aliases[$countryName])) {
        return $aliases[$countryName];
    }

    $escaped = mysqli_real_escape_string($db, $countryName);
    $sql = "SELECT country_code FROM country
            WHERE country_name = '$escaped'
               OR country_code = '$escaped'
               OR country_name LIKE '%$escaped%'
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return $row['country_code'];
    }

    return strtoupper(substr($countryName, 0, 2));
}

function abroad_import_god_sub_name_from_name($godName)
{
    $godName = trim((string) $godName);
    $words = preg_split('/\s+/', $godName, -1, PREG_SPLIT_NO_EMPTY);
    if ($words === false || $words === []) {
        return 'GOD';
    }
    if (count($words) === 1) {
        $base = preg_replace('/[^A-Za-z0-9]/', '', $words[0]);

        return strtoupper(substr($base, 0, 20)) ?: 'GOD';
    }
    $acronym = '';
    foreach ($words as $word) {
        if ($word !== '') {
            $acronym .= strtoupper($word[0]);
        }
    }

    return substr($acronym, 0, 20) ?: 'GOD';
}

/**
 * Insert into `god` (same fields as god_process.php) when name is missing.
 */
function abroad_import_create_god(mysqli $db, $godName)
{
    $godName = trim((string) $godName);
    if ($godName === '') {
        return '';
    }

    $escaped = mysqli_real_escape_string($db, $godName);
    $dup = mysqli_query($db, "SELECT index_id FROM god WHERE god_name = '$escaped' LIMIT 1");
    if ($dup && ($row = mysqli_fetch_assoc($dup))) {
        return (string) $row['index_id'];
    }

    $subName = mysqli_real_escape_string($db, abroad_import_god_sub_name_from_name($godName));
    $sql = "INSERT INTO `god` (`god_name`, `sub_name`) VALUES ('$escaped', '$subName')";
    if (!mysqli_query($db, $sql)) {
        return '';
    }

    return (string) mysqli_insert_id($db);
}

function abroad_import_lookup_god_id(mysqli $db, $godNames, $createIfMissing = true)
{
    $godNames = trim((string) $godNames);
    if ($godNames === '') {
        return '';
    }

    $parts = preg_split('/[,;&]+/', $godNames);
    $firstName = '';
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        if ($firstName === '') {
            $firstName = $part;
        }

        if (ctype_digit($part)) {
            $id = (int) $part;
            $byId = mysqli_query($db, "SELECT index_id FROM god WHERE index_id = $id LIMIT 1");
            if ($byId && ($row = mysqli_fetch_assoc($byId))) {
                return (string) $row['index_id'];
            }
        }

        $escaped = mysqli_real_escape_string($db, $part);
        $sql = "SELECT index_id FROM god
                WHERE god_name = '$escaped'
                   OR god_name LIKE '%$escaped%'
                   OR '$escaped' LIKE CONCAT('%', god_name, '%')
                ORDER BY LENGTH(god_name) ASC
                LIMIT 1";
        $result = mysqli_query($db, $sql);
        if ($result && ($row = mysqli_fetch_assoc($result))) {
            return (string) $row['index_id'];
        }
    }

    if ($createIfMissing && $firstName !== '') {
        return abroad_import_create_god($db, $firstName);
    }

    return '';
}

function abroad_import_clean_value($value)
{
    $value = trim((string) $value);
    if ($value === '' || strtoupper($value) === 'NULL') {
        return '';
    }

    return $value;
}

function abroad_import_normalize_image($path)
{
    $path = abroad_import_clean_value($path);
    if ($path === '' || substr($path, -1) === '/') {
        return '';
    }

    $path = preg_replace('#^abroad_temples/#i', '', $path);
    return basename(str_replace('\\', '/', $path));
}

function abroad_import_collect_images(array $row)
{
    $imageIndexes = [10, 11, 12, 13, 14, 21, 22, 23, 24, 25];
    $images = [];

    foreach ($imageIndexes as $index) {
        $image = abroad_import_normalize_image($row[$index] ?? '');
        if ($image !== '' && !in_array($image, $images, true)) {
            $images[] = $image;
        }
    }

    return $images;
}

function abroad_import_escape(mysqli $db, $value)
{
    return mysqli_real_escape_string($db, (string) $value);
}

function abroad_import_strip_bom($value)
{
    $value = (string) $value;
    if (strncmp($value, "\xEF\xBB\xBF", 3) === 0) {
        $value = substr($value, 3);
    }

    return trim($value);
}

function abroad_import_detect_format(array $row)
{
    $first = abroad_import_strip_bom($row[0] ?? '');
    if (strtolower($first) === 'banner_image') {
        return 'sample';
    }

    if (count($row) >= 30) {
        return 'legacy';
    }

    return 'unknown';
}

function abroad_import_row_from_legacy(mysqli $db, array $row, $logDate)
{
    $legacyId = preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
    $title = abroad_import_clean_value($row[4] ?? '');
    if ($title === '') {
        return null;
    }

    $images = abroad_import_collect_images($row);
    $photos = $images[0] ?? '';
    $gallery = implode(',', $images);

    $statusRaw = strtolower(abroad_import_clean_value($row[19] ?? ''));
    $status = ($statusRaw === 'approved') ? 'approved' : 'unapproved';

    $sthalam = abroad_import_clean_value($row[5] ?? '');
    if ($sthalam === '') {
        $sthalam = abroad_import_clean_value($row[8] ?? '');
    }

    return [
        'title' => $title,
        'temple_place' => abroad_import_clean_value($row[3] ?? ''),
        'sthalam' => $sthalam,
        'puranam' => abroad_import_clean_value($row[29] ?? ''),
        'photos' => $photos,
        'varnam' => abroad_import_clean_value($row[8] ?? ''),
        'highlights' => '',
        'sevas' => abroad_import_clean_value($row[17] ?? ''),
        'open_time' => '00:00:00',
        'close_time' => '00:00:00',
        'gallery_image' => $gallery,
        'country' => abroad_import_normalize_country_code($db, $row[1] ?? ''),
        'state' => abroad_import_clean_value($row[3] ?? ''),
        'city' => abroad_import_clean_value($row[3] ?? ''),
        'address' => abroad_import_clean_value($row[7] ?? ''),
        'order_by' => $legacyId !== '' ? (int) $legacyId : 0,
        'speciality' => abroad_import_clean_value($row[36] ?? ''),
        'time' => abroad_import_clean_value($row[6] ?? ''),
        'god_id' => abroad_import_lookup_god_id($db, $row[2] ?? ''),
        'my_stery' => abroad_import_clean_value($row[20] ?? '') !== '' ? abroad_import_clean_value($row[20] ?? '') : '0',
        'banner' => '',
        'status' => $status,
        'log_date' => $logDate,
    ];
}

function abroad_import_row_from_sample(array $row, $logDate)
{
    $title = abroad_import_clean_value($row[3] ?? '');
    if ($title === '') {
        return null;
    }

    return [
        'title' => $title,
        'temple_place' => abroad_import_clean_value($row[4] ?? ''),
        'sthalam' => abroad_import_clean_value($row[5] ?? ''),
        'puranam' => abroad_import_clean_value($row[6] ?? ''),
        'photos' => abroad_import_normalize_image($row[7] ?? ''),
        'varnam' => abroad_import_clean_value($row[8] ?? ''),
        'highlights' => abroad_import_clean_value($row[9] ?? ''),
        'sevas' => abroad_import_clean_value($row[10] ?? ''),
        'open_time' => abroad_import_clean_value($row[11] ?? '') ?: '00:00:00',
        'close_time' => abroad_import_clean_value($row[12] ?? '') ?: '00:00:00',
        'gallery_image' => abroad_import_clean_value($row[13] ?? ''),
        'country' => abroad_import_clean_value($row[14] ?? ''),
        'state' => abroad_import_clean_value($row[15] ?? ''),
        'city' => abroad_import_clean_value($row[16] ?? ''),
        'address' => abroad_import_clean_value($row[17] ?? ''),
        'order_by' => (int) abroad_import_clean_value($row[18] ?? ''),
        'speciality' => abroad_import_clean_value($row[19] ?? ''),
        'time' => abroad_import_clean_value($row[20] ?? ''),
        'god_id' => abroad_import_clean_value($row[2] ?? ''),
        'my_stery' => abroad_import_clean_value($row[1] ?? '') !== '' ? abroad_import_clean_value($row[1] ?? '') : '0',
        'banner' => abroad_import_normalize_image($row[0] ?? ''),
        'status' => 'unapproved',
        'log_date' => $logDate,
    ];
}

function abroad_import_insert(mysqli $db, array $data)
{
    if (!function_exists('abroad_sanitize_abroad_content_fields')) {
        require_once dirname(__DIR__, 2) . '/include/abroad_listing_helpers.php';
    }

    $sanitized = abroad_sanitize_abroad_content_fields($data['sthalam'] ?? '', $data['varnam'] ?? '', $data['map_embed'] ?? '');
    $data['sthalam'] = $sanitized['sthalam'];
    $data['varnam'] = $sanitized['varnam'];
    $data['map_embed'] = $sanitized['map_embed'];

    $contactSanitized = abroad_sanitize_abroad_contact_fields($data['sevas'] ?? '', $data['speciality'] ?? '');
    $data['sevas'] = $contactSanitized['sevas'];
    $data['speciality'] = $contactSanitized['contact'];

    $fields = [
        'banner', 'my_stery', 'god_id', 'title', 'temple_place', 'log_date', 'sthalam', 'puranam',
        'photos', 'varnam', 'highlights', 'sevas', 'open_time', 'close_time', 'gallery_image',
        'country', 'state', 'city', 'address', 'map_embed', 'order_by', 'speciality', 'time', 'status',
    ];

    $values = [];
    foreach ($fields as $field) {
        $values[] = "'" . abroad_import_escape($db, $data[$field] ?? '') . "'";
    }

    $sql = 'INSERT INTO `abroad` (`' . implode('`, `', $fields) . '`) VALUES (' . implode(', ', $values) . ')';
    return mysqli_query($db, $sql);
}

function abroad_import_from_file(mysqli $db, $filePath)
{
    $file = fopen($filePath, 'r');
    if (!$file) {
        return ['imported' => 0, 'skipped' => 0, 'errors' => 1, 'format' => 'unknown', 'messages' => ['Unable to open CSV file.']];
    }

    $logDate = date('Y-m-d');
    $imported = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $format = 'unknown';
    $line = 0;

    while (($row = fgetcsv($file)) !== false) {
        $line++;

        if ($line === 1) {
            $format = abroad_import_detect_format($row);
            if ($format === 'sample') {
                continue;
            }
            if ($format === 'unknown') {
                fclose($file);
                return [
                    'imported' => 0,
                    'skipped' => 0,
                    'errors' => 1,
                    'format' => 'unknown',
                    'messages' => ['Unrecognized CSV format.'],
                ];
            }
        }

        if ($format === 'legacy') {
            $data = abroad_import_row_from_legacy($db, $row, $logDate);
        } else {
            $data = abroad_import_row_from_sample($row, $logDate);
        }

        if ($data === null) {
            $skipped++;
            continue;
        }

        if (!abroad_import_insert($db, $data)) {
            $errors++;
            if (count($messages) < 5) {
                $messages[] = 'Line ' . $line . ': ' . mysqli_error($db);
            }
            continue;
        }

        $imported++;
    }

    fclose($file);

    return [
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors,
        'format' => $format,
        'messages' => $messages,
    ];
}
