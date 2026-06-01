<?php
session_start();
header("Content-Type: application/json");
//SHOW ERRORS
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

include_once "../app/class/databaseConn.php";
include_once "../app/lib/requestHandler.php";

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

$action = $_POST['action'] ?? "";

function clean($str) {
    return mysqli_real_escape_string($GLOBALS['db'], trim($str));
}

/* ============================================================================
    1. GET SUB CATEGORIES BASED ON SELECTED GODS
    TABLE: mantras_subcategory
============================================================================ */
if ($action == "get_sub_categories") {

    if (!isset($_POST['god_ids'])) {
        echo json_encode([
            "status" => false,
            "message" => "No gods selected."
        ]);
        exit();
    }

    $god_ids = $_POST['god_ids'];

    // CASE 1: If "All" selected
    if ($god_ids === 'all' || (is_array($god_ids) && in_array('all', $god_ids))) {

        $query = "
            SELECT 
                banner,
                index_id,
                categories_id,
                title,
                photos,
                description,
                order_by,
                status
            FROM mantras_subcategory
            WHERE status = 'approved'
            ORDER BY order_by ASC, title ASC
        ";

    } 
    // CASE 2: Normal multiple IDs
    else if (is_array($god_ids) && count($god_ids) > 0) {

        // Clean IDs
        $clean_ids = array_map('intval', $god_ids);
        $ids_str = implode(",", $clean_ids);

        $query = "
            SELECT 
                banner,
                index_id,
                categories_id,
                title,
                photos,
                description,
                order_by,
                status
            FROM mantras_subcategory
            WHERE categories_id IN ($ids_str)
            AND status = 'approved'
            ORDER BY order_by ASC, title ASC
        ";

    } else {
        echo json_encode([
            "status" => false,
            "message" => "Invalid selection."
        ]);
        exit();
    }

        $result = mysqli_query($db, $query);

        if (!$result) {
            echo json_encode([
                "status" => false,
                "message" => "Query Error",
                "error" => mysqli_error($db)
            ]);
            exit();
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                "index_id"     => $row['index_id'],
                "categories_id"=> $row['categories_id'],
                "title"        => $row['title'],
                "title_clean"  => htmlspecialchars($row['title']),
                "banner"       => 'app/uploads/gods/banner/'.$row['banner'],
                "photos"       => 'app/uploads/gods/'.$row['photos'],
                "description"  => $row['description'],
                "description_clean" => nl2br(htmlspecialchars($row['description'])),
                "order_by"     => $row['order_by']
            ];
        }

        echo json_encode([
            "status" => true,
            "count"  => count($data),
            "data"   => $data
        ]);
        exit();
}

if ($action == "get_mantras_details") {

    $ids = $_POST['mantra_ids'] ?? [];
    $meaningOption = $_POST['meaningOption'] ?? "without";

    if (!is_array($ids) || count($ids) == 0) {
        echo json_encode(["status" => false]);
        exit;
    }

    // Convert to safe CSV
    $clean_ids = array_map(function ($id) use ($db) {
        return intval(mysqli_real_escape_string($db, $id));
    }, $ids);

    $idList = implode(",", $clean_ids);

    $sql = "SELECT * FROM mantras_stotras 
            WHERE sub_category IN ($idList) AND status='approved'";

    $result = mysqli_query($db, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => true,
        "data" => $data
    ]);
    exit;
}

if ($action == "get_mantras_list_by_title") {

    $title = $_POST['title'] ?? '';
$meaningOption = $_POST['meaningOption'] ?? "without";
if (empty($title)) {
    echo json_encode([
        "status" => false,
        "message" => "Title is required."
    ]);
    exit();
}

// Clean input
$clean = mysqli_real_escape_string($db, $title);

// Correct SQL
$query = "
    SELECT * FROM mantras_stotras
    WHERE mantras_title = '$clean'
    AND status = 'approved'
     ORDER BY order_by ASC, title ASC
";

$result = mysqli_query($db, $query);

if (!$result) {
    echo json_encode([
        "status" => false,
        "message" => "Query Error",
        "error" => mysqli_error($db)
    ]);
    exit();
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "index_id"       => $row['index_id'],
        "categories_id"  => $row['categories_id'],
        "title"          => $row['title'],
        "title_clean"    => htmlspecialchars($row['title']),
        "banner"         => $row['banner'],
        "photos"         => $row['photos'],
        "content"        => $row['content'],
        "meaning"        => $row['meaning'],
        "audio"          => $row['audio'],
    ];
}

echo json_encode([
    "status" => true,
    "count"  => count($data),
    "data"   => $data
]);
exit();
}


echo json_encode(["status" => false, "message" => "Invalid Action"]);
exit();
?>
