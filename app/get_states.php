<?php
 include_once './class/databaseConn.php'; // Ensure the database connection is included
 $DatabaseCo = new DatabaseConn();
$country_code = $_POST['country_code'] ?? '';

if ($country_code) {
    $query = "SELECT * FROM state WHERE country_code = '$country_code' ORDER BY state_name";
    $result = mysqli_query($DatabaseCo->dbLink, $query);
    
    while ($row = mysqli_fetch_object($result)) {
        echo "<option value='" . htmlspecialchars($row->state_code) . "'>" . htmlspecialchars($row->state_name) . "</option>";
    }
}
?>
