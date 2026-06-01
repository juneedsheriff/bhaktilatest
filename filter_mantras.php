<?php
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

if (isset($_GET['stotra_id'])) {
    $stotra_id = intval($_GET['stotra_id']); // Sanitize the input

    // Fetch stotra details based on stotra_id
    $select = "SELECT * FROM `mantras_stotras` WHERE index_id = $stotra_id";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    if ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
        $audio = htmlspecialchars($Row['audio']);
        $title = htmlspecialchars($Row['title']);
        $content = $Row['content']; // Sanitize content
        echo '<div class="">';
        echo '<div id="content-' . $stotra_id . '" class="content-section shadow p-3 mb-4 bg-body rounded border">';


        echo '<h3 class="fs-5 fw-semibold mb-0 font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary mt-2">' . $title . '</h3>';
        echo '<span class="mt-1 col-12 text-dark">' . $Row['content'] . '</span>';
        echo '</div>';
        echo '</div>';
    }
}
