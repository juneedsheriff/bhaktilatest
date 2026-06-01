<?php
include_once './class/databaseConn.php';
include_once 'lib/requestHandler.php';
include_once './class/fileUploader.php';
$DatabaseCo = new DatabaseConn();
$response = ['successStatus' => false, 'responseMessage' => ''];
error_reporting(1);



// Include your database connection
include_once 'database_connection.php'; // Adjust the path to your actual database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $title = trim($_POST['title']);
    $index_id = $_POST['index_id'] ?? null;

    try {
        if ($action === 'update' && $index_id) {
            // Update title in the gallery
            $sql = "UPDATE gallery SET title = ? WHERE index_id = ?";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("si", $title, $index_id);

            if ($stmt->execute()) {
                // Process file upload if photos are provided
                if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                    // Fetch existing images
                    $query = "SELECT photos FROM gallery WHERE index_id = ?";
                    $stmt = $DatabaseCo->dbLink->prepare($query);
                    $stmt->bind_param("i", $index_id);
                    $stmt->execute();
                    $stmt->bind_result($existing_images);
                    $stmt->fetch();
                    $stmt->close();

                    // Convert existing images to an array and track unique entries
                    $existing_images_array = !empty($existing_images) ? explode(',', $existing_images) : [];
                    $unique_images = array_flip($existing_images_array);

                    // Upload new images
                    $imageUploader = new ImageUploaderMultiple($pdo); // Assuming $pdo is defined
                    $uploadedImages = $imageUploader->uploadMultiple($_FILES['photos'], 'gallery');

                    // Merge unique new images
                    if ($uploadedImages) {
                        foreach ($uploadedImages as $uploaded_image) {
                            $unique_images[$uploaded_image] = true; // Add new unique image
                        }
                    }

                    // Update the images in the database
                    $gallery_images_str = implode(',', array_keys($unique_images));
                    $update_query = "UPDATE gallery SET photos = ? WHERE index_id = ?";
                    $stmt = $DatabaseCo->dbLink->prepare($update_query);
                    $stmt->bind_param("si", $gallery_images_str, $index_id);

                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'message' => 'Gallery updated successfully.', 'redirect' => true]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to update gallery images: ' . $stmt->error]);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => true, 'message' => 'Title updated successfully.', 'redirect' => true]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update title: ' . $stmt->error]);
            }
        } elseif ($action === 'insert') {
            // Insert a new gallery record
            $sql = "INSERT INTO gallery (title) VALUES (?)";
            $stmt = $DatabaseCo->dbLink->prepare($sql);
            $stmt->bind_param("s", $title);

            if ($stmt->execute()) {
                $inserted_id = $stmt->insert_id; // Get the ID of the newly inserted record

                // Handle file uploads if photos are provided
                if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
                    $imageUploader = new ImageUploaderMultiple($pdo);
                    $uploadedImages = $imageUploader->uploadMultiple($_FILES['photos'], 'gallery');

                    if ($uploadedImages) {
                        $gallery_images_str = implode(',', $uploadedImages);
                        $update_query = "UPDATE gallery SET photos = ? WHERE index_id = ?";
                        $stmt = $DatabaseCo->dbLink->prepare($update_query);
                        $stmt->bind_param("si", $gallery_images_str, $inserted_id);
                        $stmt->execute();
                    }
                }

                echo json_encode(['success' => true, 'message' => 'Gallery added successfully.', 'redirect' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add gallery: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action or missing index ID.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}


// Assuming your database connection is available as $dbLink

// Make sure this points to your database configuration file


?>










