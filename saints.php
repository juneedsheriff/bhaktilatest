<?php
include('./include/header.php');
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
// Check if god_id is set to 7 in the URL
$id = $xssClean->clean_input($_REQUEST['id']);

error_reporting(0);
?>
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

                        <div class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-2">Filter by God Names</h4>
                            </div>
                            <!-- Start Form Check -->
                            <?php
    // Fetch all data from the other_page table with limit and offset, ordered alphabetically by title
    $select = "SELECT * FROM other_page WHERE page_id ='$id' ORDER BY title ASC";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    // Check if any rows are returned
    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
            $title = $Row['title'];
            $index_id = $Row['index_id'];
?>
            <!-- Displaying each row with a checkbox -->
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="<?php echo $index_id; ?>" id="god<?php echo $Row['index_id']; ?>">
                <label class="form-check-label" for="god<?php echo $Row['index_id']; ?>">
                    <?php echo $title; ?>
                </label>
            </div>
<?php
        }
    } else {
        echo "<p class='text-center'>No temples found.</p>";
    }
?>
                        </div>

                        <!-- start apply button -->
                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->
                        <!-- end /. apply button -->
                        <!-- start clear filters -->
                        <a href="saints.php?id=<?php echo $id; ?>" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">
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
                    <?php
                    $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT name FROM  category WHERE index_id='" . $id . "'");
                    $res3 = mysqli_fetch_object($sql3);
                    ?>
                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Temples in <?php echo $res3->name  ?></div>
                    <!-- start button group -->

                    <!-- end /. button group -->
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
                    // Fetch the total number of records
                    $query = "SELECT COUNT(*) AS total FROM other_page WHERE page_id = '$id'";
                    $total_result = mysqli_query($DatabaseCo->dbLink, $query);

                    if ($total_result) {
                        $total_row = mysqli_fetch_assoc($total_result);
                        $total_records = $total_row['total'];
                    } else {
                        // Handle query error
                        $total_records = 0; // Default value or handle as needed
                        error_log("Query Error: " . mysqli_error($DatabaseCo->dbLink));
                    }


                    // Calculate total pages
                    $total_pages = ceil($total_records / $records_per_page);

                    // Fetch paginated results
                    $select = "SELECT * FROM other_page WHERE page_id = '$id' ORDER BY order_by ASC";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    // Check if records are available
                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                        while ($Row =  mysqli_fetch_assoc($SQL_STATEMENT)) {
                            $photos = $Row['photos'];
                            $title = $Row['title'];
                            // Your HTML/PHP code here
                    ?>
                            <div class="listing">
                                <a href="saints-details?id=<?php echo $Row['index_id']; ?>&page_id=<?php echo $Row['page_id']; ?>" >
                                    <a href="saints-details.php?id=<?php echo $Row['index_id']; ?>&page_id=<?php echo $Row['page_id']; ?>" >
                                        <img src="app/uploads/others/<?php echo $photos; ?>" alt="">
                                    </a>
                                    <div class="listing-details">
                                        <a href="saints-details.php?id=<?php echo $Row['index_id']; ?>&page_id=<?php echo $Row['page_id']; ?>" >
                                            <div class="listing-title"><?php echo $title; ?></div>
                                        </a>
                                        <div class="listing-rating text-dark"><a href="saints-details.php?id=<?php echo htmlspecialchars($Row['index_id']); ?>&page_id=<?php echo htmlspecialchars($Row['page_id']); ?>" >Read more</a>
                                        </div>
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
                                <a class="prev page-numbers" href="?page=<?php echo $page - 1; ?> &id=<?php echo $id; ?>" aria-label="Previous">
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
                                    <a href="?page=<?php echo $i; ?> &id=<?php echo $id; ?>"
                                        class="page-numbers <?php echo ($i == $page) ? 'current' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php } ?>
                            </div>

                            <!-- Next Button -->
                            <?php if ($page < $total_pages) { ?>
                                <a class="next page-numbers" href="?page=<?php echo $page + 1; ?>&id=<?php echo $id; ?>" aria-label="Next">
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
        </div>
    </div>
</div>
<!-- end /. items content -->
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
    function fetchListings(page = 1) {
        let selectedFilters = [];
        document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {
            selectedFilters.push(checkbox.value);
        });

        const params = new URLSearchParams();
        params.append('page', page);

        if (selectedFilters.length > 0) {
            params.append('selectedFilters_saints', selectedFilters.join(','));
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('listings-container').innerHTML = response.listings;
                document.getElementById('paginationControls').innerHTML = response.pagination;
            }
        };
        xhr.send(params.toString());
    }

    // Event listener for checkboxes to fetch listings on change
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', () => fetchListings());
    });

    // Initial fetch for page load
    fetchListings();
</script>
<script>
    function clearPage() {
        location.reload(saints.php); // Reloads the current page, clearing any changes
    }
</script>