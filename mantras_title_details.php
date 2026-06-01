<?php
error_reporting(0);
include('./include/header.php');

// Include required classes
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$id = $xssClean->clean_input($_REQUEST['id']);

// Fetch temple details for the provided id
$select = "SELECT * FROM `mantras_stotras` WHERE index_id='$id'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    echo "<p>No Records.</p>";
    exit;
}


$select = "SELECT * FROM `mantras_subcategory` WHERE index_id='$Row->sub_category'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row1 = mysqli_fetch_object($SQL_STATEMENT);
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
</style>
<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">
    <!-- <h1 class="h2 page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title; ?></h1> -->
    <div class=" overflow-hidden position-relative">
        <div class="row">
            <div class="col-md-12">
                <a class="d-block position-relative" href="#">
                    <img class="w-100" src="app/uploads/gods/banner/<?php echo $Row1->banner; ?>" class="img-fluid" alt="Temple Image">
                </a>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container py-4" id="second-section">
    <div id="content-container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <div class="card border-0 p-4 text-center">
                <h3 class="fw-bold text-primary font-caveat page-header-title  mb-3"><?= htmlspecialchars($Row->title, ENT_QUOTES, 'UTF-8') ?></h3>
                    <?php 
                    if (!empty($audio)) {
                            echo '<div style="width: 300px; height: 50px;  display: inline-block; text-align: center; padding: 10px;">';
                            echo '<audio controls style="width: 100%;">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($Row->audio, ENT_QUOTES, 'UTF-8') . '" type="audio/ogg">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($Row->audio, ENT_QUOTES, 'UTF-8') . '" type="audio/mpeg">';
                            echo 'Your browser does not support the audio element.';
                            echo '</audio>';
                            echo '</div>';
                        }?>
                    <p class=" sth-text text-dark "><?= $Row->content?></p>
                </div>
                <div class="justify-content-center d-flex">
                <img src="./assets/images/imges1.png" alt="" class="img-fluid">
                </div>
            
            </div>
          
        </div>

    </div>
</div>

<div class="container my-5">
    <h3 class="text-center fw-bold  page-header-title " style="font-size: 24px;">You May Also Like</h3>
    <div class="row justify-content-center g-4">
        <?php
        // Fetch all data from temples table with limit and offset
        $select = "SELECT * FROM `mantras_subcategory` WHERE index_id !='0' ORDER BY index_id DESC LIMIT 3";
        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

        // Check if any rows are returned
        if (mysqli_num_rows($SQL_STATEMENT) > 0) {
            while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                $photos = $Row['photos'];
                $title = $Row['title'];
                $index_id = $Row['index_id'];
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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


<?php include('./include/footer.php'); ?>