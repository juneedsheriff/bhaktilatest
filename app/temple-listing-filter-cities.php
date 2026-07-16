<?php
include_once __DIR__ . '/class/databaseConn.php';
include_once __DIR__ . '/includes/temple_listing_query.php';

if (empty($_SESSION['admin_id']) && empty($_SESSION['staff_id'])) {
    http_response_code(403);
    exit;
}

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

$stateCode = isset($_REQUEST['state_code']) ? trim((string) $_REQUEST['state_code']) : '';
$selectedPlace = isset($_REQUEST['selected']) ? trim((string) $_REQUEST['selected']) : '';
$listTempleStatus = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], temple_listing_valid_tabs(), true))
    ? (string) $_REQUEST['temple_status']
    : '';

header('Content-Type: text/html; charset=utf-8');

$allSelected = ($selectedPlace === '' || strtoupper($selectedPlace) === 'ALL') ? ' selected' : '';
echo '<option value="ALL"' . $allSelected . '>ALL</option>';

$where = temple_listing_opt_where($listTempleStatus);
if ($stateCode !== '' && strtoupper($stateCode) !== 'ALL') {
    $where .= " AND t.`state` = '" . $db->real_escape_string($stateCode) . "' ";
}

$sql = "SELECT DISTINCT t.city AS place_value, c.city_name AS place_label
    FROM temples t
    INNER JOIN city c ON c.city_id = t.city
    WHERE $where AND TRIM(COALESCE(c.city_name, '')) != ''
    ORDER BY c.city_name";

$result = mysqli_query($db, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $value = (string) ($row['place_value'] ?? '');
        $label = trim((string) ($row['place_label'] ?? ''));
        if ($value === '' || $label === '') {
            continue;
        }
        $sel = ($selectedPlace !== '' && strtoupper($selectedPlace) !== 'ALL' && $selectedPlace === $value) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
