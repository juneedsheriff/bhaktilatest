<?php
/**
 * Fix temples with empty state/city from legacy CSV export.
 * CLI: php fix-temple-state-from-csv-cli.php "C:\path\to\Results-temple.csv"
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php fix-temple-state-from-csv-cli.php <path-to-Results-temple.csv>\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/temple_import.php';
require_once __DIR__ . '/includes/city_import.php';

mysqli_report(MYSQLI_REPORT_OFF);
$db = new DatabaseConn();
$link = $db->dbLink;
$fixed = 0;
$skipped = 0;
$errors = 0;

$file = fopen($csvPath, 'r');
while (($row = fgetcsv($file)) !== false) {
    if (temple_import_detect_format($row) === 'sample') {
        continue;
    }

    $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
    if ($legacyId <= 0) {
        continue;
    }

    $check = mysqli_query(
        $link,
        "SELECT index_id FROM temples
         WHERE country = 'IN'
           AND (index_id = $legacyId OR order_by = $legacyId)
           AND TRIM(COALESCE(state, '')) = ''
         LIMIT 1"
    );
    if (!$check || mysqli_num_rows($check) === 0) {
        $skipped++;
        continue;
    }

    $stateName = abroad_import_clean_value($row[1] ?? '');
    $placeName = abroad_import_clean_value($row[3] ?? '');
    $stateRow = city_import_lookup_state_by_name($link, $stateName);
    if ($stateRow === null || $placeName === '') {
        $skipped++;
        continue;
    }

    $cityId = city_import_find_city_id($link, $stateRow['state_code'], $placeName);
    if ($cityId <= 0) {
        $cityId = city_import_upsert_city($link, $stateRow, $placeName);
    }
    if ($cityId <= 0) {
        $errors++;
        continue;
    }

    $stateEsc = mysqli_real_escape_string($link, $stateRow['state_code']);
    $cityEsc = (int) $cityId;
    $sql = "UPDATE temples SET state = '$stateEsc', city = '$cityEsc'
            WHERE country = 'IN'
              AND (index_id = $legacyId OR order_by = $legacyId)
              AND TRIM(COALESCE(state, '')) = ''";
    if (mysqli_query($link, $sql) && mysqli_affected_rows($link) > 0) {
        $fixed++;
    } else {
        $errors++;
    }
}
fclose($file);

echo "Fixed temples from CSV: $fixed\n";
echo "Skipped: $skipped\n";
echo "Errors: $errors\n";

$withCity = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) c FROM temples WHERE country='IN' AND TRIM(COALESCE(temple_place,'')) != '' AND TRIM(COALESCE(city,'')) != '' AND city != '0'"));
$totalPlaces = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) c FROM temples WHERE country='IN' AND TRIM(COALESCE(temple_place,'')) != ''"));
echo 'Temples linked to city: ' . (int) ($withCity['c'] ?? 0) . ' / ' . (int) ($totalPlaces['c'] ?? 0) . "\n";

exit($errors > 0 ? 1 : 0);
