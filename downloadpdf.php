<?php
error_reporting(1);
include('./include/header.php');

// Include required classes
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$id = $xssClean->clean_input($_REQUEST['id']);

// Fetch temple details for the provided id
$select = "SELECT * FROM `mantras_subcategory`";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    echo "<p>Temple not found.</p>";
    exit;
}
?>
<style>
    /* Custom CSS for Audio Players and Content */
/* General styles for the audio player */
.content-section {
    margin-bottom: 30px;
    padding: 15px;
}

.audio-player {
    width: 100%; /* Make audio player responsive */
    max-width: 100%; /* Ensure it does not exceed container width */
    border-radius: 8px; /* Optional: For rounded corners */
    background-color: #f8f9fa; /* Optional: A background color */
}

.content-section h3 {
    font-size: 1.5rem; /* Adjust the font size for readability */
    margin-top: 1rem;
}

/* Make the content responsive */
.content-section span {
    font-size: 1rem;
    line-height: 1.5;
    display: block;
    margin-top: 1rem;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .audio-player {
        width: 100%; /* Ensure the player takes full width on smaller screens */
        max-width: 100%; /* Prevent the player from exceeding container width */
    }

    .content-section h3 {
        font-size: 1.25rem; /* Smaller title font on mobile */
    }

    .content-section span {
        font-size: 0.875rem; /* Smaller content text for better mobile readability */
    }
}

/* Tablet responsiveness */
@media (max-width: 992px) and (min-width: 769px) {
    .audio-player {
        width: 100%;
        max-width: 100%;
    }

    .content-section h3 {
        font-size: 1.375rem; /* Slightly larger title font for tablets */
    }

    .content-section span {
        font-size: 1rem; /* Adjust content text for tablet */
    }
}

/* Desktop responsiveness */
@media (min-width: 1200px) {
    .audio-player {
        width: 100%;
        max-width: 600px; /* Limit the maximum width of the player on larger screens */
    }

    .content-section h3 {
        font-size: 1.75rem; /* Larger title font for desktop */
    }

    .content-section span {
        font-size: 1.125rem; /* Adjust content text for desktop readability */
    }
}
.banner-over-container {
    position: relative;
    display: inline-block;
    width: 100%;
}
.banner-over-title {
    position: absolute;
    bottom: 0;
    left: 0;
    background: rgba(255, 255, 255, 0.25);
    padding: 0 !important;
    text-align: center;
    width: 100%;
    margin: 0 !important;
}
</style>
<div class="container-fluid m-0 p-0 text-center bg-gradient">
    <div class="overflow-hidden position-relative banner-over-container">
        <a class="d-block position-relative" href="#">
            <img class="w-100 banner-h-420" src="assets/images/mantra-banner.png" alt="Mantras">
        </a>
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Download Mantras</h1>
    </div>
</div>
<div class="pdf-export text-lg-end text-center me-lg-5 me-md-1 me-sm-1 mb-3">
    <button class="btn btn-primary" onclick="downloadAllStotrasAsPDF()">
        <i class="fas fa-file-pdf"></i> Download Mantras
    </button>
</div>
<div class="container" id="second-section">
    <div class="row">
        <!-- Left-Side Filter Panel -->
        <div class="col-md-3 col-lg-3 col-12 mb-4 mb-md-0 p-3 filter-panel shadow bg-body rounded">
            <h3 class="fw-bold mb-3 fs-4">Select God</h3>
            <?php
            $select = "SELECT * FROM `mantras_subcategory`";
            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

            while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                $title = htmlspecialchars($Row['title']);
                $index_id = htmlspecialchars($Row['index_id']);
                echo '<div class="form-check mb-2">';
                echo '<input class="form-check-input god-checkbox" type="checkbox" id="god-' . $index_id . '" data-index-id="' . $index_id . '">';
                echo '<label class="form-check-label" for="god-' . $index_id . '">' . $title . '</label>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-3 col-lg-3 col-6">
            <div class=" p-3 mb-4 ">
                <!-- <h4 class="fw-bold mb-3 fs-5">Select Any</h4> -->
                <div id="stotras-checklist"></div>
            </div>
            
        </div>

            <div class="col-md-6 col-lg-6 col-6">
                <div id="stotras-content" class="text-dark"></div>
            </div>
    </div>

    <!-- PDF Export Button -->
    <!-- PDF Export Button -->
   



</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    // Example condition
    const condition = true; // Change this to false to hide

    if (condition && $("#stotras-checklist").html().trim() !== "") {
        $("#bg-body-div").show(); // Show the div
    } else {
        $("#bg-body-div").hide(); // Hide the div
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
async function downloadAllStotrasAsPDF() {
    // Load jsPDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Title for the PDF
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("All Stotras", 10, 10);

    // Get all stotra sections
    const stotraSections = document.querySelectorAll(".content-section");

    if (stotraSections.length === 0) {
        alert("No stotra content available to download.");
        return;
    }

    let yPosition = 20; // Initial y position for content

    // Loop through each stotra section
    stotraSections.forEach((section, index) => {
        // Fetch Title
        const titleElement = section.querySelector("h3");
        const title = titleElement ? titleElement.innerText.trim() : `Stotra ${index + 1}`;

        // Fetch Content
        const contentElement = section.querySelector("span");
        const contentHTML = contentElement ? contentElement.innerHTML : "No content available.";

        // Clean the content from HTML and format it
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = contentHTML.replace(/<br\s*\/?>/g, "\n").replace(/<\/p>/g, "\n");
        const cleanContent = tempDiv.innerText.trim();

        // Add Title to PDF
        if (yPosition > 270) { // Check for page overflow and create a new page if necessary
            doc.addPage();
            yPosition = 10;
        }
        doc.setFont("helvetica", "bold");
        doc.setFontSize(12);
        doc.text(`Title: ${title}`, 10, yPosition);
        yPosition += 10;

        // Add Content to PDF
        doc.setFont("helvetica", "normal");
        const wrappedContent = doc.splitTextToSize(cleanContent, 180); // Wrap content for better layout
        wrappedContent.forEach((line) => {
            if (yPosition > 270) { // Check for page overflow
                doc.addPage();
                yPosition = 10;
            }
            doc.text(line, 10, yPosition);
            yPosition += 6;
        });

        // Add spacing between entries
        yPosition += 10;
    });

    // Save the PDF
    doc.save("All_Stotras.pdf");
}

</script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle clicking a 'God' checkbox
        $('.god-checkbox').change(function() {
            var godId = $(this).data('index-id');
            // const allContents = document.querySelectorAll('.content-section');
            // allContents.forEach(content => content.style.display = 'none');
            if ($(this).prop('checked')) {
                // Load related 'mantras_stotras' checkboxes dynamically based on the selected 'god_id'
                $.ajax({
                    url: 'get_stotras.php',
                    method: 'GET',
                    data: {
                        god_id: godId
                    },
                    success: function(response) {
                        // Add the stotras checkboxes to the stotras checklist div
                        $('#stotras-checklist').append(response);
                    },
                    error: function() {
                        alert('Error loading stotras for this god.');
                    }
                });
            } else {
                // If the God checkbox is unchecked, remove the related stotras checkboxes
                $('#stotras-checklist').find('.god-' + godId).remove();
            }
        });

        // Handle clicking a 'Stotra' checkbox
        $(document).on('change', '.stotra-checkbox', function() {
            var stotraId = $(this).data('index-id');

            if ($(this).prop('checked')) {
                // Fetch the content for the selected stotra
                $.ajax({
                    url: 'filter_mantras.php',
                    method: 'GET',
                    data: {
                        stotra_id: stotraId
                    },
                    success: function(response) {
                        // Display the stotra content (audio, title, and content)
                        $('#stotras-content').append(response);
                    },
                    error: function() {
                        alert('Error loading stotra content.');
                    }
                });
            } else {
                // If the Stotra checkbox is unchecked, remove the content
                $('#content-' + stotraId).remove();
            }
        });
    });
</script>
<?php include('./include/footer.php'); ?>