<?php
/**
 * Full CSV reconcile — counts should match legacy Temple.aspx / india_other export.
 * CLI: php reconcile-temple-cli.php "C:\path\to\Results-temple.csv"
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php reconcile-temple-cli.php <path-to-csv>\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/temple_import.php';

$db = new DatabaseConn();
$summary = temple_import_reconcile_from_file($db->dbLink, $csvPath, true);

echo "=== Reconcile complete ===\n";
echo 'Inserted: ' . (int) ($summary['imported'] ?? 0) . "\n";
echo 'Updated: ' . (int) ($summary['updated'] ?? 0) . "\n";
echo 'Skipped (no title): ' . (int) ($summary['skipped'] ?? 0) . "\n";
echo 'Removed (not in CSV): ' . (int) ($summary['removed'] ?? 0) . "\n";
echo 'Duplicate order_by removed: ' . (int) ($summary['dupes_removed'] ?? 0) . "\n";
echo 'God IDs backfilled: ' . (int) ($summary['gods_filled'] ?? 0) . "\n";
echo 'Errors: ' . (int) ($summary['errors'] ?? 0) . "\n";

$exp = $summary['csv_expected'] ?? [];
echo "\nCSV expected (Temple.aspx):\n";
echo '  Approved: ' . (int) ($exp['approved'] ?? 0) . "\n";
echo '  Approval Pending: ' . (int) ($exp['pending'] ?? 0) . "\n";
echo '  New: ' . (int) ($exp['new'] ?? 0) . "\n";
echo '  Rejected: ' . (int) ($exp['rejected'] ?? 0) . "\n";

$db = $summary['db_counts'] ?? [];
echo "\nDatabase now:\n";
echo '  Approved: ' . (int) ($db['approved'] ?? 0) . "\n";
echo '  Approval Pending (unapproved): ' . (int) ($db['unapproved'] ?? 0) . "\n";
echo '  New: ' . (int) ($db['new'] ?? 0) . "\n";
echo '  Rejected: ' . (int) ($db['rejected'] ?? 0) . "\n";
echo '  Total: ' . (int) ($db['total'] ?? 0) . "\n";

$csvRows = (int) ($summary['csv_rows'] ?? 0);
$match = (
    (int) ($exp['approved'] ?? 0) === (int) ($db['approved'] ?? 0)
    && (int) ($exp['pending'] ?? 0) === (int) ($db['unapproved'] ?? 0)
    && (int) ($exp['new'] ?? 0) === (int) ($db['new'] ?? 0)
    && (int) ($exp['rejected'] ?? 0) === (int) ($db['rejected'] ?? 0)
    && $csvRows === (int) ($db['total'] ?? 0)
);
echo $match ? "\nCounts MATCH CSV exactly.\n" : "\nCounts still differ — check errors above.\n";

if (!empty($summary['messages'])) {
    foreach ($summary['messages'] as $msg) {
        echo $msg . "\n";
    }
}

exit($match && (int) ($summary['errors'] ?? 0) === 0 ? 0 : 1);
