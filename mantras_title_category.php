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

$select = "SELECT * FROM `mantras_title` WHERE index_id='$id'";

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

    .border-mantras-title{

        border: solid 2px #ff8776;

    }

    @media (max-width: 768px) {

    .mx-auto {

        margin-left: auto !important;

        margin-right: auto !important;

        

    }



    .card {

        width: 100%; /* Make cards take full width on mobile */

    }

    .border-mantras-title{

        border: solid 2px #ff8776;

    }

}





</style>



<div class="py-3 py-xl-5 bg-gradient">

<div class="container" id="second-section">

    <div id="content-container">

        <div class="row">

        <aside class="col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end ">

                <div class="sidebar-filters js-sidebar-filters-mobile">

                    <!-- filter header -->

                    <div class="border-bottom d-flex justify-content-between align-items-center p-3 sidebar-filters-header d-xl-none">

                        <div class="align-items-center btn-icon d-flex filter-close justify-content-center rounded-circle">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewbox="0 0 16 16">

                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"></path>

                            </svg>

                        </div>

                        <!-- <span class="fs-3 fw-semibold">Filters</span> -->

                        <span class="text-primary fw-medium" onclick="clearPage()">Clear</span>

                    </div>

                    <!-- end /. filter header -->

                    <div class="sidebar-filters-body p-3 p-xl-4">

<ul class="nav nav-tabs mb-2" id="sidebarTab" role="tablist">

                                    <li class="nav-item" role="presentation">

                                        <a class="nav-link text-dark fw-bolder fs-5 active" id="god-tab" data-bs-toggle="tab" href="#godTab" role="tab" aria-controls="god" aria-selected="false" tabindex="-1">God</a>

                                    </li>

                                    <li class="nav-item" role="presentation">

                                        <a class="nav-link text-dark fw-bolder fs-5" id="mantra-tab" data-bs-toggle="tab" href="#mantraTab" role="tab" aria-controls="mantra" aria-selected="false" tabindex="-1">Mantras</a>

                                    </li>

                                </ul>

<div class="tab-content">                             

<div class="tab-pane fade show active" id="godTab" role="tabpanel" aria-labelledby="god-tab">

<!-- Start Accordion for Mantras Categories -->

<?php

$select = "SELECT * FROM mantras_category ORDER BY title ASC";

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



// Check if any categories are returned

if (mysqli_num_rows($SQL_STATEMENT) > 0) {

    $accordionIndex = 0; // To uniquely target each accordion section

    while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

        $title = $Row['title'];

        $god_id = $Row['index_id'];

        $accordionIndex++;

?>

        <div class="accordion mb-3" id="filterAccordion<?php echo $accordionIndex; ?>">

            <div class="accordion-item">

                <h2 class="accordion-header" id="headingFilter<?php echo $accordionIndex; ?>">

                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter<?php echo $accordionIndex; ?>" aria-expanded="false" aria-controls="collapseFilter<?php echo $accordionIndex; ?>">

                        <?php echo $title; ?>

                    </button>

                </h2>

                <div id="collapseFilter<?php echo $accordionIndex; ?>" class="accordion-collapse collapse" aria-labelledby="headingFilter<?php echo $accordionIndex; ?>" data-bs-parent="#filterAccordion<?php echo $accordionIndex; ?>">



                    <!-- Start Subcategory Accordion for each category -->

                    <?php

                    // Fetch subcategories based on the current category

                    $select_subcategory = "SELECT * FROM mantras_subcategory WHERE categories_id = $god_id ORDER BY title ASC";

                    $SQL_STATEMENT_subcategory = mysqli_query($DatabaseCo->dbLink, $select_subcategory);



                    // Check if any subcategories are returned

                    if (mysqli_num_rows($SQL_STATEMENT_subcategory) > 0) {

                        while ($Row_subcategory = mysqli_fetch_assoc($SQL_STATEMENT_subcategory)) {

                            $title2 = htmlspecialchars($Row_subcategory['title'], ENT_QUOTES, 'UTF-8'); // Secure output

                            $god_id2 = $Row_subcategory['index_id'];

                    ?>

                            <!-- Displaying each subcategory with checkbox -->

                            <div class="accordion-body">

                                <div class="form-check">

                                    <input class="form-check-input" type="checkbox" value="<?php echo $god_id2; ?>" id="god<?php echo $god_id2; ?>">

                                    <label class="form-check-label" for="god<?php echo $god_id2; ?>"><?php echo $title2; ?></label>

                                </div>

                            </div>

                    <?php

                        }

                    } else {

                        echo "<p class='text-center'>No Mantras or Stotras found in this category.</p>";

                    }

                    ?>



                    <!-- End Subcategory Accordion -->

                </div>

            </div>

        </div>

<?php

    }

} else {

    echo "<p class='text-center'>No Mantras or Stotras found.</p>";

}

?>

<!-- End Accordion for Mantras Categories -->

</div>

<div class="tab-pane fade" id="mantraTab" role="tabpanel" aria-labelledby="mantra-tab">

                    <!-- Start Subcategory Accordion for each category -->

                    <?php

                    // Fetch subcategories based on the current category

                    $select_subcategory = "SELECT * FROM mantras_title ORDER BY title ASC";

                    $SQL_STATEMENT_subcategory = mysqli_query($DatabaseCo->dbLink, $select_subcategory);



                    // Check if any subcategories are returned

                    if (mysqli_num_rows($SQL_STATEMENT_subcategory) > 0) {

                        while ($Row_subcategory = mysqli_fetch_assoc($SQL_STATEMENT_subcategory)) {

                            $title2 = htmlspecialchars($Row_subcategory['title'], ENT_QUOTES, 'UTF-8'); // Secure output

                            $god_id2 = $Row_subcategory['index_id'];

                    ?>

                            <!-- Displaying each subcategory with checkbox -->

                            <div class="accordion-body">

                                <div class="form-check">

                                    <input class="form-check-input" type="radio" name="mantraTitl" value="<?php echo $god_id2; ?>" id="mantras<?php echo $god_id2; ?>">

                                    <label class="form-check-label" for="mantras<?php echo $god_id2; ?>"><?php echo $title2; ?></label>

                                </div>

                            </div>

                    <?php

                        }

                    } else {

                        echo "<p class='text-center'>No Mantras or Stotras found in this category.</p>";

                    }

                    ?>

</div>







                        <!-- start apply button -->

                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->

                        <!-- end /. apply button -->

                        <!-- start clear filters -->

                        <a href="mantras_title_category.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewbox="0 0 16 16">

                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"></path>

                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"></path>

                            </svg>

                            Clear filters

                        </a>

                        <!-- end /. clear filters -->

                    </div>

                </div>

            </aside>



            <div class="col-xl-9 col-lg-9 ps-lg-4 ps-xl-5 sidebar">

                <div class="flex-wrap align-items-center mb-3 gap-2">

                  <div class="d-flex flex-wrap align-items-center mb-3 gap-2">

                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Mantras & Stotras</div>

                </div>

                    <div class="row" id="mantras-stotras-section">



<?php

// Fetch subcategories based on the current category

$select_subcategory = "SELECT * FROM mantras_stotras WHERE mantras_title = '$id' ORDER BY title ASC";

$SQL_STATEMENT_subcategory = mysqli_query($DatabaseCo->dbLink, $select_subcategory);



// Check if any subcategories are returned

if (mysqli_num_rows($SQL_STATEMENT_subcategory) > 0) {

    while ($Row_subcategory = mysqli_fetch_assoc($SQL_STATEMENT_subcategory)) {

        $title2 = htmlspecialchars($Row_subcategory['title'], ENT_QUOTES, 'UTF-8'); // Secure output

        $god_id2 = $Row_subcategory['index_id'];

?>

<div class="col-4 mb-2">

    <a href="mantras_title_details.php?id=<?php echo $god_id2; ?>" class="text-decoration-none">

        <div class=" border-4 border-mantras-title rounded p-3 text-center text-dark" style="cursor: pointer; font-size:17px;">

            <?php echo $title2; ?>

        </div>

    </a>

</div>



<?php

    }

} else {

    echo "<p class='text-center'>No Mantras or Stotras found in this category.</p>";

}

?>

</div>

                </div>

          



               

<div id="listings-container" class="d-flex flex-wrap gap-2">

  <!-- Card 1 -->



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














<?php include('./include/footer.php'); ?>


<script>

// Fetch and display initial mantras/stotras data on page load

/* ------------------------------------
   LOAD INITIAL MANTRAS DATA (JQUERY)
------------------------------------ */
function loadInitialMantrasData() {

    let $section = $('#mantras-stotras-section');
    let $row = $section.find('.row.g-3');

    // Data coming from PHP
    let mantrasData = <?php echo json_encode($mantrasData); ?>;

    $row.empty(); // Clear old data

    if (mantrasData.length > 0) {
        $.each(mantrasData, function (_, item) {
            $row.append(`
                <div class="col-md-4">
                    <div class="border border-warning rounded p-3 text-center">
                        ${item.title}
                    </div>
                </div>
            `);
        });
    } else {
        $row.html("<p class='text-center'>No Mantras or Stotras found in this category.</p>");
    }
}


/* ------------------------------------
   TOGGLE BETWEEN LISTINGS & MANTRAS
------------------------------------ */
function toggleViewBasedOnSelection(e) {

    let mantrasSelected = $('[id^="mantras"]:checked').length > 0;

    if (mantrasSelected) {
        $('#listings-container').hide();
        $('#mantras-stotras-section').show();
    } else {
        $('#mantras-stotras-section').hide();
        $('#listings-container').show();
        fetchListings(); // Load listings
    }
}


/* ------------------------------------
   FETCH LISTINGS (JQUERY AJAX)
------------------------------------ */
function fetchListings(page = 1) {

    let selectedFilters = $('.form-check-input:checked')
        .map(function () { return this.value; })
        .get()
        .join(',');

    $.ajax({
        url: 'fetch_listings.php',
        type: 'POST',
        data: {
            page: page,
            selectedFilters_mantras_title: selectedFilters
        },
        success: function (res) {
            let response = JSON.parse(res);
            $('#listings-container').html(response.listings);
            $('#paginationControls').html(response.pagination);
        }
    });
}


/* ------------------------------------
   REDIRECT FOR MANTRAS SELECTION
------------------------------------ */
function redirectToAnotherPage() {

    let selectedFilters = $('[id^="mantras"]:checked')
        .map(function () { return this.value; })
        .get()
        .join(',');

    let baseUrl = 'mantras_title_category.php';

    window.location.href = `${baseUrl}?id=${selectedFilters}`;
}


/* ------------------------------------
   ON CHECKBOX CHANGE (GLOBAL)
------------------------------------ */
$(document).on('change', '.form-check-input', function (e) {

    e.preventDefault();

    let isMantra = $(this).attr('id')?.startsWith('mantras');

    toggleViewBasedOnSelection();

    // If mantra category selected → redirect
    if (isMantra && $('[id^="mantras"]:checked').length > 0) {
        redirectToAnotherPage();
    }
});


/* ------------------------------------
   INITIAL PAGE LOAD
------------------------------------ */
$(document).ready(function () {
    loadInitialMantrasData();
    toggleViewBasedOnSelection();
});

</script>