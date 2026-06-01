<?php
include_once './app/class/databaseConn.php';
$DatabaseCo = new DatabaseConn();

// Fetch category ID if it exists
$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;

// Default query for fetching all images
$imagesQuery = "SELECT * FROM gallery";
if ($category_id) {
    $imagesQuery .= " WHERE index_id = $category_id";
}

$imagesResult = mysqli_query($DatabaseCo->dbLink, $imagesQuery);
$images = [];

while ($row = mysqli_fetch_assoc($imagesResult)) {
    if (!empty($row['photos'])) {
        $images = array_merge($images, array_filter(explode(',', $row['photos'])));
    }
}

// Generate the HTML for the images


// Set the limit of images to display initially
$limit = 6; // Number of images to display initially
$counter = 0; // Counter to track number of displayed images
$html = '';
// Initialize the HTML string

// Check if images are available
if (!empty($images)) {
    foreach ($images as $image) {
        // Stop displaying more images once the limit is reached
        if ($counter >= $limit) {
            break; // Stop the loop if the counter exceeds the limit
        }

        // Build the image path
        $imagePath = "app/uploads/gallery/" . trim(htmlspecialchars($image));
        
        // Check if the image file exists
        if (file_exists($imagePath)) {
            // Append the image HTML to the $html variable
            $html .= '<div class="col-6 col-md-4 col-lg-3 image-item" onclick="openModal(\'' . $imagePath . '\')">';
            $html .= '<img src="' . $imagePath . '" alt="Gallery Image" class="img-fluid">';
            $html .= '</div>';

            $counter++; // Increment the counter after displaying an image
        }
    }
    
    // Optionally, add a "Load More" button if there are more images
    // if ($counter < count($images)) {
    //     $html .= '<button id="load-more" class="btn btn-primary" onclick="loadMoreImages()">Load More</button>';
    // }

} else {
    // Display message if no images are available
    $html = '<p>Select a category to see images.</p>';
}

// Output the generated HTML




// Return the HTML for the gallery
echo $html;

// Close the database connection
$DatabaseCo->dbLink->close();
?>
