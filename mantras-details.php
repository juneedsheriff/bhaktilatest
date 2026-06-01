<?php
error_reporting(0);
include('./include/header.php');

// Include required classes
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$mantras_id = $xssClean->clean_input($_REQUEST['id']);
$title_id = $xssClean->clean_input($_REQUEST['title_id']);

// Fetch temple details for the provided id
$select = "SELECT * FROM `mantras_subcategory` WHERE index_id='$mantras_id'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    echo "<p>No Records.</p>";
    exit;
}
?>
<style>
    /* Highlight animation */
    .card.highlight {
        background-color: #f0f8ff;
    }

    .tab-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .tab-container button {
        padding: 10px 20px;
        cursor: pointer;
        border: none;
        background-color: #ddd;
        border-radius: 5px;
    }

    .tab-container button:hover {
        background-color: #ccc;
    }

    .align {
        margin-left: 5%;
    }

    .next-gods-container {
        display: flex;
        gap: 15px;
        justify-content: center;
    }

    .next-gods-container .card {
        width: 200px;
    }

    .social-media {
        display: flex;
        justify-content: center;
        margin-top: -92px;
        margin-bottom: 34px;
    }

    .a1 {
        display: flex;
        background: #e3edf7;
        height: 55px;
        width: 55px;
        margin: 0 15px;
        border-radius: 8px;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 6px 6px 10px -1px rgba(0, 0, 0, 0.15),
            -6px -6px 10px -1px rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0);
        transition: transform 0.5s;
    }

    .a1 i {
        font-size: 25px;
        color: #777;
        transition: transform 0.5s;
    }

    .a1:hover {
        box-shadow: inset 4px 4px 6px -1px rgba(0, 0, 0, 0.2),
            inset -4px -4px 6px -1px rgba(255, 255, 255, 0.7),
            -0.5px -0.5px 0px rgba(255, 255, 255, 1),
            0.5px 0.5px 0px rgba(0, 0, 0, 0.15), 0px 12px 10px -10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.01);
        transform: translateY(2px);
    }

    @media (max-width: 768px) {
        .social-media {
            display: flex;
            justify-content: center;
            margin-top: -18px;
            margin-bottom: 34px;
        }
    }

    .line {
        border: 1px solid black;
    }

    /* Container styling */
    .audio-container {
        display: inline-block;
        padding: 10px 40px;
        background-color: #f3f4f6;
        border-radius: 30px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    /* Audio player default styles */
    audio {
        outline: none;
        width: 20%;
    }
    @media(max-width: 768px) {
        audio {
            outline: none;
            width: 100%;
        }

    }
    /* Additional styling for play/pause button, volume, and timeline */
    audio::-webkit-media-controls-play-button,
    audio::-webkit-media-controls-volume-slider,
    audio::-webkit-media-controls-timeline {
        background-color: #f3f4f6;
        border-radius: 5px;
    }
    /* Hide the download button for a cleaner look */
    audio::-webkit-media-controls-download-button {
        display: none;
    }
    .hover-content {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        background-color: #f9f9f9;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .hover-content:hover {
        background-color: #e0f7fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .hover-content i {
        font-size: 1.5rem;
        /* Adjust icon size */
        color: #ff8776 !important;
        /* Icon color */
    }

    .hover-content span {
        font-size: 1rem;
        /* Adjust text size */
        font-weight: 500;
        /* Adjust text weight */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .row {
        margin-bottom: 15px;
        /* Add spacing between rows */
    }

    .hover-content {
        display: flex;
        align-items: center;
        /* Center the content vertically */
        justify-content: flex-start;
        /* Align content to the left */
        background-color: transparent;
        transition: background-color 0.3s ease, padding 0.3s ease;
        cursor: pointer;
        border-radius: 5px;
        /* Optional for rounded corners */
        width: 100%;
    }

    /* On hover, change background color and add padding */
    .hover-content:hover {
        background-color: #ff8776 !important;
        /* Change to desired background color */
        padding: 10px;
        /* Increase padding on hover */
    }

    /* Add margin to the icon for spacing */
    .fas.fa-bahai {
        margin-right: 8px;
        /* Adjust space between icon and text */
    }

    /* Responsive Design: Adjust padding on smaller screens */
    @media (max-width: 767px) {
        .hover-content {
            padding: 10px;
        }
    }

    @media (max-width: 575px) {
        .hover-content {
            padding: 8px;
        }
    }
</style>
<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">
    <div class="overflow-hidden position-relative  banner-over-container">
                    <img class="w-100 banner-h-420" src="app/uploads/gods/banner/<?php echo $Row->banner; ?>" class="img-fluid" alt="Temple Image" style="object-position: top;">
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title; ?></h1>
    </div>
</div>

<div class="container-fluid " id="first-section">
    <?php
    // Fetch all data from temples table
    $select = "SELECT * FROM `mantras_stotras` WHERE sub_category='$mantras_id'";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    // Check if any rows are returned
    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        $counter = 0; // Initialize counter to track items per row
        $isFirst = true; // Flag to identify the first iteration
        echo '<div class="row ">'; // Start the first row

        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
            $title = htmlspecialchars($Row['title']);
            $index_id = $Row['index_id'];

            // Display each title as a button in a column
            echo '<div class="col-md-3 col-sm-6 mb-1">';
            echo '<span class="hover-content p-2 d-flex align-items-center" style="cursor: pointer;" ';
            echo 'onclick="showContent(' . htmlspecialchars($index_id) . ')"';

            // Add a check to call showContent() for the first item
            if ($isFirst) {
                echo ' id="defaultContent">';
                echo '<script>document.addEventListener("DOMContentLoaded", function() { showContent(' . htmlspecialchars($index_id) . '); });</script>';
                $isFirst = false;
            } else {
                echo '>';
            }

            // Add the icon and title
            echo '<i class="fas fa-gopuram me-2" style="font-size: 1.2rem;"></i>'; // Icon
            echo '<span class="text-truncate">' . $title . '</span>'; // Title
            echo '</span>';
            echo '</div>';

            $counter++;

            // After 4 items, close the row and start a new one
            if ($counter % 4 === 0) {
                echo '</div><div class="row">';
            }
        }

        echo '</div>'; // Close the last row
    } else {
        echo "<p class='text-center'>No Song found.</p>";
    }
    ?>


</div>
<div class="justify-content-center d-flex">
    <img src="./assets/images/imges1.png" alt="" class="img-fluid">
</div>
<div class="container " id="second-section">
    <div id="content-container" class="container">
        <div class="row">
                <div id="mantras_content">
                    <?php
                    // Fetch all data from temples table
                    $select = "SELECT * FROM `mantras_stotras` WHERE sub_category='$mantras_id'";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    mysqli_data_seek($SQL_STATEMENT, 0);
                    while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                        $audio = htmlspecialchars($Row['audio']);
                        $title = htmlspecialchars($Row['title']);
                        $content = $Row['content'];
                        $meaning = $Row['meaning'];
                        $index_id = htmlspecialchars($Row['index_id']);

                        echo '<div id="content-' . $index_id . '" class="content-section text-center" style="display: none;">';

                        // Check if audio exists
                        if (!empty($audio)) {
                            echo '<div style="width: 300px; height: 50px;  display: inline-block; text-align: center; padding: 10px;">';
                            echo '<audio controls style="width: 100%;">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($audio, ENT_QUOTES, 'UTF-8') . '" type="audio/ogg">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($audio, ENT_QUOTES, 'UTF-8') . '" type="audio/mpeg">';
                            echo 'Your browser does not support the audio element.';
                            echo '</audio>';
                            echo '</div>';
                        }


                        // Always display title and content
                        echo '<h3 class="text-center font-caveat page-header-title fw-semibold m-2 pb-3  text-primary  fw-semibold mb-0">' . $title . '</h3>';
                        echo '<p class=" mt-3 col-4 text-dark text-center" align="center">' . $content . '</p>';
                        // echo '<h3 class=" page-header-title fw-semibold m-2 pb-3  text-primary mt-3 col-4">Meaning</h3>';
                        // echo '<p class="mt-3 col-4">' . $meaning . '</p>';

                        echo '</div>';
                    }
                    ?>
                </div>
    </div>
                <div class="row">
                    <div id="mantras-stotras-section" class="d-flex flex-wrap gap-2">
                    </div>
                    <div id="listings-container" class="d-flex flex-wrap gap-2">
                    </div>
                </div>

                <div class="container my-5 you_may" id="you_may">
                    <h3 class="text-center fw-bold fs-5 card-title page-header-title ">You May Also Like</h3>
                    <div class="row justify-content-center g-4">
                        <?php
                        // Fetch all data from temples table with limit and offset
                        $select = "SELECT * FROM `mantras_subcategory` WHERE index_id !='0' ORDER BY index_id DESC LIMIT 2";
                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                        // Check if any rows are returned
                        if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                            while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                                $photos = $Row['photos'];
                                $title = $Row['title'];
                                $index_id = $Row['index_id'];
                        ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                    <div class="card shadow-sm h-100">
                                        <a href="mantras-details.php?id=<?php echo $index_id; ?>" >
                                            <img src="app/uploads/gods/<?php echo $photos; ?>" class="card-img-top img-fluid" alt="<?php echo $title; ?>" style="height: 300px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <a href="mantras-details.php?id=<?php echo $index_id; ?>"  class="text-decoration-none">
                                                <h5 class="card-title text-dark fw-bold" style="font-size: 18px;"><?php echo $title; ?></h5>
                                            </a>
                                            <p class="card-text text-dark mb-0">
                                                <a href="mantras-details.php?id=<?php echo $index_id; ?>"  class="btn  p-0">Read more</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p class='text-center text-muted'>No temples found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function showContent(indexId) {
        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show the selected content section
        const content = document.getElementById(`content-${indexId}`);
        if (content) {
            content.style.display = 'block';
        }
    }

    function filterContent() {
        // Get all checkboxes
        const checkboxes = document.querySelectorAll('.mantras');
        let found = false;

        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show content for checked boxes
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const indexId = checkbox.value;
                const content = document.getElementById(`content-${indexId}`);
                if (content) {
                    content.style.display = 'block';
                    found = true;
                }
            }
        });

        // If no checkbox is checked, you can show a default message or do nothing
        if (!found) {
            console.log("No content to display.");
        }
    }
</script>
<script>
    function showContent(index_id) {
        console.log("Loading content for index_id: " + index_id); // Debugging output

        // Hide all content sections
        const allContents = document.querySelectorAll('.content-section');
        allContents.forEach(content => content.style.display = 'none');

        // Show the selected content section
        const selectedContent = document.getElementById('content-' + index_id);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        } else {
            console.error("Content for index_id " + index_id + " not found.");
        }
    }
</script>
<script>
    // JavaScript to handle checkbox clicks
    document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const indexId = this.getAttribute('data-index-id');

            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');

            // Show selected content if checkbox is checked
            if (this.checked) {
                document.getElementById('content-' + indexId).style.display = 'block';

                // Uncheck all other checkboxes
                document.querySelectorAll('.filter-checkbox').forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Set default active content (id = 1)
        const defaultContent = document.getElementById('content-1');
        if (defaultContent) {
            defaultContent.style.display = 'block';
        }
    });

    function downloadAllContentAsPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        let yPosition = 10; // Initial vertical position in the PDF

        // Fetch all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            const title = section.getAttribute('data-title') || 'Untitled';
            let content = section.getAttribute('data-content') || 'No content available';

            // Strip HTML tags from content using a temporary DOM element
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            content = tempDiv.textContent || tempDiv.innerText || 'No content available';

            // Add title to the PDF
            doc.setFont('Helvetica', 'bold');
            doc.text(title, 10, yPosition);
            yPosition += 10;

            // Add content to the PDF
            doc.setFont('Helvetica', 'normal');
            const contentLines = doc.splitTextToSize(content, 180); // Ensure text fits within page width
            doc.text(contentLines, 10, yPosition);
            yPosition += contentLines.length * 10;

            // Add new page if content exceeds page limit
            if (yPosition > 270) {
                doc.addPage();
                yPosition = 10; // Reset vertical position for the new page
            }
        });

        // Save the PDF
        doc.save('GodContent.pdf');
    }
</script>
<script>
    // Clear all displayed data
    function clearAllData() {
        // Clear content from both sections
        document.getElementById('mantras-stotras-section').innerHTML = '';
        document.getElementById('listings-container').innerHTML = '';
    }

    // Show or hide sections based on the selected checkbox behavior
    function toggleViewBasedOnSelection() {
        const selectedCheckbox = document.querySelector('.form-check-input:checked, .mantras-title:checked');
        const listingsContainer = document.getElementById('listings-container');
        const mantrasStotrasSection = document.getElementById('mantras-stotras-section');
        const mantrasContent = document.getElementById('mantras_content');
        const firstSection = document.getElementById('first-section');
        const youMay = document.getElementById('you_may');

        // Clear any previously displayed data
        clearAllData();

        if (selectedCheckbox) {
            // Show relevant section based on selected checkbox
            if (selectedCheckbox.classList.contains('mantras-title')) {
                // Show mantras/stotras data
                listingsContainer.style.display = 'none';
                mantrasContent.style.display = 'none';
                firstSection.style.display = 'none';
                youMay.style.display = 'none';
                mantrasStotrasSection.style.display = 'block';
                fetchMantrasData();
            } else {
                // Show listings data
                mantrasStotrasSection.style.display = 'none';
                mantrasContent.style.display = 'none';
                firstSection.style.display = 'none';
                youMay.style.display = 'none';
                listingsContainer.style.display = 'block';
                fetchListings();
            }
        } else {
            // Default view if no checkbox is selected
            mantrasContent.style.display = 'block';
            firstSection.style.display = 'block';
            youMay.style.display = 'block';
            listingsContainer.style.display = 'none';
            mantrasStotrasSection.style.display = 'none';
        }
    }

    // Handle single checkbox selection
    function handleSingleCheckboxSelection(event) {
        // Uncheck all other checkboxes to allow only one selection at a time
        const checkboxes = document.querySelectorAll('.form-check-input, .mantras-title');
        checkboxes.forEach(checkbox => {
            if (checkbox !== event.target) {
                checkbox.checked = false;
            }
        });

        // Update the view based on the current selection
        toggleViewBasedOnSelection();
    }

    // Fetch listings data using POST
    function fetchListings(page = 1) {
        const selectedCheckbox = document.querySelector('.form-check-input:checked');
        if (!selectedCheckbox) return;

        const params = new URLSearchParams();
        params.append('page', page);
        params.append('selectedFilters_mantras_title', selectedCheckbox.value);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('listings-container').innerHTML = response.listings;
                if (response.pagination) {
                    document.getElementById('paginationControls').innerHTML = response.pagination;
                }
            } else {
                console.error('Failed to fetch listings');
            }
        };
        xhr.send(params.toString());
    }

    // Fetch Mantras/Stotras data using POST
    function fetchMantrasData() {
        const selectedCheckbox = document.querySelector('.mantras-title:checked');
        if (!selectedCheckbox) return;

        const params = new URLSearchParams();
        params.append('title_id', selectedCheckbox.value);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.listings_1) {
                    document.getElementById('mantras-stotras-section').innerHTML = response.listings_1;
                }
            } else {
                console.error('Failed to fetch Mantras/Stotras data');
            }
        };
        xhr.send(params.toString());
    }

    // Event listeners for checkboxes
    document.querySelectorAll('.form-check-input, .mantras-title').forEach(checkbox => {
        checkbox.addEventListener('change', handleSingleCheckboxSelection);
    });

    // Initial page load handling
    document.addEventListener('DOMContentLoaded', () => {
        toggleViewBasedOnSelection();
    });
</script>

<div class="row">
    <div id="mantras-stotras-section" class="d-flex flex-wrap gap-2"></div>
    <div id="listings-container" class="d-flex flex-wrap gap-2"></div>
</div>






<script>
    // Function to handle checkbox click across groups
    document.querySelectorAll('.mantras-title').forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            // Uncheck all other checkboxes
            document.querySelectorAll('.form-check-input').forEach(cb => {
                if (cb !== this) cb.checked = false;
            });

            // Allow deselecting the currently selected checkbox
            if (this.checked) {
                this.dataset.lastChecked = "true";
            } else {
                delete this.dataset.lastChecked;
            }
        });

        // Handle deselecting on second click
        checkbox.addEventListener('change', function() {
            if (this.dataset.lastChecked) {
                this.checked = false;
                delete this.dataset.lastChecked;
            }
        });
    });
</script>

<script>
    function showContent(indexId) {
        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show the selected content section
        const selectedContent = document.getElementById('content-' + indexId);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }

        // Scroll to the content display section
        const contentDisplay = document.getElementById('content-display');
        if (contentDisplay) {
            contentDisplay.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
</script>


<?php include('./include/footer.php'); ?>