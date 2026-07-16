<?php
include_once './class/databaseConn.php';
include_once './includes/temple_listing_query.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id']) && empty($_SESSION['staff_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_role = !empty($_SESSION['admin_id']) ? 'Admin' : 'Staff';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

$list_temple_status = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], temple_listing_valid_tabs(), true))
    ? (string) $_REQUEST['temple_status']
    : '';

$filters = temple_listing_parse_filters($db, $_REQUEST);
$draw = isset($_REQUEST['draw']) ? (int) $_REQUEST['draw'] : 1;
$start = isset($_REQUEST['start']) ? max(0, (int) $_REQUEST['start']) : 0;
$length = isset($_REQUEST['length']) ? (int) $_REQUEST['length'] : 25;
$search = trim((string) ($_REQUEST['search']['value'] ?? ''));

$orderColumn = 0;
$orderDir = 'desc';
if (!empty($_REQUEST['order'][0]['column'])) {
    $orderColumn = (int) $_REQUEST['order'][0]['column'];
}
if (!empty($_REQUEST['order'][0]['dir'])) {
    $orderDir = (string) $_REQUEST['order'][0]['dir'];
}

$result = temple_listing_fetch_datatable(
    $db,
    $list_temple_status,
    $filters['filter_sql'],
    $search,
    $start,
    $length,
    $orderColumn,
    $orderDir,
    $user_role
);

if (!empty($result['error'])) {
    http_response_code(500);
    echo json_encode(['error' => $result['error']]);
    exit;
}

echo json_encode([
    'draw' => $draw,
    'recordsTotal' => $result['recordsTotal'],
    'recordsFiltered' => $result['recordsFiltered'],
    'data' => $result['data'],
]);
