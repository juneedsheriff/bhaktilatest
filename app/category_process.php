<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];





if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Ensure the database connection is included
    $action = $_POST['action']; // Get the action ('insert' or 'update')

    $name = trim($_POST['name']);
    // $tag_name = strtoupper(trim($_POST['tag_name'])); // Uppercase for consistency
 
      $index_id = $_POST['index_id'] ?? null; // Only for updates



    try {
        if ($action === 'update') {
            // Prepare update statement
            $sql = "UPDATE category SET name = ? WHERE index_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("si", $name, $index_id);
        } elseif ($action === 'insert') {
            // Prepare insert statement
            $sql = "INSERT INTO category (name) VALUES (?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("s", $name,);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid operation or missing ID.']);
            exit;
        }

        if ($stmt->execute()) {
            $message = ($action === 'update') ? 'Category updated successfully.' : 'Category added successfully.';
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










