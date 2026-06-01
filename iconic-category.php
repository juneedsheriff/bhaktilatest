<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include('./include/header.php');

error_reporting(1);



// Check if god_id is set to 7 in the URL

$god_id = isset($_GET['god_id']) ? (int)$_GET['god_id'] : null;



// Define the SQL query based on the presence of god_id=7

if ($god_id === 7) {

    $select_query = "SELECT * FROM iconic WHERE god_id = 7";

} else {

    $select_query = "SELECT * FROM iconic";

}



// Execute the query

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select_query);

?>



<style>

    .card-img-wrap img {

        width: 100%;

        height: 100%;

        object-fit: cover;

    }



    /* Pagination Wrapper */

    .custom-pagination {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 20px;

        padding: 15px 0;

    }



    /* Page Numbers Container */

    .page-links {

        display: flex;

        gap: 8px;

    }



    /* Individual Page Links */

    .page-numbers {

        padding: 10px 15px;

        border: 1px solid #ddd;

        border-radius: 50%;

        color: black;

        text-decoration: none;

        font-weight: 500;

        transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;

    }



    /* Hover Effect for Page Links */

    .page-numbers:hover {

        background-color: #ff8776;

        color: white;

        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }



    /* Active Page Styling */

    .page-numbers.current {

        background-color: #ff8776;

        color: white;

        font-weight: 600;

        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);

    }



    /* Previous and Next Buttons */

    .prev,

    .next {

        display: flex;

        align-items: center;

        gap: 5px;

        color: black;

        text-decoration: none;

        font-weight: 600;

        transition: color 0.3s;

    }



    .prev:hover,

    .next:hover {

        color: white;

        text-decoration: underline;

    }



    /* SVG Icon Styling */

    .prev svg,

    .next svg {

        fill: currentColor;

        transition: transform 0.3s;

    }



    .prev:hover svg {

        transform: translateX(-5px);

    }



    .next:hover svg {

        transform: translateX(5px);

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

        <div class="row">

            <!-- start sidebar filters -->

            <aside class="col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end d-none">

                <div class="sidebar-filters js-sidebar-filters-mobile ">

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

                                <h4 class="fs-5 fw-semibold mb-2">Filter by Temples Name</h4>

                            </div>

                            <!-- Start Form Check -->

                            <?php

                            // Fetch all relevant index IDs from the iconic table

                            $selectIndexIds = "SELECT DISTINCT god_id FROM iconic";

                            $indexResult = mysqli_query($DatabaseCo->dbLink, $selectIndexIds);



                            // Array to collect god IDs

                            $godIds = [];



                            // Check if any rows are returned

                            if (mysqli_num_rows($indexResult) > 0) {

                                // Collect all god_ids into an array

                                while ($indexRow = mysqli_fetch_assoc($indexResult)) {

                                    $godIds[] = $indexRow['god_id'];

                                }

                                //var_dump($godIds);

                                // Check if there are valid god IDs

                                if (!empty($godIds)) {

                                    // Convert the array of god IDs into a comma-separated list

                                    $godIdList = implode(',', $godIds);



                                    // Fetch all god details in alphabetical order by god_name

                                    $selectGod = "SELECT DISTINCT * FROM god WHERE index_id IN ($godIdList) ORDER BY god_name ASC";

                                    $godResult = mysqli_query($DatabaseCo->dbLink, $selectGod);



                                    if (mysqli_num_rows($godResult) > 0) {

                                        // Array to track displayed god names

                                        $displayedGods = [];



                                        // Display each god's details

                                        while ($godRow = mysqli_fetch_assoc($godResult)) {

                                            $god_name = htmlspecialchars($godRow['god_name'], ENT_QUOTES, 'UTF-8'); // Secure output

                                            $index_id = $godRow['index_id'];



                                            // Check if this god name has already been displayed

                                            if (!in_array($god_name, $displayedGods)) {

                                                // Add god name to the displayed list

                                                $displayedGods[] = $god_name;

                            ?>

                                                <!-- Start Form Check -->

                                                <div class="form-check mb-2">

                                                    <input class="form-check-input" type="checkbox" value="<?php echo $index_id; ?>" id="god<?php echo $index_id; ?>">

                                                    <label class="form-check-label" for="god<?php echo $index_id; ?>">

                                                        <?php echo $god_name; ?>

                                                    </label>

                                                </div>

                                                <!-- End Form Check -->

                            <?php

                                            }

                                        }

                                    } else {

                                        echo "<p class='text-center'>No gods found for the provided index IDs.</p>";

                                    }

                                } else {

                                    echo "<p class='text-center'>No valid god IDs found in the iconic table.</p>";

                                }

                            } else {

                                echo "<p class='text-center'>No records found in the iconic table.</p>";

                            }

                            ?>



                        </div>



                        <!-- start apply button -->

                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->

                        <!-- end /. apply button -->

                        <!-- start clear filters -->

                        <a href="iconic-category.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">

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

            <div class="col-xl-12 ps-lg-4 ps-xl-5 sidebar">

                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">

                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Iconic Temples </div>

                    <!-- start button group -->

                    <!-- end /. button group -->

                </div>

                <div id="listings-container" class="listings grid-view">



                    <?php

                    // Set the number of records per page

                    $records_per_page = 10;



                    // Get the current page from the URL, default to page 1 if not set

                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                    $page = max($page, 1); // Ensure the page is at least 1



                    // Calculate the OFFSET for SQL query

                    $offset = ($page - 1) * $records_per_page;



                    // Check if a specific god_id is set in the URL

                    $god_id = isset($_GET['god_id']) ? (int)$_GET['god_id'] : null;



                    // Prepare the base query

                    $count_query = "SELECT COUNT(*) AS total FROM iconic";

                    $select_query = "SELECT * FROM iconic WHERE status='approved' ORDER BY (index_id = 85) DESC, title ASC ";



                    // Modify queries if god_id is set

                    if ($god_id === 7) {

                        $count_query .= " WHERE god_id = 7";

                        $select_query .= " WHERE god_id = 7";

                    }



                    // Add pagination to the select query

                    //$select_query .= " LIMIT $offset, $records_per_page";



                    // Fetch total number of records

                    $total_result = mysqli_query($DatabaseCo->dbLink, $count_query);

                    $total_row = mysqli_fetch_assoc($total_result);

                    $total_records = $total_row['total'];



                    // Calculate total pages

                    $total_pages = ceil($total_records / $records_per_page);



                    // Fetch paginated results

                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select_query);



                    // Check if records are available

                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                            // Use Featured Image (photos); fallback to banner for backward compatibility
                            $photos = !empty($Row['photos']) ? $Row['photos'] : $Row['banner'];

                            $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . $Row['city'] . "'");

                            $cff = mysqli_fetch_array($ccc);

                            $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . $Row['state'] . "' AND country_code='" . $Row['country'] . "'");

                            $fff = mysqli_fetch_array($sss); ?>

                            <div class="listing" >

                                <a href="iconic-category-details.php?id=<?php echo $Row['index_id']; ?>" >

                                    <img src="app/uploads/iconic/<?php echo $photos; ?>" onerror="this.onerror=null; this.src='assets/images/default-image.png';" 
     alt="">

                                </a>

                                <div class="listing-details">

                                    <a href="iconic-category-details.php?id=<?php echo $Row['index_id']; ?>" style="" >

                                        <div class="listing-title"><?php echo $Row['title'];

                                                                    echo $cff['city_name'] != '' ? ', ' : '';

                                                                    echo $cff['city_name'];

                                                                    echo  $fff['state_name'] != '' ? ', ' : '';

                                                                    echo $fff['state_name']; ?></div>

                                    </a>

                                    <div class="listing-rating text-dark"><a href="iconic-category-details.php?id=<?php echo $Row['index_id']; ?>" >Read more</a></div>

                                </div>

                            </div>

                    <?php

                        }

                    } else {

                        echo "<p class='text-center'>No temples found.</p>";

                    }

                    ?>

                </div>

                <div class="row d-none">

                    <div class="col-lg-12 mt--60">

                        <nav class="custom-pagination mt-5" aria-label="Page navigation">

                            <!-- Previous Button -->

                            <?php if ($page > 1) { ?>

                                <a class="prev page-numbers" href="?page=<?php echo $page - 1; ?><?php echo ($god_id === 7) ? '&god_id=7' : ''; ?>" aria-label="Previous">

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

                                    <a href="?page=<?php echo $i; ?><?php echo ($god_id === 7) ? '&god_id=7' : ''; ?>"

                                        class="page-numbers <?php echo ($i == $page) ? 'current' : ''; ?>">

                                        <?php echo $i; ?>

                                    </a>

                                <?php } ?>

                            </div>



                            <!-- Next Button -->

                            <?php if ($page < $total_pages) { ?>

                                <a class="next page-numbers" href="?page=<?php echo $page + 1; ?><?php echo ($god_id === 7) ? '&god_id=7' : ''; ?>" aria-label="Next">

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

            params.append('selectedFilters_iconic', selectedFilters.join(','));

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

    function clearPage() {

        location.reload(iconic.php); // Reloads the current page, clearing any changes

    }

</script>