<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action !== 'insert' && $action !== 'update') {
        echo json_encode(['success' => false, 'message' => 'Invalid operation.']);
        exit;
    }

    $city_name = trim($_POST['city_name']);
    $country_id = isset($_POST['country_id']) ? intval($_POST['country_id']) : 0;
    $state_code = strtoupper(trim($_POST['state_code'] ?? ''));
    $country_status = $_POST['status'] ?? 'UNAPPROVED';
    $city_id = isset($_POST['city_id']) && $_POST['city_id'] !== '' ? intval($_POST['city_id']) : null;

    if ($country_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please select a country.']);
        exit;
    }
    if ($state_code === '') {
        echo json_encode(['success' => false, 'message' => 'Please select a state.']);
        exit;
    }

    // Get country_code from country table for display/reference
    $codeResult = $DatabaseCo->dbLink->query("SELECT country_code FROM country WHERE country_id = " . $country_id);
    $country_code = ($codeResult && $codeRow = $codeResult->fetch_object()) ? $codeRow->country_code : '';

    try {
        if ($action === 'update' && $city_id) {
            $sql = "UPDATE city SET city_name = ?, country_id = ?, country_code = ?, status = ?, state_code = ? WHERE city_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("sisssi", $city_name, $country_id, $country_code, $country_status, $state_code, $city_id);
        } elseif ($action === 'insert') {
            $sql = "INSERT INTO city (city_name, country_id, country_code, status, state_code) VALUES (?, ?, ?, ?, ?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("sisss", $city_name, $country_id, $country_code, $country_status, $state_code);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid operation.']);
            exit;
        }

        // Execute the statement and send response
        if ($stmt->execute()) {
            $message = ($action === 'update') ? 'City updated successfully.' : 'City added successfully.';
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










