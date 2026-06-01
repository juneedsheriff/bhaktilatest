<?php
// Include your database connection class or configuration
include_once './class/databaseConn.php'; // Update this path as necessary

// Set the response Content-Type to JSON
header('Content-Type: application/json');

// Initialize the database connection
$DatabaseCo = new DatabaseConn();

// Helper function to handle the update operation
function updateTableStatus($dbLink, $table, $idField, $id, $statusField, $status, $allowedStatuses)
{
    // Check if the status value is valid
    if (!in_array($status, $allowedStatuses)) {
        return ['success' => false, 'message' => 'Invalid status value.'];
    }

    // Prepare the SQL query
    $stmt = $dbLink->prepare("UPDATE $table SET $statusField = ? WHERE $idField = ?");
    if (!$stmt) {
        return ['success' => false, 'message' => 'Prepare failed: ' . $dbLink->error];
    }

    // Bind parameters and execute
    $stmt->bind_param('si', $status, $id);
    if (!$stmt->execute()) {
        $stmt->close();
        return ['success' => false, 'message' => 'Execute failed: ' . $stmt->error];
    }

    // Check affected rows
    $response = $stmt->affected_rows > 0
        ? ['success' => true, 'message' => 'Status updated successfully.']
        : ['success' => false, 'message' => 'No changes made or record not found.'];

    $stmt->close();
    return $response;
}

// Check for `temples` table update
if (!empty($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']); // Sanitize ID
    $status = trim($_POST['status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved', 'new', 'rejected'];

    // Perform the update for the `temples` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'temples',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Check for `abroad` table update
} elseif (!empty($_POST['abroad_id']) && isset($_POST['abroad_status'])) {
    $id = intval($_POST['abroad_id']); // Sanitize ID
    $status = trim($_POST['abroad_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved', 'rejected']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'abroad',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
} elseif (!empty($_POST['iconic_id']) && isset($_POST['iconic_status'])) {
    $id = intval($_POST['iconic_id']); // Sanitize ID
    $status = trim($_POST['iconic_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'iconic',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
} elseif (!empty($_POST['mantras_id']) && isset($_POST['mantras_status'])) {
    $id = intval($_POST['mantras_id']); // Sanitize ID
    $status = trim($_POST['mantras_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'mantras_subcategory',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
}
elseif (!empty($_POST['mantras_id']) && isset($_POST['mantras_status'])) {
    $id = intval($_POST['mantras_id']); // Sanitize ID
    $status = trim($_POST['mantras_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'mantras_subcategory',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
}


elseif (!empty($_POST['others_id']) && isset($_POST['others_status'])) {
    $id = intval($_POST['others_id']); // Sanitize ID
    $status = trim($_POST['others_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'other_page',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
}
elseif (!empty($_POST['iconic_temple_id']) && isset($_POST['iconic_temple_status'])) {
    $id = intval($_POST['iconic_temple_id']); // Sanitize ID
    $status = trim($_POST['iconic_temple_status']); // Sanitize status
    $allowedStatuses = ['approved', 'unapproved', 'rejected']; // Define allowed statuses

    // Perform the update for the `abroad` table
    $response = updateTableStatus(
        $DatabaseCo->dbLink,
        'iconic_temples',
        'index_id',
        $id,
        'status',
        $status,
        $allowedStatuses
    );

    echo json_encode($response);

// Handle invalid input
}

else {
    echo json_encode(['success' => false, 'message' => 'Invalid input. ID and status are required.']);
}
?>
