<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include_once 'class/XssClean.php';
include_once 'class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Something went wrong.'];

session_start(); // Ensure sessions are active

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid CSRF token.';
        echo json_encode($response);
        exit;
    }

    $db = $DatabaseCo->dbLink;

    // Basic ad fields
   $id                 = intval($_POST['id'] ?? 0);
    $full_name          = mysqli_real_escape_string($db, trim($_POST['full_name'] ?? ''));
    $contact_number     = mysqli_real_escape_string($db, trim($_POST['contact_number'] ?? ''));
    $email              = mysqli_real_escape_string($db, trim($_POST['email'] ?? ''));
    $address            = mysqli_real_escape_string($db, trim($_POST['address'] ?? ''));
    $date_of_birth      = mysqli_real_escape_string($db, trim($_POST['date_of_birth'] ?? ''));
    $gender             = mysqli_real_escape_string($db, trim($_POST['gender'] ?? ''));
    $experience_years   = intval($_POST['experience_years'] ?? 0);
    $specialization     = mysqli_real_escape_string($db, trim($_POST['specialization'] ?? ''));
    $available          = intval($_POST['available'] ?? 1);

    // Check for existing priest with same name and contact number
    $check_sql = "SELECT COUNT(*) FROM priests WHERE full_name = '$full_name' AND contact_number = '$contact_number'";
    $check_res = mysqli_query($db, $check_sql);
    $check_row = mysqli_fetch_row($check_res);

    if ($check_row[0] > 0 && $id === 0) {
        $response['success'] = false;
        $response['message'] = 'Priest with this name and contact number already exists.';
        echo json_encode($response);
        exit;
    }

    // Handle photo upload
    $photo_path = '';
    if (!empty($_FILES['photo']['name'])) {
        $upload_dir = 'uploads/priest/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = basename($_FILES['photo']['name']);
        $clean_name = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $target_path = $upload_dir . time() . '_' . $clean_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $photo_path = mysqli_real_escape_string($db, $target_path);
        } else {
            $response['message'] = 'Photo upload failed.';
            echo json_encode($response);
            exit;
        }
    }

    if ($id === 0) {
        // INSERT
        $sql = "INSERT INTO priests (
            full_name, contact_number, email, address, date_of_birth,
            gender, experience_years, specialization, available,
            photo, created_at, updated_at
        ) VALUES (
            '$full_name', '$contact_number', '$email', '$address', '$date_of_birth',
            '$gender', $experience_years, '$specialization', $available,
            '$photo_path', NOW(), NOW()
        )";

        if (mysqli_query($db, $sql)) {
            $response['success'] = true;
            $response['message'] = 'Priest inserted successfully.';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($db);
        }

    } else {
        // UPDATE
        $update_fields = "
            full_name = '$full_name',
            contact_number = '$contact_number',
            email = '$email',
            address = '$address',
            date_of_birth = '$date_of_birth',
            gender = '$gender',
            experience_years = $experience_years,
            specialization = '$specialization',
            available = $available,
            updated_at = NOW()";

        if ($photo_path !== '') {
            $update_fields .= ", photo = '$photo_path'";
        }

        $sql = "UPDATE priests SET $update_fields WHERE id = $id";

        if (mysqli_query($db, $sql)) {
            $response['success'] = true;
            $response['message'] = 'Priest updated successfully.';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($db);
        }
    }

    

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);