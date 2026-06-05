<?php
/**
 * Compare mountain.csv Approved count vs other_page page_id=8.
 */
$csvPath = $argv[1] ?? 'c:/Users/JuneedShareef/Documents/mountain.csv';
$pageId = $argv[2] ?? '8';

require_once __DIR__ . '/../class/databaseConn.php';
require_once __DIR__ . '/../includes/other_page_import.php';

$f = fopen($csvPath, 'rb');
$csvApproved = 0;
$csvPending = 0;
$csvOther = 0;
$csvTotal = 0;
$csvApprovedTitles = [];
$csvDupTitles = [];

while (($row = fgetcsv($f)) !== false) {
    if (count($row) < 7) {
        continue;
    }
    $csvTotal++;
    $title = other_page_import_trim_field(other_page_import_sanitize_text($row[1] ?? ''), 100);
    $status = strtolower(trim(other_page_import_sanitize_text($row[6] ?? '')));
    if ($status === 'approved') {
        $csvApproved++;
        $key = other_page_import_normalize_title($title);
        if (isset($csvApprovedTitles[$key])) {
            $csvDupTitles[$key] = ($csvDupTitles[$key] ?? 1) + 1;
        }
        $csvApprovedTitles[$key] = $title;
    } elseif ($status === 'pending' || $status === 'unapproved') {
        $csvPending++;
    } else {
        $csvOther++;
        echo "Unknown status line $csvTotal: [$status] $title\n";
    }
}
fclose($f);

$db = new DatabaseConn();
mysqli_set_charset($db->dbLink, 'utf8');
$pageEsc = mysqli_real_escape_string($db->dbLink, $pageId);

$r = mysqli_query($db->dbLink, "SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN LOWER(TRIM(status)) = 'approved' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN LOWER(TRIM(status)) = 'unapproved' THEN 1 ELSE 0 END) AS unapproved
    FROM other_page WHERE page_id = '$pageEsc'");
$dbCounts = mysqli_fetch_assoc($r);

echo "=== CSV ($csvPath) ===\n";
echo "Total rows: $csvTotal\n";
echo "Approved: $csvApproved\n";
echo "Pending/unapproved: $csvPending\n";
echo "Other status: $csvOther\n";
echo "Unique approved titles: " . count($csvApprovedTitles) . "\n";
if ($csvDupTitles) {
    echo "Duplicate approved titles in CSV:\n";
    foreach ($csvDupTitles as $key => $count) {
        echo "  - {$csvApprovedTitles[$key]} (appears " . ($count + 1) . " times)\n";
    }
}

echo "\n=== DB other_page page_id=$pageId ===\n";
echo "Total: {$dbCounts['total']}\n";
echo "Approved: {$dbCounts['approved']}\n";
echo "Unapproved: {$dbCounts['unapproved']}\n";

// DB approved not matching any CSV approved title (pre-import leftovers)
$res = mysqli_query($db->dbLink, "SELECT index_id, title, status FROM other_page WHERE page_id = '$pageEsc' AND LOWER(TRIM(status)) = 'approved' ORDER BY title");
$dbOnly = [];
$csvKeys = array_keys($csvApprovedTitles);
while ($row = mysqli_fetch_assoc($res)) {
    $key = other_page_import_normalize_title($row['title']);
    if (!in_array($key, $csvKeys, true)) {
        $dbOnly[] = $row;
    }
}

if ($dbOnly) {
    echo "\nDB approved rows NOT in CSV (likely pre-existing before import):\n";
    foreach ($dbOnly as $row) {
        echo "  index_id={$row['index_id']} | {$row['title']}\n";
    }
}

// CSV approved titles that lost to duplicate (last update wins in DB - show duplicates)
echo "\n=== All CSV rows with Approved status ===\n";
$f = fopen($csvPath, 'rb');
$line = 0;
while (($row = fgetcsv($f)) !== false) {
    $line++;
    if (count($row) < 7) {
        continue;
    }
    $status = strtolower(trim(other_page_import_sanitize_text($row[6] ?? '')));
    if ($status !== 'approved') {
        continue;
    }
    $legacy = trim(other_page_import_sanitize_text($row[0] ?? ''));
    $title = other_page_import_trim_field(other_page_import_sanitize_text($row[1] ?? ''), 100);
    echo "  legacy_id=$legacy | $title\n";
}
fclose($f);
