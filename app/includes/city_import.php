<?php

require_once __DIR__ . '/abroad_import.php';

/**
 * Legacy India export: city_id, city_name, NULL, legacy_state_id (no header).
 */
function city_import_india_legacy_state_code_map()
{
    return [
        1 => 'UP',
        2 => 'MH',
        3 => 'BR',
        4 => 'WB',
        5 => 'AP',
        6 => 'TN',
        7 => 'MP',
        8 => 'RJ',
        9 => 'KA',
        10 => 'GJ',
        11 => 'OR',
        12 => 'KL',
        14 => 'AS',
        15 => 'PB',
        16 => 'HR',
        17 => 'CT',
        18 => 'JK',
        20 => 'HP',
        25 => 'GA',
        29 => 'PY',
        31 => 'DL',
    ];
}

function city_import_india_resolve_state_code($legacyStateId, $cityName)
{
    $legacyStateId = (int) $legacyStateId;
    $cityKey = strtolower(trim((string) $cityName));

    static $telanganaCities = [
        'hyderabad' => true,
        'warangal' => true,
        'karimnagar' => true,
        'nizamabad' => true,
        'khammam' => true,
    ];
    if ($legacyStateId === 5 && isset($telanganaCities[$cityKey])) {
        return 'TG';
    }

    $map = city_import_india_legacy_state_code_map();

    return $map[$legacyStateId] ?? '';
}

function city_import_load_india_states(mysqli $db)
{
    static $byCode = null;
    if ($byCode !== null) {
        return $byCode;
    }

    $byCode = [];
    $r = mysqli_query($db, "SELECT state_id, state_code, country_id, country_code FROM state WHERE country_code = 'IN'");
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $byCode[$row['state_code']] = $row;
        }
    }

    return $byCode;
}

function city_import_normalize_state_name($stateName)
{
    $stateName = trim((string) $stateName);
    $stateName = preg_replace('/\s+/', ' ', $stateName);

    static $aliases = [
        'orissa' => 'Odisha',
        'pondicherry' => 'Puducherry',
        'jammu & kashmir' => 'Jammu and Kashmir',
        'jammu and kashmir' => 'Jammu and Kashmir',
        'chattisgarh' => 'Chhattisgarh',
        'chhatisgarh' => 'Chhattisgarh',
        'uttaranchal' => 'Uttarakhand',
    ];

    $key = strtolower($stateName);
    if (isset($aliases[$key])) {
        return $aliases[$key];
    }

    return $stateName;
}

function city_import_lookup_state_by_name(mysqli $db, $stateName)
{
    $stateName = city_import_normalize_state_name($stateName);
    if ($stateName === '') {
        return null;
    }

    $escaped = mysqli_real_escape_string($db, $stateName);
    $sql = "SELECT state_id, state_code, country_id, country_code
            FROM state
            WHERE state_name = '$escaped'
               OR state_name LIKE '$escaped%'
               OR '$escaped' LIKE CONCAT('%', state_name, '%')
            ORDER BY (state_name = '$escaped') DESC, LENGTH(state_name) ASC
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return $row;
    }

    return null;
}

function city_import_find_city_id(mysqli $db, $stateCode, $cityName)
{
    $stateCode = trim((string) $stateCode);
    $cityName = trim((string) $cityName);
    if ($stateCode === '' || $cityName === '') {
        return 0;
    }

    $stateEsc = mysqli_real_escape_string($db, $stateCode);
    $cityEsc = mysqli_real_escape_string($db, $cityName);
    $sql = "SELECT city_id FROM city
            WHERE state_code = '$stateEsc'
              AND (city_name = '$cityEsc' OR city_name LIKE '$cityEsc%')
            ORDER BY (city_name = '$cityEsc') DESC, LENGTH(city_name) ASC
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return (int) $row['city_id'];
    }

    return 0;
}

function city_import_detect_format(array $row)
{
    if (count($row) < 4) {
        return 'global';
    }

    $stateName = trim((string) ($row[1] ?? ''));
    $cityName = trim((string) ($row[3] ?? ''));
    $col0Numeric = (int) preg_replace('/[^\d]/', '', (string) ($row[0] ?? ''));

    // Temple export CSV: index_id, state_name, god, city_name, title, ...
    if (
        $col0Numeric > 0
        && $stateName !== ''
        && $cityName !== ''
        && preg_match('/[a-zA-Z]/', $stateName)
        && count($row) >= 6
    ) {
        return 'temple_cities';
    }

    // Legacy India: city_id,city_name,NULL,legacy_state_id
    if (count($row) <= 5) {
        $legacyStateId = (int) preg_replace('/[^\d]/', '', (string) ($row[3] ?? ''));
        $col2 = strtoupper(trim((string) ($row[2] ?? '')));
        if ($legacyStateId > 0 && ($col2 === '' || $col2 === 'NULL')) {
            return 'india';
        }
    }

    return 'global';
}

/**
 * Import unique cities from temple export CSV (indiacitys.csv): col1=state, col3=city.
 * Existing city (same state_code + city_name) is updated; otherwise inserted.
 */
function city_import_temple_cities_from_file(mysqli $db, $filePath)
{
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $byKey = [];
    $unmappedStates = [];

    $file = fopen($filePath, 'r');
    if (!$file) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'removed' => 0,
            'errors' => 1,
            'format' => 'temple_cities',
            'messages' => ['Unable to open CSV file.'],
            'csv_rows' => 0,
            'db_total' => 0,
        ];
    }

    while (($row = fgetcsv($file)) !== false) {
        if (count($row) < 4) {
            $skipped++;
            continue;
        }

        $stateName = abroad_import_clean_value($row[1] ?? '');
        $cityName = abroad_import_clean_value($row[3] ?? '');
        if ($stateName === '' || $cityName === '') {
            $skipped++;
            continue;
        }

        $key = strtolower($stateName) . '|' . strtolower($cityName);
        $byKey[$key] = [
            'state_name' => $stateName,
            'city_name' => $cityName,
        ];
    }
    fclose($file);

    $insertStmt = mysqli_prepare(
        $db,
        "INSERT INTO `city` (`city_name`, `state_id`, `country_id`, `country_code`, `state_code`, `status`)
         VALUES (?, ?, ?, ?, ?, 'APPROVED')"
    );
    $updateStmt = mysqli_prepare(
        $db,
        "UPDATE `city` SET `city_name` = ?, `state_id` = ?, `country_id` = ?, `country_code` = ?, `state_code` = ?, `status` = 'APPROVED'
         WHERE `city_id` = ?"
    );
    if (!$insertStmt || !$updateStmt) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => $skipped,
            'removed' => 0,
            'errors' => 1,
            'format' => 'temple_cities',
            'messages' => [mysqli_error($db)],
            'csv_rows' => count($byKey),
            'db_total' => 0,
        ];
    }

    mysqli_begin_transaction($db);
    foreach ($byKey as $data) {
        $state = city_import_lookup_state_by_name($db, $data['state_name']);
        if ($state === null) {
            $unmappedStates[$data['state_name']] = true;
            $skipped++;
            continue;
        }

        $stateId = (int) $state['state_id'];
        $stateCode = (string) $state['state_code'];
        $countryId = (int) $state['country_id'];
        $countryCode = (string) $state['country_code'];
        $cityName = $data['city_name'];

        $existingId = city_import_find_city_id($db, $stateCode, $cityName);
        if ($existingId > 0) {
            mysqli_stmt_bind_param(
                $updateStmt,
                'siissi',
                $cityName,
                $stateId,
                $countryId,
                $countryCode,
                $stateCode,
                $existingId
            );
            if (!mysqli_stmt_execute($updateStmt)) {
                $errors++;
                if (count($messages) < 8) {
                    $messages[] = $cityName . ' / ' . $data['state_name'] . ': ' . mysqli_stmt_error($updateStmt);
                }
                continue;
            }
            $updated++;
        } else {
            mysqli_stmt_bind_param(
                $insertStmt,
                'siiss',
                $cityName,
                $stateId,
                $countryId,
                $countryCode,
                $stateCode
            );
            if (!mysqli_stmt_execute($insertStmt)) {
                $errors++;
                if (count($messages) < 8) {
                    $messages[] = $cityName . ' / ' . $data['state_name'] . ': ' . mysqli_stmt_error($insertStmt);
                }
                continue;
            }
            $imported++;
        }
    }
    mysqli_stmt_close($insertStmt);
    mysqli_stmt_close($updateStmt);

    if ($errors > 0) {
        mysqli_rollback($db);
    } else {
        mysqli_commit($db);
    }

    if (!empty($unmappedStates)) {
        $names = array_keys($unmappedStates);
        sort($names);
        $messages[] = 'Unmapped state names (' . count($names) . '): ' . implode(', ', array_slice($names, 0, 15))
            . (count($names) > 15 ? '...' : '');
    }

    $dbTotal = 0;
    $countR = mysqli_query($db, 'SELECT COUNT(*) AS c FROM `city` WHERE `city_id` > 0');
    if ($countR && ($countRow = mysqli_fetch_assoc($countR))) {
        $dbTotal = (int) $countRow['c'];
    }

    return [
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'removed' => 0,
        'errors' => $errors,
        'format' => 'temple_cities',
        'messages' => $messages,
        'csv_rows' => count($byKey),
        'db_total' => $dbTotal,
    ];
}

/**
 * Resolve a temples.state value (state_code or state_id) to a state row.
 *
 * @return array{state_id:int,state_code:string,country_id:int,country_code:string}|null
 */
function city_import_resolve_state_for_temple(mysqli $db, $stateVal, $countryCode = 'IN')
{
    $stateVal = trim((string) $stateVal);
    if ($stateVal === '') {
        return null;
    }

    $countryEsc = mysqli_real_escape_string($db, $countryCode);
    $stateEsc = mysqli_real_escape_string($db, $stateVal);
    $sql = "SELECT state_id, state_code, country_id, country_code
            FROM state
            WHERE country_code = '$countryEsc'
              AND (state_code = '$stateEsc' OR CAST(state_id AS CHAR) = '$stateEsc')
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        return [
            'state_id' => (int) $row['state_id'],
            'state_code' => (string) $row['state_code'],
            'country_id' => (int) $row['country_id'],
            'country_code' => (string) $row['country_code'],
        ];
    }

    return null;
}

/**
 * Insert or update a city row; returns city_id or 0 on failure.
 *
 * @param array{state_id:int,state_code:string,country_id:int,country_code:string} $state
 */
function city_import_upsert_city(mysqli $db, array $state, $cityName)
{
    $cityName = trim((string) $cityName);
    if ($cityName === '') {
        return 0;
    }

    $stateCode = (string) $state['state_code'];
    $existingId = city_import_find_city_id($db, $stateCode, $cityName);
    if ($existingId > 0) {
        $stateId = (int) $state['state_id'];
        $countryId = (int) $state['country_id'];
        $countryCode = (string) $state['country_code'];
        $cityEsc = mysqli_real_escape_string($db, $cityName);
        mysqli_query(
            $db,
            "UPDATE `city`
             SET `city_name` = '$cityEsc',
                 `state_id` = $stateId,
                 `country_id` = $countryId,
                 `country_code` = '" . mysqli_real_escape_string($db, $countryCode) . "',
                 `state_code` = '" . mysqli_real_escape_string($db, $stateCode) . "',
                 `status` = 'APPROVED'
             WHERE `city_id` = $existingId"
        );

        return $existingId;
    }

    $stateId = (int) $state['state_id'];
    $countryId = (int) $state['country_id'];
    $countryCode = mysqli_real_escape_string($db, (string) $state['country_code']);
    $stateCodeEsc = mysqli_real_escape_string($db, $stateCode);
    $cityEsc = mysqli_real_escape_string($db, $cityName);
    $sql = "INSERT INTO `city` (`city_name`, `state_id`, `country_id`, `country_code`, `state_code`, `status`)
            VALUES ('$cityEsc', $stateId, $countryId, '$countryCode', '$stateCodeEsc', 'APPROVED')";
    if (!mysqli_query($db, $sql)) {
        return 0;
    }

    return (int) mysqli_insert_id($db);
}

/**
 * Link temples.city from city rows matching temple_place within a state.
 */
function city_import_link_temples_to_cities(mysqli $db, $countryCode = 'IN')
{
    $countryEsc = mysqli_real_escape_string($db, $countryCode);
    $linked = 0;
    $errors = 0;
    $messages = [];

    $sql = "SELECT DISTINCT TRIM(t.temple_place) AS city_name, t.state
            FROM temples t
            WHERE t.country = '$countryEsc'
              AND TRIM(COALESCE(t.temple_place, '')) != ''
              AND TRIM(COALESCE(t.state, '')) != ''";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        return ['linked' => 0, 'errors' => 1, 'messages' => [mysqli_error($db)]];
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $cityName = trim((string) ($row['city_name'] ?? ''));
        $stateVal = trim((string) ($row['state'] ?? ''));
        if ($cityName === '' || $stateVal === '') {
            continue;
        }

        $state = city_import_resolve_state_for_temple($db, $stateVal, $countryCode);
        if ($state === null) {
            continue;
        }

        $cityId = city_import_find_city_id($db, $state['state_code'], $cityName);
        if ($cityId <= 0) {
            continue;
        }

        $stateCodeEsc = mysqli_real_escape_string($db, $state['state_code']);
        $stateId = (int) $state['state_id'];
        $stateValEsc = mysqli_real_escape_string($db, $stateVal);
        $placeEsc = mysqli_real_escape_string($db, $cityName);
        $cityId = (int) $cityId;

        $linkSql = "UPDATE temples
            SET city = '$cityId'
            WHERE country = '$countryEsc'
              AND TRIM(temple_place) = '$placeEsc'
              AND (state = '$stateCodeEsc' OR state = '$stateId' OR state = '$stateValEsc')";

        if (!mysqli_query($db, $linkSql)) {
            $errors++;
            if (count($messages) < 8) {
                $messages[] = $cityName . ' / ' . $state['state_code'] . ': ' . mysqli_error($db);
            }
            continue;
        }
        $linked += mysqli_affected_rows($db);
    }

    return ['linked' => $linked, 'errors' => $errors, 'messages' => $messages];
}

/**
 * Copy distinct temples.temple_place values into `city` (per state) and link temples.city.
 */
function city_import_sync_from_temples(mysqli $db, $countryCode = 'IN')
{
    $countryEsc = mysqli_real_escape_string($db, $countryCode);
    $imported = 0;
    $updated = 0;
    $linked = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $unmappedStates = [];

    $sql = "SELECT DISTINCT TRIM(t.temple_place) AS city_name, t.state
            FROM temples t
            WHERE t.country = '$countryEsc'
              AND TRIM(COALESCE(t.temple_place, '')) != ''
              AND TRIM(COALESCE(t.state, '')) != ''
            ORDER BY t.state ASC, city_name ASC";
    $result = mysqli_query($db, $sql);
    if (!$result) {
        return [
            'imported' => 0,
            'updated' => 0,
            'linked' => 0,
            'skipped' => 0,
            'errors' => 1,
            'messages' => [mysqli_error($db)],
            'places' => 0,
        ];
    }

    $places = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cityName = trim((string) ($row['city_name'] ?? ''));
        $stateVal = trim((string) ($row['state'] ?? ''));
        if ($cityName === '' || $stateVal === '') {
            $skipped++;
            continue;
        }
        $state = city_import_resolve_state_for_temple($db, $stateVal, $countryCode);
        if ($state === null) {
            $unmappedStates[$stateVal] = true;
            $skipped++;
            continue;
        }
        $key = $state['state_code'] . '|' . strtolower($cityName);
        $places[$key] = [
            'city_name' => $cityName,
            'state' => $state,
            'state_val' => $stateVal,
        ];
    }

    $progress = 0;
    foreach ($places as $data) {
        $progress++;
        if ($progress % 100 === 0 && PHP_SAPI === 'cli') {
            echo "Processed {$progress}/" . count($places) . " places...\n";
        }

        $state = $data['state'];
        $cityName = $data['city_name'];
        $stateCode = $state['state_code'];
        $stateVal = $data['state_val'];

        $existingId = city_import_find_city_id($db, $stateCode, $cityName);
        if ($existingId <= 0) {
            $existingId = city_import_upsert_city($db, $state, $cityName);
            if ($existingId <= 0) {
                $errors++;
                if (count($messages) < 8) {
                    $messages[] = $cityName . ' / ' . $stateCode . ': unable to insert city';
                }
                continue;
            }
            $imported++;
        } else {
            $updated++;
        }

        $stateCodeEsc = mysqli_real_escape_string($db, $stateCode);
        $stateId = (int) $state['state_id'];
        $stateValEsc = mysqli_real_escape_string($db, $stateVal);
        $placeEsc = mysqli_real_escape_string($db, $cityName);
        $cityId = (int) $existingId;

        $linkSql = "UPDATE temples
            SET city = '$cityId'
            WHERE country = '$countryEsc'
              AND TRIM(temple_place) = '$placeEsc'
              AND (state = '$stateCodeEsc' OR state = '$stateId' OR state = '$stateValEsc')";
        if (!mysqli_query($db, $linkSql)) {
            $errors++;
            if (count($messages) < 8) {
                $messages[] = $cityName . ' / ' . $stateCode . ': ' . mysqli_error($db);
            }
            continue;
        }
        $linked += mysqli_affected_rows($db);
    }

    if (!empty($unmappedStates)) {
        $names = array_keys($unmappedStates);
        sort($names);
        $messages[] = 'Unmapped temple state values (' . count($names) . '): '
            . implode(', ', array_slice($names, 0, 15))
            . (count($names) > 15 ? '...' : '');
    }

    return [
        'imported' => $imported,
        'updated' => $updated,
        'linked' => $linked,
        'skipped' => $skipped,
        'errors' => $errors,
        'messages' => $messages,
        'places' => count($places),
    ];
}

function city_import_india_from_file(mysqli $db, $filePath, $removeOrphans = false)
{
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $byId = [];
    $unmappedStates = [];

    $file = fopen($filePath, 'r');
    if (!$file) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'removed' => 0,
            'errors' => 1,
            'format' => 'india',
            'messages' => ['Unable to open CSV file.'],
            'csv_rows' => 0,
            'db_total' => 0,
        ];
    }

    $stateIndex = city_import_load_india_states($db);
    $defaultCountryId = 0;
    $defaultCountryCode = 'IN';
    $countryR = mysqli_query($db, "SELECT country_id, country_code FROM country WHERE country_code = 'IN' ORDER BY country_id ASC LIMIT 1");
    if ($countryR && ($countryRow = mysqli_fetch_assoc($countryR))) {
        $defaultCountryId = (int) $countryRow['country_id'];
        $defaultCountryCode = $countryRow['country_code'];
    }

    while (($row = fgetcsv($file)) !== false) {
        if (count($row) < 4) {
            $skipped++;
            continue;
        }

        $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
        $cityName = abroad_import_clean_value($row[1] ?? '');
        $legacyStateId = (int) preg_replace('/[^\d]/', '', (string) ($row[3] ?? ''));

        if ($legacyId <= 0 || $cityName === '' || $legacyStateId <= 0) {
            $skipped++;
            continue;
        }

        $stateCode = city_import_india_resolve_state_code($legacyStateId, $cityName);
        if ($stateCode === '' || !isset($stateIndex[$stateCode])) {
            $unmappedStates[$legacyStateId] = true;
            $skipped++;
            continue;
        }

        $state = $stateIndex[$stateCode];
        $byId[$legacyId] = [
            'city_name' => $cityName,
            'state_id' => (int) $state['state_id'],
            'country_id' => (int) ($state['country_id'] ?: $defaultCountryId),
            'country_code' => $state['country_code'] !== '' ? $state['country_code'] : $defaultCountryCode,
            'state_code' => $stateCode,
        ];
    }
    fclose($file);

    if (!empty($unmappedStates)) {
        $messages[] = 'Skipped rows with unmapped legacy state id(s): ' . implode(', ', array_keys($unmappedStates));
    }

    $stmt = mysqli_prepare(
        $db,
        "INSERT INTO `city` (`city_id`, `city_name`, `state_id`, `country_id`, `country_code`, `state_code`, `status`)
         VALUES (?, ?, ?, ?, ?, ?, 'APPROVED')
         ON DUPLICATE KEY UPDATE
            `city_name` = VALUES(`city_name`),
            `state_id` = VALUES(`state_id`),
            `country_id` = VALUES(`country_id`),
            `country_code` = VALUES(`country_code`),
            `state_code` = VALUES(`state_code`),
            `status` = VALUES(`status`)"
    );
    if (!$stmt) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => $skipped,
            'removed' => 0,
            'errors' => 1,
            'format' => 'india',
            'messages' => array_merge($messages, [mysqli_error($db)]),
            'csv_rows' => count($byId),
            'db_total' => 0,
        ];
    }

    mysqli_begin_transaction($db);
    foreach ($byId as $legacyId => $data) {
        mysqli_stmt_bind_param(
            $stmt,
            'isiiss',
            $legacyId,
            $data['city_name'],
            $data['state_id'],
            $data['country_id'],
            $data['country_code'],
            $data['state_code']
        );
        if (!mysqli_stmt_execute($stmt)) {
            $errors++;
            if (count($messages) < 8) {
                $messages[] = 'City ID ' . $legacyId . ': ' . mysqli_stmt_error($stmt);
            }
            continue;
        }
        $affected = mysqli_stmt_affected_rows($stmt);
        if ($affected === 1) {
            $imported++;
        } elseif ($affected === 2) {
            $updated++;
        }
    }
    mysqli_stmt_close($stmt);

    if ($errors > 0) {
        mysqli_rollback($db);
    } else {
        $maxId = !empty($byId) ? max(array_keys($byId)) : 0;
        if ($maxId > 0) {
            $auto = (int) mysqli_fetch_assoc(mysqli_query($db, 'SELECT MAX(city_id) AS m FROM city'))['m'];
            mysqli_query($db, 'ALTER TABLE `city` AUTO_INCREMENT = ' . (max($maxId, $auto) + 1));
        }
        mysqli_commit($db);
    }

    $removed = 0;
    if (!$errors && $removeOrphans && !empty($byId)) {
        $r = mysqli_query($db, "SELECT `city_id` FROM `city` WHERE `city_id` > 0 AND `country_code` = 'IN'");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $id = (int) $row['city_id'];
                if (!isset($byId[$id])) {
                    if (mysqli_query($db, 'DELETE FROM `city` WHERE `city_id` = ' . $id)) {
                        $removed += mysqli_affected_rows($db);
                    }
                }
            }
        }
    }

    $dbTotal = 0;
    $countR = mysqli_query($db, "SELECT COUNT(*) AS c FROM `city` WHERE `city_id` > 0");
    if ($countR && ($countRow = mysqli_fetch_assoc($countR))) {
        $dbTotal = (int) $countRow['c'];
    }

    return [
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'removed' => $removed,
        'errors' => $errors,
        'format' => 'india',
        'messages' => $messages,
        'csv_rows' => count($byId),
        'db_total' => $dbTotal,
    ];
}

/**
 * Import legacy global city export: city_id,city_name (no header).
 */
function city_import_global_from_file(mysqli $db, $filePath, $removeOrphans = false)
{
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;
    $messages = [];
    $byId = [];

    $file = fopen($filePath, 'r');
    if (!$file) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'removed' => 0,
            'errors' => 1,
            'format' => 'global',
            'messages' => ['Unable to open CSV file.'],
            'csv_rows' => 0,
            'db_total' => 0,
        ];
    }

    while (($row = fgetcsv($file)) !== false) {
        if (count($row) < 2) {
            $skipped++;
            continue;
        }
        $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
        $cityName = abroad_import_clean_value($row[1] ?? '');
        if ($legacyId <= 0 || $cityName === '') {
            $skipped++;
            continue;
        }
        $byId[$legacyId] = $cityName;
    }
    fclose($file);

    $stmt = mysqli_prepare(
        $db,
        "INSERT INTO `city` (`city_id`, `city_name`, `state_id`, `country_id`, `country_code`, `state_code`, `status`)
         VALUES (?, ?, 0, 0, '', '', 'APPROVED')
         ON DUPLICATE KEY UPDATE
            `city_name` = VALUES(`city_name`),
            `state_id` = VALUES(`state_id`),
            `country_id` = VALUES(`country_id`),
            `country_code` = VALUES(`country_code`),
            `state_code` = VALUES(`state_code`),
            `status` = VALUES(`status`)"
    );
    if (!$stmt) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => $skipped,
            'removed' => 0,
            'errors' => 1,
            'format' => 'global',
            'messages' => [mysqli_error($db)],
            'csv_rows' => count($byId),
            'db_total' => 0,
        ];
    }

    mysqli_begin_transaction($db);
    foreach ($byId as $legacyId => $cityName) {
        mysqli_stmt_bind_param($stmt, 'is', $legacyId, $cityName);
        if (!mysqli_stmt_execute($stmt)) {
            $errors++;
            if (count($messages) < 5) {
                $messages[] = 'City ID ' . $legacyId . ': ' . mysqli_stmt_error($stmt);
            }
            continue;
        }
        $affected = mysqli_stmt_affected_rows($stmt);
        if ($affected === 1) {
            $imported++;
        } elseif ($affected === 2) {
            $updated++;
        }
    }
    mysqli_stmt_close($stmt);

    if ($errors > 0) {
        mysqli_rollback($db);
    } else {
        $maxId = !empty($byId) ? max(array_keys($byId)) : 0;
        if ($maxId > 0) {
            $auto = (int) mysqli_fetch_assoc(mysqli_query($db, 'SELECT MAX(city_id) AS m FROM city'))['m'];
            mysqli_query($db, 'ALTER TABLE `city` AUTO_INCREMENT = ' . (max($maxId, $auto) + 1));
        }
        mysqli_commit($db);
    }

    $removed = 0;
    if (!$errors && $removeOrphans && !empty($byId)) {
        $r = mysqli_query($db, "SELECT `city_id` FROM `city` WHERE `city_id` > 0");
        if ($r) {
            while ($row = mysqli_fetch_assoc($r)) {
                $id = (int) $row['city_id'];
                if (!isset($byId[$id])) {
                    if (mysqli_query($db, 'DELETE FROM `city` WHERE `city_id` = ' . $id)) {
                        $removed += mysqli_affected_rows($db);
                    }
                }
            }
        }
    }

    $dbTotal = 0;
    $countR = mysqli_query($db, "SELECT COUNT(*) AS c FROM `city` WHERE `city_id` > 0");
    if ($countR && ($countRow = mysqli_fetch_assoc($countR))) {
        $dbTotal = (int) $countRow['c'];
    }

    return [
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'removed' => $removed,
        'errors' => $errors,
        'format' => 'global',
        'messages' => $messages,
        'csv_rows' => count($byId),
        'db_total' => $dbTotal,
    ];
}

function city_import_from_file(mysqli $db, $filePath, $removeOrphans = false)
{
    $file = fopen($filePath, 'r');
    if (!$file) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'removed' => 0,
            'errors' => 1,
            'format' => 'unknown',
            'messages' => ['Unable to open CSV file.'],
            'csv_rows' => 0,
            'db_total' => 0,
        ];
    }

    $firstRow = fgetcsv($file);
    fclose($file);

    if ($firstRow === false) {
        return [
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'removed' => 0,
            'errors' => 1,
            'format' => 'unknown',
            'messages' => ['CSV file is empty.'],
            'csv_rows' => 0,
            'db_total' => 0,
        ];
    }

    $format = city_import_detect_format($firstRow);
    if ($format === 'india') {
        return city_import_india_from_file($db, $filePath, $removeOrphans);
    }
    if ($format === 'temple_cities') {
        return city_import_temple_cities_from_file($db, $filePath);
    }

    return city_import_global_from_file($db, $filePath, $removeOrphans);
}
