<?php
include('./include/header.php');

error_reporting(1);

// Initialize required classes

// $id = $xssClean->clean_input($_REQUEST['id'] ?? '');
// $Qcount = $DatabaseCo->dbLink->query("SELECT index_id FROM temples");
// $count = mysqli_num_rows($Qcount);
?>
<style>
    .accordion-button {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        font-weight: bold;
    }

    .accordion-button:not(.collapsed) {
        color: #0d6efd;
        background-color: #e7f1ff;
    }

    .accordion-body {
        padding: 15px;
    }
</style>
<!-- end /. header -->
<div class="col-lg-3 col-md-4 col-mg-3 d-xl-none gap-3 gap-md-2 hstack justify-content-center">
    <a href="#" class="sidebarCollapse align-items-center d-flex justify-content-center filters-text fw-semibold gap-2">
        <i class="fa-solid fa-arrow-up-short-wide fs-18"></i>
        <span>All filters</span>
    </a>
</div>
<div class="py-3 py-xl-5 bg-gradient">
    <div class="container">
        <div class="row"> <!-- start sidebar filters -->
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
                    <button class="accordion-button collapsed border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilter<?php echo $accordionIndex; ?>" aria-expanded="false" aria-controls="collapseFilter<?php echo $accordionIndex; ?>">
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
</div>


                        <!-- start apply button -->
                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->
                        <!-- end /. apply button -->
                        <!-- start clear filters -->
                        <a href="mantras.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">
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
            <!-- end /. sidebar filters -->
            <!-- start items content -->
            <div class="col-xl-9 ps-lg-4 ps-xl-5 sidebar">
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Mantras & Stotras</div>
                </div>
                <div id="listings-container" class="listings grid-view">
                    <?php
                    // Set the number of records per page
                    $records_per_page = 9;

                    // Get the current page from the URL, default to page 1 if not set
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $page = max($page, 1); // Ensure the page is at least 1

                    // Calculate the OFFSET for SQL query
                    $offset = ($page - 1) * $records_per_page;

                    // Fetch total number of records
                    $total_result = mysqli_query($DatabaseCo->dbLink, "SELECT COUNT(*) AS total FROM mantras_subcategory");
                    $total_row = mysqli_fetch_assoc($total_result);
                    $total_records = $total_row['total'];

                    // Calculate total pages
                    $total_pages = ceil($total_records / $records_per_page);

                    // Fetch paginated results
                    $select = "SELECT * FROM mantras_subcategory  WHERE status='approved' ORDER BY order_by ASC";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    // Check if records are availablemantras-details.php?id=<?php echo $Row['index_id']; 
                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                            $photos = $Row['photos']; ?>
                            <div class="listing">
                                <a href="#0" >
                                    <a href="mantras-details.php?id=<?php echo $Row['index_id']; ?>" >
                                        <img src="app/uploads/gods/<?php echo $photos; ?>" alt="" class="img-fluid">
                                    </a>
                                    <div class="listing-details">
                                        <a href="mantras-details.php?id=<?php echo $Row['index_id']; ?>" >
                                            <div class="listing-title"><?php echo $Row['title']; ?></div>
                                        </a>

                                        <div class="listing-rating text-dark"><a href="mantras-details.php?id=<?php echo $Row['index_id']; ?>" >Read more</a></div>
                                    </div>
                            </div>
                            <!-- Repeat for additional listings -->
                        <?php

                        }
                    } else {
                        ?>
                    <?php
                        echo "";
                    }

                    ?>
                </div>
                <div class="row d-none">
                    <div class="col-lg-12 mt--60">
                        <nav class="custom-pagination mt-5" aria-label="Page navigation">
                            <!-- Previous Button -->
                            <?php if ($page > 1) { ?>
                                <a class="prev page-numbers" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z" />
                                    </svg>
                                    Previous
                                </a>
                            <?php } ?>

                            <!-- Page Numbers -->
                            <div class="page-links">
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <a href="?page=<?php echo $i; ?>"
                                        class="page-numbers <?php echo ($i == $page) ? 'current' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php } ?>
                            </div>

                            <!-- Next Button -->
                            <?php if ($page < $total_pages) { ?>
                                <a class="next page-numbers" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    Next
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z" />
                                    </svg>
                                </a>
                            <?php } ?>
                        </nav>


                    </div>
                </div>
            </div>
            <!-- end /. items content -->
        </div>
    </div>
</div>
<?php include_once './include/footer.php' ?>
<script>
    // Load states when a country is selected
    $('#country').change(function() {
        let countryCode = $(this).val();
        $.ajax({
            url: './app/get_states.php',
            type: 'POST',
            data: {
                country_code: countryCode
            },
            success: function(response) {
                $('#state').html(response);
                $('#city').html('<option selected disabled>Select City</option>'); // Reset city dropdown
            }
        });
    });

    // Load cities when a state is selected
    $('#state').change(function() {
        let stateCode = $(this).val();
        $.ajax({
            url: './app/get_cities.php',
            type: 'POST',
            data: {
                state_code: stateCode
            },
            success: function(response) {
                $('#city').html(response);
            }
        });
    });
</script>


<script>
    // Fetch filtered and paginated listings, with all data shown by default on page load
    function fetchListings(page = 1) {
        // Get all checked filters
        let selectedFilters = [];
        document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {
            selectedFilters.push(checkbox.value);
            console.log(selectedFilters);
        });

        // Prepare the request parameters
        const params = new URLSearchParams();
        params.append('page', page);

        // Only add selectedFilters to the request if checkboxes are selected
        if (selectedFilters.length > 0) {
            params.append('selectedFilters_mantras', selectedFilters.join(','));
        }

        // Send selected filters and page number to the server
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Update listingsContainer and paginationControls with the response data
                const response = JSON.parse(xhr.responseText);
                document.getElementById('listings-container').innerHTML = response.listings;
                document.getElementById('paginationControls').innerHTML = response.pagination;
            }
        };
        xhr.send(params.toString());
    }

    // Event listeners for checkboxes to re-fetch listings when any checkbox changes
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', () => fetchListings());
    });

    // Initial fetch for page load (shows all data initially)
    fetchListings();
</script>
<script>
    // Redirect to another page with selected IDs as query parameters
    function redirectToAnotherPage() {
        let selectedFilters = [];
        
        // Select checkboxes by id pattern
        document.querySelectorAll('[id^="mantras"]:checked').forEach(checkbox => {
            selectedFilters.push(checkbox.value);
        });

        // Redirect to another page with selected IDs as query parameters
        const baseUrl = 'mantras_title_category.php'; // Replace with your destination page URL
        const params = new URLSearchParams();

        if (selectedFilters.length > 0) {
            params.append('id', selectedFilters.join(','));
        }

        window.location.href = `${baseUrl}?${params.toString()}`;
    }

    // Event listeners for checkboxes to redirect on click
    document.querySelectorAll('[id^="mantras"]').forEach(checkbox => {
        checkbox.addEventListener('change', redirectToAnotherPage);
    });
</script>


<script>
    function clearPage() {
        location.reload(mantras.php); // Reloads the current page, clearing any changes
    }
</script>