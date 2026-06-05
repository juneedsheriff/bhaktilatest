<?php
require_once __DIR__ . '/../class/databaseConn.php';
$db = new DatabaseConn();
$r = mysqli_query($db->dbLink, "SELECT index_id, title, status FROM other_page WHERE page_id='8' ORDER BY title");
while ($row = mysqli_fetch_assoc($r)) {
    echo $row['index_id'] . ' | ' . $row['status'] . ' | ' . $row['title'] . PHP_EOL;
}
