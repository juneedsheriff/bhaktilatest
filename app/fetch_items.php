<?php
include_once './class/databaseConn.php'; // Include database connection

$DatabaseCo = new DatabaseConn(); // Database connection instance

// Check if category_id is set in POST
if (isset($_POST['category_id'])) {
    $categoryId = intval($_POST['category_id']); // Sanitize input

    // Define the query based on the selected category_id
    $query = "SELECT index_id, title FROM mantras_subcategory WHERE categories_id = ? ORDER BY title";

    // Prepare and execute the query
    if ($stmt = $DatabaseCo->dbLink->prepare($query)) {
        $stmt->bind_param('i', $categoryId); // Bind the category_id
        $stmt->execute(); // Execute the query
        $result = $stmt->get_result(); // Get the result set

        // Check if there are results and display the options
        if ($result->num_rows > 0) {
            echo "<option selected disabled>Select an option</option>";
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['index_id']}'>{$row['title']}</option>";
            }
        } else {
            echo '<option disabled>No items available</option>';
        }

        $stmt->close(); // Close the statement
    } else {
        echo '<option disabled>Error executing query</option>';
    }
} else {
    echo '<option disabled>Invalid request</option>';
}

$DatabaseCo->dbLink->close(); // Close the database connection
