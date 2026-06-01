<?php
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './include/abroad_listing_helpers.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$db = $DatabaseCo->dbLink;

$country = isset($_POST['country']) ? trim($xssClean->clean_input($_POST['country'])) : '';
$city = isset($_POST['city']) ? trim($xssClean->clean_input($_POST['city'])) : '';
$filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'combined';

/**
 * Build WHERE clause for abroad location filters (approved only).
 */
function abroad_location_where($db, $country, $city, $filter_type)
{
    $where = "status = 'approved'";

    if ($filter_type === 'combined') {
        if ($country !== '') {
            $where .= " AND country = '" . $db->real_escape_string($country) . "'";
        }
        if ($city !== '') {
            $where .= " AND city = '" . $db->real_escape_string($city) . "'";
        }
    } else {
        $filters = [];
        if ($country !== '') {
            $filters[] = "country = '" . $db->real_escape_string($country) . "'";
        }
        if ($city !== '') {
            $filters[] = "city = '" . $db->real_escape_string($city) . "'";
        }
        if (!empty($filters)) {
            $where .= ' AND (' . implode(' OR ', $filters) . ')';
        }
    }

    return $where;
}

$where = abroad_location_where($db, $country, $city, $filter_type);

$countQuery = 'SELECT COUNT(*) AS total FROM abroad WHERE ' . $where;
$countResult = mysqli_query($db, $countQuery);
$countRow = $countResult ? mysqli_fetch_assoc($countResult) : ['total' => 0];
$total = (int) ($countRow['total'] ?? 0);

$query = 'SELECT * FROM abroad WHERE ' . $where . ' ORDER BY ' . abroad_listing_order_sql();
$result = mysqli_query($db, $query);

$listingsHtml = '';
if ($result && mysqli_num_rows($result) > 0) {
    while ($Row = mysqli_fetch_assoc($result)) {
        $listingsHtml .= abroad_listing_html($db, $Row);
    }
} else {
    $listingsHtml = '<p>No listings found.</p>';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'count' => $total,
    'html'  => $listingsHtml,
]);
