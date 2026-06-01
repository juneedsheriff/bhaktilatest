<?php
include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();

include_once './app/class/XssClean.php';

$xssClean = new xssClean();
if($_POST['action']=="UpdateViews"){
    if (isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $query = "UPDATE private_ads SET views = views + 1, impressions = impressions + 1 WHERE id = $id";
        mysqli_query($DatabaseCo->dbLink, $query);
        echo "success";
    } else {
        echo "error";
    }
}
if($_POST['action']=="UpdateClicks"){
if (isset($_POST['id']) && $_POST['action']=="UpdateClicks") {
    $id = intval($_POST['id']);
    $query = "UPDATE private_ads SET clicks = clicks + 1 WHERE id = $id";
    mysqli_query($DatabaseCo->dbLink, $query);
    echo "success";
} else {
    echo "error";
}
}
?>