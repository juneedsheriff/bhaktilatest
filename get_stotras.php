<?php
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();// Include your database connection
if (isset($_GET['god_id'])) {
    $god_id = intval($_GET['god_id']); // Sanitize the input

    // Fetch related stotras based on god_id
    $select = "SELECT * FROM `mantras_stotras` WHERE sub_category = $god_id";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
            $title = htmlspecialchars($Row['title']);
            $index_id = htmlspecialchars($Row['index_id']);
            echo '<div class="form-check mb-2 god-' . $god_id . '">';
            echo '<input class="form-check-input stotra-checkbox" type="checkbox" id="stotra-' . $index_id . '" data-index-id="' . $index_id . '">';
            echo '<label class="form-check-label" for="stotra-' . $index_id . '">' . $title . '</label>';
            echo '</div>';
        }
    } else {
        echo '<p>No stotras found for this god.</p>';
    }
}
?>
