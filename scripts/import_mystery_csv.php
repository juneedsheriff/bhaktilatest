<?php

/**
 * One-time import: mystertemple.csv -> mystery table.
 * Run: php scripts/import_mystery_csv.php
 */

require_once __DIR__ . '/../app/class/databaseConn.php';
require_once __DIR__ . '/../include/mystery_table_helpers.php';

$db = new DatabaseConn();
$result = mystery_table_import_csv($db->dbLink);

echo 'Import complete.' . PHP_EOL;
echo 'Inserted: ' . (int) ($result['imported'] ?? 0) . PHP_EOL;
echo 'Updated: ' . (int) ($result['updated'] ?? 0) . PHP_EOL;
if (!empty($result['error'])) {
    echo 'Error: ' . $result['error'] . PHP_EOL;
    exit(1);
}

$count = mysqli_fetch_assoc(mysqli_query($db->dbLink, 'SELECT COUNT(*) AS c FROM mystery'));
echo 'Total rows in mystery table: ' . (int) ($count['c'] ?? 0) . PHP_EOL;
