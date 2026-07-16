<?php
/**
 * CLI: php sync-temple-image-columns-from-csv-cli.php "C:\path\to\Results-temple.csv"
 * Overwrites temples.image1..image10 from exact CSV columns:
 * [10..14] -> image1..image5 and [21..25] -> image6..image10
 */

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "CLI only\n");
    exit(1);
}

require_once __DIR__ . '/class/databaseConn.php';
require_once __DIR__ . '/includes/abroad_import.php';

$csvPath = $argv[1] ?? 'C:/Users/JuneedShareef/Documents/Results-temple.csv';
if (!is_readable($csvPath)) {
    fwrite(STDERR, "CSV not readable: {$csvPath}\n");
    exit(1);
}

$db = (new DatabaseConn())->dbLink;
if (!$db) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

$file = fopen($csvPath, 'r');
if (!$file) {
    fwrite(STDERR, "Unable to open CSV file.\n");
    exit(1);
}

$updated = 0;
$notMatched = 0;
$skipped = 0;
$errors = 0;

while (($row = fgetcsv($file)) !== false) {
    if (!is_array($row) || count($row) < 26) {
        $skipped++;
        continue;
    }

    $legacyId = (int) preg_replace('/[^\d]/', '', abroad_import_strip_bom($row[0] ?? ''));
    if ($legacyId <= 0) {
        $skipped++;
        continue;
    }

    $images = [
        'image1' => abroad_import_clean_value($row[10] ?? ''),
        'image2' => abroad_import_clean_value($row[11] ?? ''),
        'image3' => abroad_import_clean_value($row[12] ?? ''),
        'image4' => abroad_import_clean_value($row[13] ?? ''),
        'image5' => abroad_import_clean_value($row[14] ?? ''),
        'image6' => abroad_import_clean_value($row[21] ?? ''),
        'image7' => abroad_import_clean_value($row[22] ?? ''),
        'image8' => abroad_import_clean_value($row[23] ?? ''),
        'image9' => abroad_import_clean_value($row[24] ?? ''),
        'image10' => abroad_import_clean_value($row[25] ?? ''),
    ];

    $set = [];
    foreach ($images as $field => $value) {
        $set[] = "`{$field}`='" . mysqli_real_escape_string($db, $value) . "'";
    }

    $where = "(order_by = {$legacyId} OR index_id = {$legacyId})";
    $sql = "UPDATE temples SET " . implode(', ', $set) . " WHERE {$where}";
    if (!mysqli_query($db, $sql)) {
        $errors++;
        continue;
    }

    $affected = mysqli_affected_rows($db);
    if ($affected > 0) {
        $updated += $affected;
    } else {
        $notMatched++;
    }
}

fclose($file);

echo "CSV: {$csvPath}\n";
echo "Updated rows: {$updated}\n";
echo "Not matched by legacy id: {$notMatched}\n";
echo "Skipped rows: {$skipped}\n";
echo "Errors: {$errors}\n";

exit($errors > 0 ? 1 : 0);
