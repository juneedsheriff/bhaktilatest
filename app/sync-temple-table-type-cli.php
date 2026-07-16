<?php
/**
 * CLI: populate temples.table_type from CSV column S.
 * Usage: php sync-temple-table-type-cli.php [path-to-csv]
 */
$csvFile = $argv[1] ?? 'C:/Users/JuneedShareef/Documents/Results-temple.csv';

include_once __DIR__ . '/class/databaseConn.php';
include_once __DIR__ . '/includes/temple_listing_query.php';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;
if (!$db) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

if (!is_readable($csvFile)) {
    fwrite(STDERR, "CSV not readable: $csvFile\n");
    exit(1);
}

$colCheck = mysqli_query($db, "SHOW COLUMNS FROM temples LIKE 'table_type'");
if (mysqli_num_rows($colCheck) === 0) {
    if (!mysqli_query($db, "ALTER TABLE `temples` ADD COLUMN `table_type` VARCHAR(100) DEFAULT NULL AFTER `speciality_title`")) {
        fwrite(STDERR, 'Failed to add table_type: ' . mysqli_error($db) . PHP_EOL);
        exit(1);
    }
    echo "Added column table_type.\n";
} else {
    echo "Column table_type already exists.\n";
}

$file = fopen($csvFile, 'r');
$updated = 0;
$skipped = 0;
$notFound = 0;

while (($row = fgetcsv($file)) !== false) {
    $legacyId = (int) ltrim(trim($row[0] ?? ''), "\xEF\xBB\xBF");
    $state = trim($row[1] ?? '');
    $place = trim($row[3] ?? '');
    $title = trim($row[4] ?? '');
    $csvType = trim($row[18] ?? '');

    $tableType = temple_table_type_normalize($csvType);
    if ($tableType === null) {
        $skipped++;
        continue;
    }

    $templeId = temple_table_type_find_temple_id($db, $legacyId, $title, $state, $place);
    if ($templeId <= 0) {
        $notFound++;
        continue;
    }

    $escaped = mysqli_real_escape_string($db, $tableType);
    $sql = "UPDATE `temples` SET `table_type` = '$escaped' WHERE `index_id` = $templeId";
    if (mysqli_query($db, $sql)) {
        $updated++;
    } else {
        fwrite(STDERR, "Update failed for temple $templeId: " . mysqli_error($db) . PHP_EOL);
    }
}
fclose($file);

echo "Updated: $updated | Skipped: $skipped | Not found: $notFound\n";

$res = mysqli_query($db, "SELECT table_type, COUNT(*) AS cnt FROM temples WHERE table_type IS NOT NULL AND table_type != '' GROUP BY table_type ORDER BY cnt DESC");
echo "Distinct table_type values in DB:\n";
while ($r = mysqli_fetch_assoc($res)) {
    echo "  [{$r['table_type']}] => {$r['cnt']}\n";
}
