<?php
include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


//header("Content-Type: application/json");

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}

// 1. Basic validation
$requiredFields = ['templeName', 'deityName', 'country', 'address'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Field '$field' is required."]);
        exit;
    }
}


// 3. Escape and assign variables
$templeName  =$DatabaseCo->dbLink->real_escape_string($_POST['templeName']);
$deityName   =$DatabaseCo->dbLink->real_escape_string($_POST['deityName']);
$phone       = isset($_POST['phone']) ?$DatabaseCo->dbLink->real_escape_string($_POST['phone']) : '';
$email       = isset($_POST['email']) ?$DatabaseCo->dbLink->real_escape_string($_POST['email']) : '';
$timings     = isset($_POST['timings']) ?$DatabaseCo->dbLink->real_escape_string($_POST['timings']) : '';
$year        = isset($_POST['year']) ?$DatabaseCo->dbLink->real_escape_string($_POST['year']) : '';
$my_stery    = isset($_POST['my_stery']) ? 1 : 0;
$country     =$DatabaseCo->dbLink->real_escape_string($_POST['country']);
$state       =$DatabaseCo->dbLink->real_escape_string($_POST['state']);
$city        =$DatabaseCo->dbLink->real_escape_string($_POST['city']);
$address     =$DatabaseCo->dbLink->real_escape_string($_POST['address']);
$description = isset($_POST['description']) ?$DatabaseCo->dbLink->real_escape_string($_POST['description']) : '';
$photoPath   = '';

// 4. Handle file upload (if photo provided)
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'app/uploads/temples_submission_request_images/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $photoName = time() . "_" . basename($_FILES['photo']['name']);
    $photoPath = $uploadDir . $photoName;
    move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    $photoPath =$DatabaseCo->dbLink->real_escape_string($photoPath);
}
if($state==""){
    $state = '';
}

if($city==""){
    $city = '';
}
// 5. Insert query
$sql = "INSERT INTO temples_submission_request 
    (temple_name, deity_name, phone, email, timings, established_year, mystery, country, state, city, address, description, photo) 
    VALUES (
        '$templeName', '$deityName', '$phone', '$email', '$timings', '$year', $my_stery,
        '$country', '$state', '$city', '$address', '$description', '$photoPath'
    )";

if ($DatabaseCo->dbLink->query($sql) === TRUE) {

        echo json_encode(['status' => 'success']);

} else {
    
        echo json_encode(['status' => 'error', 'message' => $DatabaseCo->dbLink->error]);

}

// Close the database connection
$DatabaseCo->dbLink->close();
