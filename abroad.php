<?php
include('./include/header.php');
include_once './include/abroad_listing_helpers.php';
error_reporting(0);
?>
<style>
    .card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
                        <div class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-1">Country</h4>
                            </div>
                            <?php

                            ?>
                            <!-- Start Select2 -->
                            <select class="form-select mb-3" name="country" id="country">
                                <option value="">Select Country</option>
                                <?php
                                $countrySql = "SELECT DISTINCT c.country_code, c.country_name
                                    FROM country c
                                    INNER JOIN abroad a ON a.country = c.country_code
                                    WHERE a.status = 'approved'
                                      AND TRIM(COALESCE(a.country, '')) != ''
                                      AND c.country_code != 'IN'";
                                if (isset($country_code) && strtoupper((string) $country_code) !== 'IN') {
                                    $cc = $DatabaseCo->dbLink->real_escape_string($country_code);
                                    $countrySql .= " AND c.country_code = '$cc'";
                                }
                                $countrySql .= ' ORDER BY c.country_name ASC';
                                $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $countrySql);
                                if ($VSQL_STATEMENT) {
                                    while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) { ?>
                                    <option value="<?php echo htmlspecialchars($VRow->country_code, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($VRow->country_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php }
                                } ?>
                            </select>

                            <!-- /.End Select2 -->
                        </div>
                        <div class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-1">City</h4>
                            </div>
                            <!-- Start Select2 -->
                            <select class="form-select" aria-label="Default select example" name="city" id="city">
                                <option value="">Select City</option>
                            </select>
                            <!-- /.End Select2 -->
                        </div>
                        <div class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-2">Filter by God Name</h4>
                            </div>
                            <!-- Start Form Check -->
                            <?php
                            $godQuery = "SELECT DISTINCT g.index_id, g.god_name
                                FROM abroad a
                                INNER JOIN god g ON g.index_id = a.god_id
                                WHERE a.status = 'approved' AND a.god_id > 0
                                ORDER BY g.god_name ASC";
                            $godResult = mysqli_query($DatabaseCo->dbLink, $godQuery);

                            if ($godResult && mysqli_num_rows($godResult) > 0) {
                                while ($godRow = mysqli_fetch_assoc($godResult)) {
                                    $god_name = htmlspecialchars($godRow['god_name'], ENT_QUOTES, 'UTF-8');
                                    $index_id = (int) $godRow['index_id'];
                            ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="<?php echo $index_id; ?>" id="god<?php echo $index_id; ?>">
                                    <label class="form-check-label" for="god<?php echo $index_id; ?>">
                                        <?php echo $god_name; ?>
                                    </label>
                                </div>
                            <?php
                                }
                            } else {
                                echo "<p class='small text-muted'>No gods found.</p>";
                            }
                            ?>


                        </div>

                        <!-- start apply button -->
                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->
                        <!-- end /. apply button -->
                        <!-- start clear filters -->
                        <a href="abroad.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">
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
                <?php
                $total_result = mysqli_query($DatabaseCo->dbLink, "SELECT COUNT(*) AS total FROM abroad WHERE status='approved'");
                $total_row = mysqli_fetch_assoc($total_result);
                $total_records = (int) $total_row['total'];
                $abroad_per_page = abroad_listing_per_page();
                ?>
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary">
                        Temples in Abroad
                        <span id="abroad-listing-count" class="fs-4 fw-normal text-muted">(<?php echo number_format($total_records); ?>)</span>
                    </div>
                    <!-- start button group -->
                    <!-- end /. button group -->
                </div>
                <div id="viewmore" class="listings grid-view">
                    <?php
                    $select = "SELECT * FROM abroad WHERE status='approved' ORDER BY " . abroad_listing_order_sql() . " LIMIT 0, {$abroad_per_page}";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    // Check if records are available
                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                            $photos = $Row['photos'];
                            $placeLabel = abroad_listing_place_label($DatabaseCo->dbLink, $Row);
                        ?>
                            <div class="listing">
                                <a href="abroad-details.php?id=<?php echo $Row['index_id']; ?>">
                                        <img <?php echo abroad_listing_photo_attrs($photos, $Row['title'] ?? ''); ?>>
                                </a>
                                    <div class="listing-details">
                                        <a href="abroad-details.php?id=<?php echo $Row['index_id']; ?>" >
                                        <div class="listing-title"><?php echo $Row['title']; echo $placeLabel !== '' ? ', ' . htmlspecialchars($placeLabel, ENT_QUOTES, 'UTF-8') : ''; ?></div>
                                       </a>
                                        <div class="listing-rating text-dark"><a href="abroad-details.php?id=<?php echo $Row['index_id']; ?>" >Read more</a></div>
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
                 <?php if($total_records > $abroad_per_page){?>
                <div class="show_more_main m-3" id="show_more_main1" align="center">
        <span id="getID" data-id="1" data-limit="<?php echo $abroad_per_page; ?>" data-category="<?php echo "abroad";?>" class="show_more btn btn-primary btn-lg" title="Load more Images">Load More</span>
        <span class="loding btn btn-info btn-lg text-white" style="display: none;"><span class="loding_txt">Loading...</span></span>
    </div><?php }?>
                <div class="row">
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
    
$(document).on('click', '.show_more', function () {
    var button = $(this);
    var ID = $('#getID').data('id');//alert(ID);
    var newID = parseInt(ID) + 1;
    var category = button.data('category');
    var limit = $('#getID').data('limit') || 50;
    $('.show_more').hide();
    $('.loding').show();
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        data: 'pageid=' + ID + '&type=' + category + '&limit=' + limit,
        success: function (html) {
            console.log(html);
            if (html != '') {
                $('#viewmore').append(html);
                $('#getID').attr('data-id', newID); 
                $('#getID').data('id', newID); 
                button.show(); 
                $('.loding').hide();
            } else {
                button.hide();
                $('.loding').hide();
            }
        }
    });
});
    function updateAbroadListingCount(count) {
        if (count === undefined) {
            return;
        }
        $('#abroad-listing-count').text('(' + Number(count).toLocaleString('en-IN') + ')');
    }

    $('#country').change(function() {
        let countryCode = $(this).val();
        if (!countryCode) {
            $('#city').html('<option value="">Select City</option>');
            fetchListingsByCountry();
            return;
        }
        $.ajax({
            url: './app/get_abroad_cities.php',
            type: 'POST',
            data: { country_code: countryCode },
            success: function(response) {
                $('#city').html(response);
                fetchListingsByCountry();
            },
            error: function() {
                $('#city').html('<option value="">Select City</option>');
                fetchListingsByCountry();
            }
        });
    });

    // Fetch listings based on selected country and city filters
    function fetchListingsByCountry() {
        let country = ($('#country').val() || '').toString().trim();
        let city = ($('#city').val() || '').toString().trim();
        let filterType = $('input[name="filter_type"]:checked').val();
        $('.form-check-input').each(function() {
            this.checked = false;
        });

        $.ajax({
            url: 'fetch_listings_3.php',
            type: 'POST',
            dataType: 'json',
            data: { country: country, city: city, filter_type: filterType },
            success: function(payload) {
                $('#viewmore').html(payload.html || '');
                updateAbroadListingCount(payload.count);
                $('#show_more_main1').hide();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching listings:", error);
                $('#viewmore').html("<p>An error occurred while fetching listings.</p>");
            }
        });
    }

    // Fetch listings based on selected filters (checkboxes) and pagination
    function fetchListings(page = 1) {
        $("#country").prop("selectedIndex", 0).val();
        $('#city').html('<option value="" selected disabled>Select City</option>');
        let selectedFilters = [];
        document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {
            selectedFilters.push(checkbox.value);
        });

        const params = new URLSearchParams();
        params.append('page', page);
        params.append('selectedFilters_abroad', selectedFilters.join(','));

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('viewmore').innerHTML = response.listings;
                updateAbroadListingCount(response.total);
                const paginationControls = document.getElementById('paginationControls');
                if (paginationControls) {
                    paginationControls.innerHTML = response.pagination;
                }
                $('#show_more_main1').hide();
            }
        };
        xhr.send(params.toString());
    }

    // Event listener for checkboxes to fetch listings on change
    document.querySelectorAll('.form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', () => fetchListings());
    });

    // Initial fetch for page load is handled by PHP (50 records + Load More).

    // Clear page and reload function
    function clearPage() {
        location.reload();
    }

    $('#city').change(fetchListingsByCountry);
    $('input[name="filter_type"]').change(fetchListingsByCountry);
</script>
