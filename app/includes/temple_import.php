<?php

/**
 * Import legacy India temple CSV export (39 columns, no header) or sample template into `temples`.
 */
require_once __DIR__ . '/abroad_import.php';

function temple_import_lookup_state_code(mysqli $db, $stateName, $countryCode = 'IN')
{
    $stateName = trim((string) $stateName);
    if ($stateName === '') {
        return '';
    }

    $escaped = abroad_import_escape($db, $stateName);
    $countryEsc = abroad_import_escape($db, $countryCode);
    $sql = "SELECT state_code FROM state
            WHERE country_code = '$countryEsc'
              AND (state_name = '$escaped' OR state_code = '$escaped' OR state_name LIKE '%$escaped%')
            ORDER BY (state_name = '$escaped') DESC, LENGTH(state_name) ASC
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (string) $row['state_code'];
    }

    return '';
}

function temple_import_lookup_city_id(mysqli $db, $placeName, $stateCode = '')
{
    $placeName = trim((string) $placeName);
    if ($placeName === '') {
        return '';
    }

    $escaped = abroad_import_escape($db, $placeName);
    $stateFilter = '';
    if ($stateCode !== '') {
        $stateEsc = abroad_import_escape($db, $stateCode);
        $stateFilter = " AND c.state_code = '$stateEsc'";
    }

    $sql = "SELECT c.city_id FROM city c
            WHERE (c.city_name = '$escaped' OR c.city_name LIKE '%$escaped%' OR '$escaped' LIKE CONCAT('%', c.city_name, '%'))
            $stateFilter
            ORDER BY (c.city_name = '$escaped') DESC, LENGTH(c.city_name) ASC
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (string) $row['city_id'];
    }

    return '';
}

function temple_import_lookup_town_id(mysqli $db, $placeName, $cityName = '')
{
    $placeName = trim((string) $placeName);
    if ($placeName === '') {
        return '';
    }

    $escaped = abroad_import_escape($db, $placeName);
    $cityFilter = '';
    if ($cityName !== '') {
        $cityEsc = abroad_import_escape($db, $cityName);
        $cityFilter = " AND (city = '$cityEsc' OR city LIKE '%$cityEsc%')";
    }

    $sql = "SELECT id FROM towns
            WHERE (town_name = '$escaped' OR town_name LIKE '%$escaped%')
            $cityFilter
            ORDER BY (town_name = '$escaped') DESC, LENGTH(town_name) ASC
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (string) $row['id'];
    }

    return '';
}

function temple_import_lookup_city_name(mysqli $db, $cityId)
{
    if ($cityId === '') {
        return '';
    }

    $escaped = abroad_import_escape($db, $cityId);
    $sql = "SELECT city_name FROM city WHERE city_id = '$escaped' LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (string) $row['city_name'];
    }

    return '';
}

function temple_import_collect_images(array $row)
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

function temple_import_pick_banner_and_photo(array $images)
{
    $banner = '';
    $photo = '';

    foreach ($images as $image) {
        if ($banner === '' && stripos($image, 'banner') !== false) {
            $banner = $image;
        }
    }

    foreach ($images as $image) {
        if ($photo === '' && stripos($image, 'banner') === false) {
            $photo = $image;
        }
    }

    if ($photo === '' && !empty($images)) {
        $photo = $images[0];
    }
    if ($banner === '' && !empty($images)) {
        $banner = $images[0];
    }

    return [$banner, $photo];
}

function temple_import_normalize_status($raw)
{
    $raw = strtolower(abroad_import_clean_value($raw));
    // Match legacy Temple.aspx / india_other tempstatus values
    if ($raw === 'approved') {
        return 'approved';
    }
    if ($raw === 'pending') {
        return 'unapproved';
    }
    if ($raw === 'new') {
        return 'new';
    }
    if (in_array($raw, ['rejected', 'reject', 'denied'], true)) {
        return 'rejected';
    }
    if ($raw === 'unapproved') {
        return 'unapproved';
    }

    // Blank tempstatus in legacy export — not Approval Pending (ASPX counts only explicit Pending)
    return 'new';
}

function temple_import_detect_format(array $row)
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

function temple_import_row_from_legacy(mysqli $db, array $row, $logDate)
{
    $legacyId = preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
    $title = abroad_import_clean_value($row[4] ?? '');
    if ($title === '') {
        return null;
    }

    $placeName = abroad_import_clean_value($row[3] ?? '');
    $stateCode = temple_import_lookup_state_code($db, $row[1] ?? '');
    $cityId = temple_import_lookup_city_id($db, $placeName, $stateCode);
    $cityName = temple_import_lookup_city_name($db, $cityId);
    $townId = temple_import_lookup_town_id($db, $placeName, $cityName);

    $images = temple_import_collect_images($row);
    [$banner, $photos] = temple_import_pick_banner_and_photo($images);
    $gallery = implode(',', $images);

    $sthalam = abroad_import_clean_value($row[5] ?? '');
    if ($sthalam === '') {
        $sthalam = abroad_import_clean_value($row[8] ?? '');
    }

    $mapOrEmbed = abroad_import_clean_value($row[8] ?? '');
    $videoUrl = '';
    if ($mapOrEmbed !== '' && (stripos($mapOrEmbed, 'google.com/maps') !== false || stripos($mapOrEmbed, 'maps.app.goo.gl') !== false || stripos($mapOrEmbed, 'maps/embed') !== false)) {
        $videoUrl = $mapOrEmbed;
    }

    $effectiveLogDate = $logDate;
    $rowLogDate = abroad_import_clean_value($row[34] ?? '');
    if ($rowLogDate !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $rowLogDate)) {
        $effectiveLogDate = $rowLogDate;
    }

    return [
        'title' => $title,
        'temple_place' => $placeName,
        'sthalam' => $sthalam,
        'puranam' => abroad_import_clean_value($row[29] ?? ''),
        'photos' => $photos,
        'banner' => $banner,
        'varnam' => abroad_import_clean_value($row[20] ?? ''),
        'highlights' => '',
        'sevas' => '',
        'open_time' => '00:00:00',
        'close_time' => '00:00:00',
        'gallery_image' => $gallery,
        'video_url' => $videoUrl,
        'video_thumbnail' => '',
        'country' => 'IN',
        'state' => $stateCode,
        'city' => $cityId,
        'town' => $townId,
        'address' => abroad_import_clean_value($row[17] ?? ''),
        'order_by' => $legacyId !== '' ? (int) $legacyId : 0,
        'speciality' => abroad_import_clean_value($row[20] ?? ''),
        'speciality_title' => abroad_import_clean_value($row[36] ?? ''),
        'time' => abroad_import_clean_value($row[6] ?? ''),
        'god_id' => abroad_import_lookup_god_id($db, $row[2] ?? ''),
        'csv_god_name' => abroad_import_clean_value($row[2] ?? ''),
        'my_stery' => '0',
        'status' => temple_import_normalize_status($row[19] ?? ''),
        'log_date' => $effectiveLogDate,
        'legacy_index_id' => $legacyId !== '' ? (int) $legacyId : 0,
    ];
}

function temple_import_row_from_sample(mysqli $db, array $row, $logDate)
{
    $title = abroad_import_clean_value($row[3] ?? '');
    if ($title === '') {
        return null;
    }

    $stateVal = abroad_import_clean_value($row[15] ?? '');
    $stateCode = $stateVal;
    if (!preg_match('/^[A-Z]{2}$/', $stateVal)) {
        $stateCode = temple_import_lookup_state_code($db, $stateVal);
    }

    $cityVal = abroad_import_clean_value($row[16] ?? '');
    $cityId = $cityVal;
    if (!ctype_digit($cityVal)) {
        $cityId = temple_import_lookup_city_id($db, $cityVal, $stateCode);
    }

    $images = [];
    $thumb = abroad_import_normalize_image($row[7] ?? '');
    $bannerImg = abroad_import_normalize_image($row[0] ?? '');
    $galleryRaw = abroad_import_clean_value($row[13] ?? '');
    if ($thumb !== '') {
        $images[] = $thumb;
    }
    if ($bannerImg !== '' && !in_array($bannerImg, $images, true)) {
        $images[] = $bannerImg;
    }
    if ($galleryRaw !== '') {
        foreach (explode(',', $galleryRaw) as $part) {
            $img = abroad_import_normalize_image($part);
            if ($img !== '' && !in_array($img, $images, true)) {
                $images[] = $img;
            }
        }
    }

    [$banner, $photos] = temple_import_pick_banner_and_photo($images);
    if ($banner === '' && $bannerImg !== '') {
        $banner = $bannerImg;
    }
    if ($photos === '' && $thumb !== '') {
        $photos = $thumb;
    }

    return [
        'title' => $title,
        'temple_place' => abroad_import_clean_value($row[4] ?? ''),
        'sthalam' => abroad_import_clean_value($row[5] ?? ''),
        'puranam' => abroad_import_clean_value($row[6] ?? ''),
        'photos' => $photos,
        'banner' => $banner,
        'varnam' => abroad_import_clean_value($row[8] ?? ''),
        'highlights' => abroad_import_clean_value($row[9] ?? ''),
        'sevas' => abroad_import_clean_value($row[10] ?? ''),
        'open_time' => abroad_import_clean_value($row[11] ?? '') ?: '00:00:00',
        'close_time' => abroad_import_clean_value($row[12] ?? '') ?: '00:00:00',
        'gallery_image' => $galleryRaw !== '' ? implode(',', $images) : implode(',', $images),
        'video_url' => '',
        'video_thumbnail' => '',
        'country' => abroad_import_clean_value($row[14] ?? '') ?: 'IN',
        'state' => $stateCode,
        'city' => $cityId,
        'town' => '',
        'address' => abroad_import_clean_value($row[17] ?? ''),
        'order_by' => (int) abroad_import_clean_value($row[18] ?? ''),
        'speciality' => abroad_import_clean_value($row[19] ?? ''),
        'speciality_title' => abroad_import_clean_value($row[21] ?? ''),
        'time' => abroad_import_clean_value($row[20] ?? ''),
        'god_id' => abroad_import_lookup_god_id($db, $row[2] ?? ''),
        'my_stery' => abroad_import_clean_value($row[1] ?? '') !== '' ? abroad_import_clean_value($row[1] ?? '') : '0',
        'status' => 'unapproved',
        'log_date' => $logDate,
        'legacy_index_id' => 0,
    ];
}

/**
 * Set god_id on temples that were imported before auto-create or lost god_id on save.
 */
function temple_import_backfill_missing_god_ids(mysqli $db, array $byLegacyId)
{
    $filled = 0;
    foreach ($byLegacyId as $legacyId => $data) {
        $lid = (int) $legacyId;
        $godId = trim((string) ($data['god_id'] ?? ''));
        if ($godId === '' || (int) $godId <= 0) {
            $csvGod = trim((string) ($data['csv_god_name'] ?? ''));
            if ($csvGod === '') {
                continue;
            }
            $godId = abroad_import_lookup_god_id($db, $csvGod, true);
            if ($godId === '') {
                continue;
            }
        }
        $esc = abroad_import_escape($db, $godId);
        foreach (["order_by=$lid", "index_id=$lid"] as $where) {
            $q = mysqli_query(
                $db,
                "UPDATE temples SET god_id='$esc' WHERE $where AND (god_id IS NULL OR god_id = '' OR god_id = 0 OR god_id = '0')"
            );
            if ($q) {
                $filled += mysqli_affected_rows($db);
            }
        }
    }

    return $filled;
}

function temple_import_remove_duplicate_order_by(mysqli $db)
{
    $sql = 'DELETE t1 FROM temples t1
            INNER JOIN temples t2 ON t1.order_by = t2.order_by AND t1.order_by > 0 AND t1.index_id > t2.index_id';

    if (!mysqli_query($db, $sql)) {
        return 0;
    }

    return mysqli_affected_rows($db);
}

function temple_import_find_existing_id(mysqli $db, array $data, $matchByTitle = true)
{
    $legacyId = (int) ($data['legacy_index_id'] ?? 0);
    if ($legacyId > 0) {
        $check = mysqli_query($db, "SELECT index_id FROM temples WHERE index_id = $legacyId LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            return $legacyId;
        }
        $orderCheck = mysqli_query(
            $db,
            "SELECT index_id FROM temples WHERE order_by = $legacyId
             ORDER BY CASE WHEN index_id = $legacyId THEN 0 ELSE 1 END, index_id ASC
             LIMIT 1"
        );
        if ($orderCheck && mysqli_num_rows($orderCheck) > 0) {
            return (int) mysqli_fetch_assoc($orderCheck)['index_id'];
        }
    }

    if (!$matchByTitle) {
        return 0;
    }

    $titleEsc = abroad_import_escape($db, $data['title'] ?? '');
    $stateEsc = abroad_import_escape($db, $data['state'] ?? '');
    $placeEsc = abroad_import_escape($db, $data['temple_place'] ?? '');
    $dup = mysqli_query(
        $db,
        "SELECT index_id FROM temples
         WHERE title = '$titleEsc' AND state = '$stateEsc' AND temple_place = '$placeEsc'
         LIMIT 1"
    );
    if ($dup && ($row = mysqli_fetch_assoc($dup))) {
        return (int) $row['index_id'];
    }

    return 0;
}

function temple_import_row_fields()
{
    return [
        'banner', 'my_stery', 'god_id', 'title', 'temple_place', 'log_date', 'sthalam', 'puranam',
        'photos', 'varnam', 'highlights', 'sevas', 'open_time', 'close_time', 'gallery_image',
        'video_url', 'video_thumbnail', 'country', 'state', 'city', 'town', 'address',
        'order_by', 'speciality', 'time', 'speciality_title', 'status',
    ];
}

function temple_import_save(mysqli $db, array $data, $useLegacyIdOnInsert = false)
{
    $fields = temple_import_row_fields();
    $legacyId = (int) ($data['legacy_index_id'] ?? 0);

    $existingId = temple_import_find_existing_id($db, $data, !$useLegacyIdOnInsert);
    if ($existingId > 0) {
        $sets = [];
        foreach ($fields as $field) {
            $sets[] = "`$field`='" . abroad_import_escape($db, $data[$field] ?? '') . "'";
        }
        $sql = 'UPDATE `temples` SET ' . implode(', ', $sets) . " WHERE `index_id`=$existingId";
        return mysqli_query($db, $sql) ? 'updated' : false;
    }

    $insertFields = $fields;
    if ($useLegacyIdOnInsert && $legacyId > 0) {
        $slot = mysqli_query($db, "SELECT index_id FROM temples WHERE index_id = $legacyId LIMIT 1");
        if (!$slot || mysqli_num_rows($slot) === 0) {
            array_unshift($insertFields, 'index_id');
        }
    }
    $values = [];
    foreach ($insertFields as $field) {
        if ($field === 'index_id') {
            $values[] = (string) (int) $legacyId;
        } else {
            $values[] = "'" . abroad_import_escape($db, $data[$field] ?? '') . "'";
        }
    }
    $sql = 'INSERT INTO `temples` (`' . implode('`, `', $insertFields) . '`) VALUES (' . implode(', ', $values) . ')';
    if (!mysqli_query($db, $sql)) {
        return false;
    }

    return 'inserted';
}

function temple_import_count_by_status(mysqli $db)
{
    $counts = ['approved' => 0, 'unapproved' => 0, 'new' => 0, 'rejected' => 0, 'other' => 0, 'total' => 0];
    $r = mysqli_query($db, 'SELECT status, COUNT(*) AS c FROM temples GROUP BY status');
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $st = strtolower(trim((string) ($row['status'] ?? '')));
            $c = (int) $row['c'];
            $counts['total'] += $c;
            if (isset($counts[$st])) {
                $counts[$st] = $c;
            } else {
                $counts['other'] += $c;
            }
        }
    }

    return $counts;
}

/**
 * Full reconcile: one DB row per CSV legacy id, statuses match india_other / Temple.aspx.
 */
function temple_import_reconcile_from_file(mysqli $db, $filePath, $removeOrphans = true)
{
    $file = fopen($filePath, 'r');
    if (!$file) {
        return ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'removed' => 0, 'errors' => 1, 'format' => 'unknown', 'messages' => ['Unable to open CSV file.'], 'counts' => []];
    }

    $logDate = date('Y-m-d');
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $format = 'unknown';
    $line = 0;
    $byLegacyId = [];

    while (($row = fgetcsv($file)) !== false) {
        $line++;
        if ($line === 1) {
            $format = temple_import_detect_format($row);
            if ($format === 'sample') {
                continue;
            }
            if ($format === 'unknown') {
                fclose($file);
                return [
                    'imported' => 0, 'updated' => 0, 'skipped' => 0, 'removed' => 0, 'errors' => 1,
                    'format' => 'unknown', 'messages' => ['Unrecognized CSV format.'], 'counts' => [],
                ];
            }
        }

        if ($format !== 'legacy') {
            continue;
        }

        $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
        if ($legacyId <= 0) {
            $skipped++;
            continue;
        }

        $data = temple_import_row_from_legacy($db, $row, $logDate);
        if ($data === null) {
            $skipped++;
            continue;
        }

        $byLegacyId[$legacyId] = $data;
    }
    fclose($file);

    foreach ($byLegacyId as $legacyId => $data) {
        $result = temple_import_save($db, $data, true);
        if ($result === false) {
            $errors++;
            if (count($messages) < 5) {
                $messages[] = 'Legacy ID ' . $legacyId . ': ' . mysqli_error($db);
            }
            continue;
        }
        if ($result === 'updated') {
            $updated++;
        } else {
            $imported++;
        }
    }

    $godsFilled = temple_import_backfill_missing_god_ids($db, $byLegacyId);

    $dupesRemoved = temple_import_remove_duplicate_order_by($db);

    $removed = 0;
    if ($removeOrphans && !empty($byLegacyId)) {
        $ids = implode(',', array_map('intval', array_keys($byLegacyId)));
        $del = mysqli_query($db, "DELETE FROM temples WHERE order_by NOT IN ($ids) AND index_id NOT IN ($ids)");
        if ($del) {
            $removed = mysqli_affected_rows($db);
        }
    }

    $csvExpected = ['approved' => 0, 'pending' => 0, 'new' => 0, 'rejected' => 0];
    foreach ($byLegacyId as $data) {
        $st = $data['status'] ?? '';
        if ($st === 'approved') {
            $csvExpected['approved']++;
        } elseif ($st === 'unapproved') {
            $csvExpected['pending']++;
        } elseif ($st === 'new') {
            $csvExpected['new']++;
        } elseif ($st === 'rejected') {
            $csvExpected['rejected']++;
        }
    }

    $dbCounts = temple_import_count_by_status($db);

    return [
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'removed' => $removed,
        'dupes_removed' => $dupesRemoved,
        'gods_filled' => $godsFilled,
        'errors' => $errors,
        'format' => $format,
        'messages' => $messages,
        'csv_rows' => count($byLegacyId),
        'csv_expected' => $csvExpected,
        'db_counts' => $dbCounts,
    ];
}

function temple_import_from_file(mysqli $db, $filePath)
{
    $file = fopen($filePath, 'r');
    if (!$file) {
        return ['imported' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 1, 'format' => 'unknown', 'messages' => ['Unable to open CSV file.']];
    }

    $logDate = date('Y-m-d');
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $format = 'unknown';
    $line = 0;

    while (($row = fgetcsv($file)) !== false) {
        $line++;

        if ($line === 1) {
            $format = temple_import_detect_format($row);
            if ($format === 'sample') {
                continue;
            }
            if ($format === 'unknown') {
                fclose($file);
                return [
                    'imported' => 0,
                    'updated' => 0,
                    'skipped' => 0,
                    'errors' => 1,
                    'format' => 'unknown',
                    'messages' => ['Unrecognized CSV format. Expected legacy export (39 columns) or temple-india.csv template.'],
                ];
            }
        }

        if ($format === 'legacy') {
            $data = temple_import_row_from_legacy($db, $row, $logDate);
        } else {
            $data = temple_import_row_from_sample($db, $row, $logDate);
        }

        if ($data === null) {
            $skipped++;
            continue;
        }

        unset($data['legacy_index_id']);
        $result = temple_import_save($db, $data);
        if ($result === false) {
            $errors++;
            if (count($messages) < 5) {
                $messages[] = 'Line ' . $line . ': ' . mysqli_error($db);
            }
            continue;
        }

        if ($result === 'updated') {
            $updated++;
        } else {
            $imported++;
        }
    }

    fclose($file);

    return [
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors,
        'format' => $format,
        'messages' => $messages,
    ];
}
