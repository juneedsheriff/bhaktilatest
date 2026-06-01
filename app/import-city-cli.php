<?php
/**
 * Import cities from legacy CSV (city_id,city_name).
 * CLI: php import-city-cli.php "C:\path\to\cityResults.csv"
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php import-city-cli.php <path-to-cityResults.csv>\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/city_import.php';

$db = new DatabaseConn();
$summary = city_import_from_file($db->dbLink, $csvPath, true);

echo "=== City import complete ===\n";
echo 'CSV rows: ' . (int) ($summary['csv_rows'] ?? 0) . "\n";
echo 'Inserted: ' . (int) ($summary['imported'] ?? 0) . "\n";
echo 'Updated: ' . (int) ($summary['updated'] ?? 0) . "\n";
echo 'Skipped: ' . (int) ($summary['skipped'] ?? 0) . "\n";
echo 'Format: ' . ($summary['format'] ?? 'unknown') . "\n";
echo 'Removed (not in CSV): ' . (int) ($summary['removed'] ?? 0) . "\n";
echo 'Skipped: ' . (int) ($summary['skipped'] ?? 0) . "\n";
echo 'Errors: ' . (int) ($summary['errors'] ?? 0) . "\n";
echo 'Database total: ' . (int) ($summary['db_total'] ?? 0) . "\n";

if (!empty($summary['messages'])) {
    foreach ($summary['messages'] as $msg) {
        echo $msg . "\n";
    }
}

exit((int) ($summary['errors'] ?? 0) === 0 ? 0 : 1);
