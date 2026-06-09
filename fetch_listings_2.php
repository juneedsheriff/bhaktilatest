<?php

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$link = $DatabaseCo->dbLink;

$country = isset($_POST['country']) ? $xssClean->clean_input($_POST['country']) : 'IN';
$state = isset($_POST['state']) ? trim($xssClean->clean_input($_POST['state'])) : '';
$city  = isset($_POST['city'])  ? trim($xssClean->clean_input($_POST['city']))  : '';
$town  = isset($_POST['town'])  ? trim($xssClean->clean_input($_POST['town']))  : '';
$filter_type = isset($_POST['filter_type']) ? $_POST['filter_type'] : 'combined';

/**
 * Build WHERE clause for India temple location filters (approved only).
 */
function temples_india_location_where($link, $country, $state, $city, $town, $filter_type)
{
    $country_esc = mysqli_real_escape_string($link, $country);
    $where = "status = 'approved' AND country = '" . $country_esc . "'";

    if ($filter_type === 'separate') {
        $filters = [];
        if ($state !== '') {
            $filters[] = "state = '" . mysqli_real_escape_string($link, $state) . "'";
        }
        if ($city !== '') {
            $filters[] = "city = '" . mysqli_real_escape_string($link, $city) . "'";
        }
        if (count($filters) > 0) {
            $where .= ' AND (' . implode(' OR ', $filters) . ')';
        }
    } else {
        if ($state !== '') {
            $state_esc = mysqli_real_escape_string($link, $state);
            $where .= " AND (state = '" . $state_esc . "' OR state = (SELECT state_id FROM state WHERE state_code = '" . $state_esc . "' AND country_code = '" . $country_esc . "' LIMIT 1))";
        }
        if ($city !== '') {
            $where .= " AND city = '" . mysqli_real_escape_string($link, $city) . "'";
        }
        if ($town !== '') {
            $where .= " AND town = '" . mysqli_real_escape_string($link, $town) . "'";
        }
    }

    return $where;
}

$where = temples_india_location_where($link, $country, $state, $city, $town, $filter_type);

$countQuery = 'SELECT COUNT(*) AS total FROM temples WHERE ' . $where;
$countResult = mysqli_query($link, $countQuery);
$countRow = $countResult ? mysqli_fetch_assoc($countResult) : ['total' => 0];
$total = (int) ($countRow['total'] ?? 0);

$query = 'SELECT * FROM temples WHERE ' . $where . ' ORDER BY order_by ASC';
$result = mysqli_query($link, $query);

$listingsHtml = '';
if ($result && mysqli_num_rows($result) > 0) {
    while ($Row = mysqli_fetch_assoc($result)) {
        $photos = $Row['photos'];
        $city_name = '';
        $state_name = '';
        $town_name = '';
        if (!empty($Row['city'])) {
            $cr = mysqli_query($link, "SELECT city_name FROM city WHERE city_id = '" . mysqli_real_escape_string($link, $Row['city']) . "' LIMIT 1");
            if ($cr && $crow = mysqli_fetch_assoc($cr)) {
                $city_name = $crow['city_name'];
            }
        }
        if (!empty($Row['state']) && !empty($Row['country'])) {
            $sr = mysqli_query($link, "SELECT state_name FROM state WHERE (state_code = '" . mysqli_real_escape_string($link, $Row['state']) . "' OR state_id = '" . mysqli_real_escape_string($link, $Row['state']) . "') AND country_code = '" . mysqli_real_escape_string($link, $Row['country']) . "' LIMIT 1");
            if ($sr && $srow = mysqli_fetch_assoc($sr)) {
                $state_name = $srow['state_name'];
            }
        }
        if (!empty($Row['town'])) {
            $tr = mysqli_query($link, "SELECT town_name FROM towns WHERE id = '" . mysqli_real_escape_string($link, $Row['town']) . "' LIMIT 1");
            if ($tr && $trow = mysqli_fetch_assoc($tr)) {
                $town_name = $trow['town_name'];
            }
        }
        $location = trim(implode(', ', array_filter([$city_name, $state_name, $town_name])));
        $title_display = $Row['title'] . ($location !== '' ? ', ' . $location : '');

        $listingsHtml .= "<div class='listing'>
                <a href='temple-details.php?id={$Row['index_id']}'>
                    <img src='app/uploads/temple/{$photos}' alt=''>
                </a>
                <div class='listing-details'>
                    <a href='temple-details.php?id={$Row['index_id']}'>
                        <div class='listing-title'>" . htmlspecialchars($title_display) . "</div>
                    </a>
                </div>
              </div>";
    }
} else {
    $listingsHtml = '<p>No listings found.</p>';
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'count' => $total,
    'html'  => $listingsHtml,
]);
