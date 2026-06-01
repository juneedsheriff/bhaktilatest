<?php
include_once __DIR__ . '/class/databaseConn.php';

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;
$countryCode = isset($_POST['country_code']) ? trim($DatabaseCo->dbLink->real_escape_string($_POST['country_code'])) : '';

echo '<option value="">Select City</option>';

if ($countryCode === '' || strtoupper($countryCode) === 'IN') {
    return;
}

$sql = "SELECT DISTINCT TRIM(a.city) AS city_name
        FROM abroad a
        WHERE a.status = 'approved'
          AND a.country = '$countryCode'
          AND TRIM(COALESCE(a.city, '')) != ''
        ORDER BY city_name ASC";

$result = mysqli_query($db, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['city_name'];
        echo '<option value="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">'
            . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
