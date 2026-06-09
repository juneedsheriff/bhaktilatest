<?php

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './include/temple_listing_helpers.php';

header('Content-Type: application/json; charset=utf-8');

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$link = $DatabaseCo->dbLink;

$godIds = isset($_POST['selectedFilters'])
    ? array_filter(explode(',', (string) $_POST['selectedFilters']))
    : [];
$templeIds = isset($_POST['selectedTempleFilters'])
    ? array_map('intval', array_filter(explode(',', (string) $_POST['selectedTempleFilters'])))
    : [];
$country = isset($_POST['country']) ? trim($xssClean->clean_input($_POST['country'])) : 'IN';

if (empty($godIds) && empty($templeIds)) {
    echo json_encode(['listings' => '', 'total' => 0, 'pagination' => '']);
    exit;
}

$result = temples_india_fetch_god_temple_listings($link, $godIds, $templeIds, $country);

echo json_encode([
    'listings' => $result['html'],
    'total' => $result['total'],
    'pagination' => '',
]);
