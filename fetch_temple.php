<?php
error_reporting(0);

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

$searchTerm = $xssClean->clean_input($_POST['query']);
$searchTerm = strtolower($searchTerm);
$likeTerm = '%' . $searchTerm . '%';

// Unified query to search in multiple tables
$query = "
    (SELECT index_id, title, 'temples' AS source 
     FROM temples 
     WHERE LOWER(title) LIKE ?)
    UNION
    (SELECT index_id, title, 'abroad' AS source 
     FROM abroad 
     WHERE LOWER(title) LIKE ?)
    UNION
    (SELECT index_id, title, 'mantras_subcategory' AS source 
     FROM mantras_subcategory 
     WHERE LOWER(title) LIKE ?)
    UNION
    (SELECT index_id, title, 'iconic' AS source 
     FROM iconic 
     WHERE LOWER(title) LIKE ?)
";

// Prepare statement
$stmt = $DatabaseCo->dbLink->prepare($query);

// Check if the query is prepared successfully
if ($stmt === false) {
    die('Query preparation failed: ' . $DatabaseCo->dbLink->error);
}

// Bind parameters for the LIKE search term
$stmt->bind_param('ssss', $likeTerm, $likeTerm, $likeTerm, $likeTerm);

// Execute the query
$stmt->execute();

// Get results
$result = $stmt->get_result();

// Check if there are any results
$resultsFound = false; // Flag to check if results are found

// Function to display the results dynamically
function displayResults($result) {
    global $resultsFound;
    while ($row = $result->fetch_assoc()) {
        $resultsFound = true; // Set flag to true when results are found
        $link = '';
        // Determine the correct link based on the source
        switch ($row['source']) {
            case 'temples':
                $link = 'temple-details.php?id=' . $row['index_id'];
                break;
            case 'abroad':
                $link = 'abroad-details.php?id=' . $row['index_id'];
                break;
            case 'mantras_subcategory':
                $link = 'mantras-details.php?id=' . $row['index_id'];
                break;
            case 'iconic':
                $link = 'iconic-category-details.php?id=' . $row['index_id'];
                break;
        }
        echo '<a href="' . $link . '" class="list-group-item list-group-item-action border-1" >'
            . htmlspecialchars($row['title']) . '</a>';
    }
}

// Display the results
displayResults($result);

// If no results are found
if (!$resultsFound) {
    echo '<p class="list-group-item border-1">No results found.</p>';
}

// Close the statement
$stmt->close();
?>
