<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Ensure the database connection is included
    $action = $_POST['action']; // Get the action ('insert' or 'update')

    $god_name = trim($_POST['god_name']);
    $sub_name = strtoupper(trim($_POST['sub_name'])); // Uppercase for consistency
 
      $index_id = $_POST['index_id'] ?? null; // Only for updates



    try {
        if ($action === 'update') {
            // Prepare update statement
            $sql = "UPDATE god SET god_name = ?, sub_name = ? WHERE index_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("ssi", $god_name, $sub_name, $index_id);
        } elseif ($action === 'insert') {
            // Prepare insert statement
            $sql = "INSERT INTO god (god_name, sub_name) VALUES (?, ?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("ss", $god_name, $sub_name);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid operation or missing ID.']);
            exit;
        }

        if ($stmt->execute()) {
            $message = ($action === 'update') ? 'God updated successfully.' : 'God added successfully.';
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










