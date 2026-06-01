<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Ensure the database connection is included
    $action = $_POST['action']; // Get the action ('insert' or 'update')

    $country_code = trim($_POST['country_code']);
    $state_name = trim($_POST['state_name']);
    $state_code = strtoupper(trim($_POST['state_code'])); // Uppercase for consistency
    $state_status = $_POST['status'];
    $state_id = $_POST['state_id'] ?? null; // Only for updates

    if (strlen($country_code) !== 2) {
        echo json_encode(['success' => false, 'message' => 'State code must be exactly 2 characters.']);
        exit;
    }

    try {
        if ($action === 'update') {
            // Prepare update statement
            $sql = "UPDATE `state` SET country_code= ?,  state_name = ?, state_code = ?, status = ? WHERE state_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("ssssi",$country_code, $state_name, $state_code, $state_status, $state_id);
        } elseif ($action === 'insert') {
            // Prepare insert statement
            $sql = "INSERT INTO `state` (country_code,state_name, state_code, status) VALUES (?, ?, ?, ?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("ssss",$country_code, $state_name, $state_code, $state_status);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid operation or missing ID.']);
            exit;
        }

        if ($stmt->execute()) {
            $message = ($action === 'update') ? 'State updated successfully.' : 'State added successfully.';
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










