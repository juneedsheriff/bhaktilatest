<?php
/**
 * CLI: php add-temple-image-columns-cli.php
 * Adds image1..image10 columns to temples table if missing.
 */

require_once __DIR__ . '/class/databaseConn.php';

$db = (new DatabaseConn())->dbLink;
if (!$db) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

$existing = [];
$res = mysqli_query($db, 'SHOW COLUMNS FROM temples');
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $existing[(string) ($row['Field'] ?? '')] = true;
    }
}

$added = [];
$errors = [];
for ($i = 1; $i <= 10; $i++) {
    $col = 'image' . $i;
    if (isset($existing[$col])) {
        continue;
    }
    $sql = "ALTER TABLE temples ADD COLUMN `$col` VARCHAR(255) NULL DEFAULT NULL";
    if (!mysqli_query($db, $sql)) {
        $errors[] = $col . ': ' . mysqli_error($db);
        continue;
    }
    $added[] = $col;
}

if (!empty($added)) {
    echo "Added columns: " . implode(', ', $added) . PHP_EOL;
} else {
    echo "No new image columns needed." . PHP_EOL;
}

if (!empty($errors)) {
    echo "Errors:\n";
    foreach ($errors as $err) {
        echo "- " . $err . PHP_EOL;
    }
    exit(1);
}

echo "Done." . PHP_EOL;
