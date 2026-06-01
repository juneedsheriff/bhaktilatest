<?php

include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';
include_once './class/fileUploader.php';
$DatabaseCo = new DatabaseConn();
$dbLink = $DatabaseCo->dbLink; // Assign the dbLink to a variable
$imageIndex = isset($_REQUEST['imageIndex']) ? intval($_REQUEST['imageIndex']) : null;
$nameIndex = isset($_REQUEST['nameIndex']) ? $_REQUEST['nameIndex'] : null;

// Validate that imageIndex and nameIndex were provided
if ($imageIndex !== null && $nameIndex !== null) {
  // Prepare SQL statement to remove the specific image from the gallery
  $stmt = $dbLink->prepare("UPDATE gallery SET photos = REPLACE(photos, ?, '') WHERE index_id = ?");
  $stmt->bind_param("si", $nameIndex, $imageIndex);

  // Execute the query and check for success
  if ($stmt->execute()) {
    // Fetch the updated gallery images (optional, if you want to return updated image list)
    $stmt = $dbLink->prepare("SELECT photos FROM gallery WHERE index_id = ?");
    $stmt->bind_param("i", $imageIndex);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Return success message and remaining images as JSON
    echo json_encode([
      'status' => 'success',
      'message' => 'Image removed successfully',
      'remainingImages' => explode(',', $row['photos']) // Return remaining images
    ]);
  } else {
    // Error in query execution
    echo json_encode(['status' => 'error', 'message' => 'Failed to update image']);
  }

  // Close statement
  $stmt->close();
} else {
  // Invalid indices received
  echo json_encode(['status' => 'error', 'message' => 'Invalid indices received']);
}


?>