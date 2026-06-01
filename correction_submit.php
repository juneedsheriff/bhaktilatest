<?php
session_start();

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean   = new xssClean();

// HEADER FOR JSON RESPONSE
header("Content-Type: application/json");

// --------------------------------------
// CSRF Validation
// --------------------------------------
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
    exit;
}

// --------------------------------------
// Required Fields Validation
// --------------------------------------
$requiredFields = ['user_name', 'user_email', 'user_phone', 'correction_details', 'page_url'];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'status' => 'error',
            'message' => "Field '$field' is required."
        ]);
        exit;
    }
}

// --------------------------------------
// Clean & Escape Inputs
// --------------------------------------
$user_name  = $DatabaseCo->dbLink->real_escape_string($xssClean->clean_input($_POST['user_name']));
$user_email = $DatabaseCo->dbLink->real_escape_string($xssClean->clean_input($_POST['user_email']));
$user_phone = $DatabaseCo->dbLink->real_escape_string($xssClean->clean_input($_POST['user_phone']));
$details    = $DatabaseCo->dbLink->real_escape_string($xssClean->clean_input($_POST['correction_details']));
$page_url   = $DatabaseCo->dbLink->real_escape_string($_POST['page_url']);

$user_ip    = $_SERVER['REMOTE_ADDR'];

// --------------------------------------
// DB INSERT
// --------------------------------------
$sql = "INSERT INTO correction_feedback 
(user_name, user_email, user_phone, correction_details, page_url, user_ip)
VALUES (
    '$user_name',
    '$user_email',
    '$user_phone',
    '$details',
    '$page_url',
    '$user_ip'
)";

if ($DatabaseCo->dbLink->query($sql) === TRUE) {

    // --------------------------------------
    // SEND EMAIL TO ADMIN
    // --------------------------------------
    $admin_email = "jsskmd@gmail.com";
    $subject     = "New Correction Submitted - $page_url";

    $emailMsg = "
A new correction has been submitted:

Name: $user_name
Email: $user_email
Phone: $user_phone
IP: $user_ip
Page URL: $page_url

Correction:
$details
";

    $headers = "From: noreply@bhaktikalpa.com";

    @mail($admin_email, $subject, $emailMsg, $headers);

    echo json_encode(['status' => 'success', 'message' => 'Correction submitted successfully.']);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => $DatabaseCo->dbLink->error
    ]);
}

// CLOSE CONNECTION
$DatabaseCo->dbLink->close();
?>
