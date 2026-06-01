<?php
ob_start();
header('Content-Type: application/json; charset=utf-8');

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

$response = ['status' => 'error', 'email_exists' => false, 'message' => ''];

if (isset($_POST['email'])) {
    $email = $DatabaseCo->dbLink->real_escape_string(trim($_POST['email']));
    $phoneRaw = isset($_POST['phone']) ? preg_replace('/\D/', '', trim($_POST['phone'])) : '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email address.';
        ob_end_clean();
        echo json_encode($response);
        exit;
    }

    if (strlen($phoneRaw) < 10 || strlen($phoneRaw) > 15 || !ctype_digit($phoneRaw)) {
        $response['message'] = 'Please enter a valid phone number (10–15 digits with country code).';
        ob_end_clean();
        echo json_encode($response);
        exit;
    }
    $phone = $DatabaseCo->dbLink->real_escape_string($phoneRaw);

    $checkSql = "SELECT index_id FROM subscribe WHERE email = ? LIMIT 1";
    $stmt = $DatabaseCo->dbLink->prepare($checkSql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $response['status'] = 'exists';
        $response['email_exists'] = true;
        $response['message'] = 'This email is already subscribed.';
        ob_end_clean();
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    $insertSql = "INSERT INTO subscribe (email, phone) VALUES (?, ?)";
    $stmt = $DatabaseCo->dbLink->prepare($insertSql);
    $stmt->bind_param("ss", $email, $phone);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'Thank you! Your subscription has been successfully recorded.';
    } else {
        if ($DatabaseCo->dbLink->errno === 1062) {
            $response['status'] = 'exists';
            $response['email_exists'] = true;
            $response['message'] = 'This email is already subscribed.';
        } else {
            $response['message'] = 'Subscription failed. Please try again.';
        }
    }
    $stmt->close();
} else {
    $response['message'] = 'Email or phone number not provided.';
}

ob_end_clean();
echo json_encode($response);
$DatabaseCo->dbLink->close();

