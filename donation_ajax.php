<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();

include_once './app/class/XssClean.php';

$xssClean = new xssClean();

header('Content-Type: application/json');
ob_clean(); // clear any accidental output
$response = ['success' => false, 'message' => 'Something went wrong.'];

session_start(); // Ensure sessions are active

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ CSRF Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid CSRF token.';
        echo json_encode($response);
        exit;
    }

    $db = $DatabaseCo->dbLink;

    // ✅ Sanitize & Collect Inputs
    $full_name      = mysqli_real_escape_string($db, trim($_POST['donor_name'] ?? ''));
    $contact_number = mysqli_real_escape_string($db, trim($_POST['donor_phone'] ?? ''));
    $email          = mysqli_real_escape_string($db, trim($_POST['donor_email'] ?? ''));
    $address        = mysqli_real_escape_string($db, trim($_POST['address'] ?? ''));
    $amount         = floatval($_POST['amount'] ?? 0);
    $donation_purpose  = mysqli_real_escape_string($db, trim($_POST['donation_purpose'] ?? 'General')); 
    $remarks        = mysqli_real_escape_string($db, trim($_POST['remarks'] ?? ''));

    // ✅ Validation
    if ($full_name === '' || $amount <= 0) {
        $response['message'] = 'Name and valid donation amount are required.';
        echo json_encode($response);
        exit;
    }

    // ✅ Insert Donation
    $sql = "INSERT INTO donations (
                full_name, contact_number, email, address,
                amount, donation_type, remarks, created_at
            ) VALUES (
                '$full_name', '$contact_number', '$email', '$address',
                $amount, '$donation_purpose', '$remarks', NOW()
            )";

    if (mysqli_query($db, $sql)) {
        $response['success'] = true;
        $response['message'] = 'Donation recorded successfully. Thank you for your contribution!';
    } else {
        $response['message'] = 'Database error: ' . mysqli_error($db);
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit();