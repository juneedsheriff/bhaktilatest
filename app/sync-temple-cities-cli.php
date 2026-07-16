<?php
/**
 * Sync city table from temples.temple_place and link temples.city.
 * CLI: php sync-temple-cities-cli.php [country_code]
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$countryCode = isset($argv[1]) && $argv[1] !== '' ? $argv[1] : 'IN';

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/city_import.php';

$db = new DatabaseConn();
if (!$db->isConnected()) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

mysqli_report(MYSQLI_REPORT_OFF);
$summary = city_import_sync_from_temples($db->dbLink, $countryCode);

echo "=== Temple place -> city sync ({$countryCode}) ===\n";
echo 'Distinct places: ' . (int) ($summary['places'] ?? 0) . "\n";
echo 'Cities inserted: ' . (int) ($summary['imported'] ?? 0) . "\n";
echo 'Cities updated: ' . (int) ($summary['updated'] ?? 0) . "\n";
echo 'Temple rows linked: ' . (int) ($summary['linked'] ?? 0) . "\n";
echo 'Skipped: ' . (int) ($summary['skipped'] ?? 0) . "\n";
echo 'Errors: ' . (int) ($summary['errors'] ?? 0) . "\n";

if (!empty($summary['messages'])) {
    foreach ($summary['messages'] as $msg) {
        echo $msg . "\n";
    }
}

$link = $db->dbLink;
$withCity = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) c FROM temples WHERE country='" . mysqli_real_escape_string($link, $countryCode) . "' AND TRIM(COALESCE(temple_place,'')) != '' AND TRIM(COALESCE(city,'')) != ''"));
$totalPlaces = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) c FROM temples WHERE country='" . mysqli_real_escape_string($link, $countryCode) . "' AND TRIM(COALESCE(temple_place,'')) != ''"));
echo 'Temples with temple_place linked to city: ' . (int) ($withCity['c'] ?? 0) . ' / ' . (int) ($totalPlaces['c'] ?? 0) . "\n";

exit((int) ($summary['errors'] ?? 0) === 0 ? 0 : 1);
