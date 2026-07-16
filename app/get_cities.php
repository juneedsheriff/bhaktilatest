<?php

include_once __DIR__ . '/class/databaseConn.php';

$DatabaseCo = new DatabaseConn();
$link = $DatabaseCo->dbLink;

$state_code = isset($_POST['state_code']) ? $link->real_escape_string(trim($_POST['state_code'])) : '';
$for_temples = !empty($_POST['for_temples']);
$country = isset($_POST['country']) ? $link->real_escape_string(trim($_POST['country'])) : 'IN';

echo "<option value=''>Select City</option>";

if ($state_code) {
    if ($for_temples) {
        $query = "SELECT DISTINCT c.city_id, c.city_name
            FROM temples t
            INNER JOIN city c ON c.city_id = t.city
            WHERE t.status = 'approved'
            AND t.country = '$country'
            AND (t.state = '$state_code'
                OR t.state = (SELECT CAST(state_id AS CHAR) FROM state WHERE state_code = '$state_code' AND country_code = '$country' LIMIT 1))
            AND TRIM(COALESCE(c.city_name, '')) != ''
            ORDER BY c.city_name ASC";
    } else {
        $query = "SELECT city_id, city_name FROM city WHERE state_code = '$state_code' ORDER BY city_name ASC";
    }

    $result = mysqli_query($link, $query);

    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            $name = htmlspecialchars((string) $row->city_name, ENT_QUOTES, 'UTF-8');
            echo "<option value='{$row->city_id}'>{$name}</option>";
        }
    }
}

?>
