<?php
/**
 * CLI: php import-other-page-cli.php "C:\path\to\tree.csv" [page_id]
 *
 * Default page_id 7 = Sacred Trees (saints.php?id=7).
 * page_id 6 = Hindu Ashrams, 8 = Sacred Mountains, 9 = Vahana Gods, 10 = Alwars.
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
$pageId = $argv[2] ?? '7';

if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php import-other-page-cli.php <path-to-csv> [page_id]\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/other_page_import.php';

$db = new DatabaseConn();
$summary = other_page_import_from_file($db->dbLink, $csvPath, $pageId);

echo 'Page ID: ' . $pageId . PHP_EOL;
echo 'Inserted: ' . (int) ($summary['imported'] ?? 0) . PHP_EOL;
echo 'Updated: ' . (int) ($summary['updated'] ?? 0) . PHP_EOL;
echo 'Skipped: ' . (int) ($summary['skipped'] ?? 0) . PHP_EOL;
echo 'Errors: ' . (int) ($summary['errors'] ?? 0) . PHP_EOL;

if (!empty($summary['messages'])) {
    foreach ($summary['messages'] as $msg) {
        echo $msg . PHP_EOL;
    }
}

$pageEsc = mysqli_real_escape_string($db->dbLink, (string) $pageId);
$counts = mysqli_fetch_assoc(mysqli_query(
    $db->dbLink,
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN LOWER(TRIM(status)) = 'approved' THEN 1 ELSE 0 END) AS approved
     FROM other_page WHERE page_id = '$pageEsc'"
));
echo 'Rows for page_id ' . $pageId . ': ' . (int) ($counts['total'] ?? 0)
    . ' (approved: ' . (int) ($counts['approved'] ?? 0) . ')' . PHP_EOL;

exit(($summary['errors'] ?? 0) > 0 ? 1 : 0);
