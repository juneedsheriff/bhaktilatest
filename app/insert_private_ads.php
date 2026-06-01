<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
include_once 'class/XssClean.php';

include_once 'class/databaseConn.php';

include_once 'lib/requestHandler.php';


$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Something went wrong.'];

// Validate CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $response['message'] = 'Invalid CSRF token.';
        echo json_encode($response);
        exit;
    }

    $db = $DatabaseCo->dbLink;

    // Sanitize and collect data
    $id             = intval($_POST['id'] ?? 0);
    $title          = mysqli_real_escape_string($db, trim($_POST['title']));
    $description    = mysqli_real_escape_string($db, trim($_POST['description'] ?? ''));
    $price          = floatval($_POST['price'] ?? 0);
    $sort_order          = $_POST['sort_order'] ?? 0;
    $location       = mysqli_real_escape_string($db, trim($_POST['location']));
    $contact_email  = mysqli_real_escape_string($db, trim($_POST['contact_email']));
    $contact_phone  = mysqli_real_escape_string($db, trim($_POST['contact_phone']));
    $is_active      = intval($_POST['is_active'] ?? 1);
    $is_private     = intval($_POST['is_private'] ?? 0);
    $duration_days  = intval($_POST['duration_days'] ?? 30);
    $expiry_date    = date('Y-m-d H:i:s', strtotime("+$duration_days days"));

    // Check duplicate (on insert)
    if ($id === 0) {
        $check_sql = "SELECT COUNT(*) FROM private_ads WHERE title = '$title'";
        $check_res = mysqli_query($db, $check_sql);
        $check_row = mysqli_fetch_row($check_res);
        if ($check_row[0] > 0) {
            $response['message'] = 'An ad with this title already exists.';
            echo json_encode($response);
            exit;
        }
    }

    // Image upload
    $image_path = '';
    if (!empty($_FILES['image_path']['name'])) {
        $upload_dir = 'uploads/private_ads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = basename($_FILES['image_path']['name']);
        $clean_name = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $filename);
        $target_path = $upload_dir . time() . '_' . $clean_name;

        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $target_path)) {
            $image_path = mysqli_real_escape_string($db, $target_path);
        } else {
            $response['message'] = 'Image upload failed.';
            echo json_encode($response);
            exit;
        }
    }

    // INSERT
    if ($id === 0) {
        $sql = "INSERT INTO private_ads 
        (title,sort_order, description, price, location, contact_email, contact_phone, image_path, is_active, is_private, created_at, updated_at, expiry_date,duration_days) 
        VALUES 
        ('$title','$sort_order', '$description', $price, '$location', '$contact_email', '$contact_phone', '$image_path', $is_active, $is_private, NOW(), NOW(), '$expiry_date','$duration_days')";

        if (mysqli_query($db, $sql)) {
            $response['success'] = true;
            $response['message'] = 'Ad inserted successfully.';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($db);
        }

    // UPDATE
    } else {
        $update_fields = "
            title = '$title',
            description = '$description',
            price = $price,
             sort_order = $sort_order,
            location = '$location',
            contact_email = '$contact_email',
            contact_phone = '$contact_phone',
            duration_days = '$duration_days',
            is_active = $is_active,
            is_private = $is_private,
            expiry_date = '$expiry_date',
            updated_at = NOW()";

        if ($image_path !== '') {
            $update_fields .= ", image_path = '$image_path'";
        }

        $sql = "UPDATE private_ads SET $update_fields WHERE id = $id";

        if (mysqli_query($db, $sql)) {
            $response['success'] = true;
            $response['message'] = 'Ad updated successfully.';
        } else {
            $response['message'] = 'Database error: ' . mysqli_error($db);
        }
    }

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
