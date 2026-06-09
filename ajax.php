<?php
error_reporting(0);
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './include/mystery_helpers.php';
include_once './include/mystery_table_helpers.php';
include_once './include/abroad_listing_helpers.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$db = $DatabaseCo->dbLink;

if (!empty($_REQUEST['comment']) && !empty($_REQUEST['name'])) {
    $name = $xssClean->clean_input($_REQUEST['name']);
    $comment = $xssClean->clean_input($_REQUEST['comment']);
    $type = $xssClean->clean_input($_REQUEST['ty'] ?? '');
    $temple_id = $xssClean->clean_input($_REQUEST['id'] ?? '');
    $log = date("d-m-Y H:i A");
    $query = "INSERT INTO `comments` (`name`, temple_id, `type`, `comment`, is_approved, log_date) VALUES ('$name', '$temple_id', '$type', '$comment', '0', '$log')";
    mysqli_query($db, $query);
}

$page = isset($_REQUEST['pageid']) ? (int) $_REQUEST['pageid'] : 1;
$type = $_REQUEST['type'] ?? '';
$limit = ($type === 'india') ? 50 : (($type === 'abroad' || $type === 'mantras') ? abroad_listing_per_page() : 9);
$start = $page * $limit;

if ($type === 'india') {
    $query = "SELECT * FROM temples WHERE status='approved' ORDER BY order_by ASC LIMIT $start,$limit";
    $result = mysqli_query($db, $query);

    $listingsHtml = '';
    while ($Row = mysqli_fetch_array($result)) {
        $photos = htmlspecialchars($Row['photos']);
        $title = htmlspecialchars($Row['title']);
        $index_id = (int) $Row['index_id'];

        $listingsHtml .= "<div class='listing'>
                            <a href='temple-details.php?id={$index_id}' target='_blank'>
                                <img src='app/uploads/temple/{$photos}' alt=''>
                            </a>
                            <div class='listing-details'>
                                <a href='temple-details.php?id={$index_id}' target='_blank'>
                                    <div class='listing-title'>{$title}</div>
                                </a>
                            </div>
                          </div>";
    }
    echo $listingsHtml;
}

if ($type === 'abroad') {
    $query = "SELECT * FROM abroad WHERE status='approved' ORDER BY " . abroad_listing_order_sql() . " LIMIT $start,$limit";
    $result = mysqli_query($db, $query);

    $listingsHtml = '';
    while ($Row = mysqli_fetch_array($result)) {
        $listingsHtml .= abroad_listing_html($db, $Row);
    }
    echo $listingsHtml;
}

if ($type === 'mystery') {
    $allItems = mystery_table_load_all($db);
    $pageItems = array_slice($allItems, $start, $limit);

    $listingsHtml = '';
    foreach ($pageItems as $Row) {
        $listingsHtml .= mystery_table_listing_html($Row);
    }

    echo $listingsHtml;
}

if ($type === 'mantras') {
    $query = "SELECT * FROM abroad WHERE status='approved' ORDER BY " . abroad_listing_order_sql() . " LIMIT $start,$limit";
    $result = mysqli_query($db, $query);

    $listingsHtml = '';
    while ($Row = mysqli_fetch_array($result)) {
        $listingsHtml .= abroad_listing_html($db, $Row);
    }
    echo $listingsHtml;
}
