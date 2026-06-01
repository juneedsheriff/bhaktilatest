<?php
/**
 * CLI: php import-temple-cli.php "C:\path\to\Results-temple.csv"
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php import-temple-cli.php <path-to-csv>\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/temple_import.php';

$db = new DatabaseConn();
$summary = temple_import_from_file($db->dbLink, $csvPath);

echo "Format: " . ($summary['format'] ?? '') . PHP_EOL;
echo "Inserted: " . (int) ($summary['imported'] ?? 0) . PHP_EOL;
echo "Updated: " . (int) ($summary['updated'] ?? 0) . PHP_EOL;
echo "Skipped: " . (int) ($summary['skipped'] ?? 0) . PHP_EOL;
echo "Errors: " . (int) ($summary['errors'] ?? 0) . PHP_EOL;
if (!empty($summary['messages'])) {
    foreach ($summary['messages'] as $msg) {
        echo $msg . PHP_EOL;
    }
}

$c = mysqli_fetch_assoc(mysqli_query($db->dbLink, "SELECT COUNT(*) AS c FROM temples WHERE status='approved'"));
echo "Approved temples in DB: " . (int) $c['c'] . PHP_EOL;

exit(($summary['errors'] ?? 0) > 0 ? 1 : 0);
