<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Ensure the database connection is included
    $action = $_POST['action']; // Get the action ('insert' or 'update')

    $country_name = trim($_POST['country_name']);
    $country_code = strtoupper(trim($_POST['country_code'])); // Uppercase for consistency
    $country_status = $_POST['status'];
    $country_id = $_POST['country_id'] ?? null; // Only for updates

    if (strlen($country_code) !== 2) {
        echo json_encode(['success' => false, 'message' => 'Country code must be exactly 2 characters.']);
        exit;
    }

    try {
        if ($action === 'update') {
            // Prepare update statement
            $sql = "UPDATE country SET country_name = ?, country_code = ?, status = ? WHERE country_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("sssi", $country_name, $country_code, $country_status, $country_id);
        } elseif ($action === 'insert') {
            // Prepare insert statement
            $sql = "INSERT INTO country (country_name, country_code, status) VALUES (?, ?, ?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("sss", $country_name, $country_code, $country_status);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid operation or missing ID.']);
            exit;
        }

        if ($stmt->execute()) {
            $message = ($action === 'update') ? 'Country updated successfully.' : 'Country added successfully.';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database operation failed.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

// Make sure this points to your database configuration file


?>










