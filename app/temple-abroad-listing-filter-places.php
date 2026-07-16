<?php
include_once __DIR__ . '/class/databaseConn.php';
include_once __DIR__ . '/includes/abroad_temple_listing_query.php';

if (empty($_SESSION['admin_id']) && empty($_SESSION['staff_id'])) {
    http_response_code(403);
    exit;
}

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

$countryCode = isset($_REQUEST['country_code']) ? trim((string) $_REQUEST['country_code']) : '';
$selectedPlace = isset($_REQUEST['selected']) ? trim((string) $_REQUEST['selected']) : '';
$listTempleStatus = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], abroad_temple_listing_valid_tabs(), true))
    ? (string) $_REQUEST['temple_status']
    : '';

header('Content-Type: text/html; charset=utf-8');

$allSelected = ($selectedPlace === '' || strtoupper($selectedPlace) === 'ALL') ? ' selected' : '';
echo '<option value="ALL"' . $allSelected . '>ALL</option>';

$where = abroad_temple_listing_opt_where($listTempleStatus);
if ($countryCode !== '' && strtoupper($countryCode) !== 'ALL') {
    $where .= " AND a.`country` = '" . $db->real_escape_string($countryCode) . "' ";
}

$sql = "SELECT DISTINCT a.temple_place AS place_value, a.temple_place AS place_label
    FROM abroad a
    WHERE $where AND TRIM(COALESCE(a.temple_place, '')) != ''
    ORDER BY a.temple_place";

$result = mysqli_query($db, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $value = trim((string) ($row['place_value'] ?? ''));
        $label = trim((string) ($row['place_label'] ?? ''));
        if ($value === '' || $label === '') {
            continue;
        }
        $sel = ($selectedPlace !== '' && strtoupper($selectedPlace) !== 'ALL' && $selectedPlace === $value) ? ' selected' : '';
        echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>'
            . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
