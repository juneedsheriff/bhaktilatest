<?php
/**
 * Sync temples.status from legacy CSV column 19 (Approved / New / Pending / etc.)
 * CLI: php sync_temple_status_from_csv.php "C:\path\to\Results-temple.csv"
 */
if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

$csvPath = $argv[1] ?? '';
if ($csvPath === '' || !is_readable($csvPath)) {
    fwrite(STDERR, "Usage: php sync_temple_status_from_csv.php <path-to-csv>\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/temple_import.php';

$db = new DatabaseConn();
$link = $db->dbLink;

$file = fopen($csvPath, 'r');
if (!$file) {
    fwrite(STDERR, "Cannot open CSV\n");
    exit(1);
}

$updated = 0;
$notFound = 0;
$line = 0;

while (($row = fgetcsv($file)) !== false) {
    $line++;
    if (count($row) < 20) {
        continue;
    }
    if ($line === 1 && temple_import_detect_format($row) === 'sample') {
        continue;
    }

    $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
    if ($legacyId <= 0) {
        continue;
    }

    $status = temple_import_normalize_status($row[19] ?? '');
    $statusEsc = mysqli_real_escape_string($link, $status);

    $sql = "UPDATE temples SET status='$statusEsc'
            WHERE order_by=$legacyId OR index_id=$legacyId";
    if (mysqli_query($link, $sql) && mysqli_affected_rows($link) > 0) {
        $updated++;
    } else {
        $notFound++;
    }
}

fclose($file);

$approved = (int) mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS c FROM temples WHERE status='approved'"))['c'];
$pending = (int) mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS c FROM temples WHERE status='unapproved'"))['c'];
$new = (int) mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS c FROM temples WHERE status='new'"))['c'];
$rejected = (int) mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS c FROM temples WHERE LOWER(TRIM(COALESCE(status,''))) IN ('rejected','reject','denied')"))['c'];

echo "Rows processed from CSV: " . ($line - 1) . PHP_EOL;
echo "Temples updated: $updated" . PHP_EOL;
echo "Not matched in DB: $notFound" . PHP_EOL;
echo "Approved (public listing): $approved" . PHP_EOL;
echo "Approval pending (CSV Pending): $pending" . PHP_EOL;
echo "New submissions (CSV New): $new" . PHP_EOL;
echo "Rejected: $rejected" . PHP_EOL;
