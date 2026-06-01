<?php

 include_once __DIR__ . '/class/databaseConn.php';

 $DatabaseCo = new DatabaseConn();

 $state_code = isset($_POST['state_code']) ? $DatabaseCo->dbLink->real_escape_string(trim($_POST['state_code'])) : '';

echo "<option value=''>-Select City-</option>";
 if ($state_code) {

     $query = "SELECT * FROM city WHERE state_code = '$state_code' ORDER BY city_name";

     $result = mysqli_query($DatabaseCo->dbLink, $query);

     

     while ($row = mysqli_fetch_object($result)) {

         echo "<option value='{$row->city_id}'>{$row->city_name}</option>";

     }

 }

?>

