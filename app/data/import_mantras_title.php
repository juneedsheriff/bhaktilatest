<?php
include dirname(__DIR__) . '/class/databaseConn.php';
include dirname(__DIR__) . '/lib/mantrasTitleImport.php';

$DatabaseCo = new DatabaseConn();
$csvPath = __DIR__ . '/mantras_title_list.csv';
$result = importMantrasTitleFromCsv($DatabaseCo->dbLink, $csvPath);

echo 'Imported: ' . $result['imported'] . PHP_EOL;
echo 'Skipped: ' . $result['skipped'] . PHP_EOL;
echo 'Errors: ' . count($result['errors']) . PHP_EOL;

if (!empty($result['errors'])) {
    echo implode(PHP_EOL, $result['errors']) . PHP_EOL;
}
