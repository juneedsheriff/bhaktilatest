<?php
include dirname(__DIR__) . '/class/databaseConn.php';
include dirname(__DIR__) . '/lib/mantrasListImport.php';

$DatabaseCo = new DatabaseConn();
$csvPath = __DIR__ . '/mantras_list.csv';
$result = importMantrasListFromCsv($DatabaseCo->dbLink, $csvPath);

echo 'Imported: ' . $result['imported'] . PHP_EOL;
echo 'Updated: ' . $result['updated'] . PHP_EOL;
echo 'Skipped: ' . $result['skipped'] . PHP_EOL;
echo 'Errors: ' . count($result['errors']) . PHP_EOL;

if (!empty($result['errors'])) {
    echo implode(PHP_EOL, array_slice($result['errors'], 0, 20)) . PHP_EOL;
}
