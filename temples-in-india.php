<?php

include('./include/header.php');
include_once './include/breadcrumb_helpers.php';

error_reporting(0);

render_breadcrumbs([
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Temples in India'],
]);

?>
<link href="assets/css/temple-pages-responsive.css" rel="stylesheet">

<div class="container d-xl-none py-2 temples-india-page">
    <div class="d-flex justify-content-center">
    <a href="#" class="sidebarCollapse align-items-center d-flex justify-content-center filters-text fw-semibold gap-2">

        <i class="fa-solid fa-arrow-up-short-wide fs-18"></i>

        <span>All filters</span>

    </a>
    </div>
</div>

<div class="py-3 py-xl-5 bg-gradient temples-india-page">

    <div class="container">

        <div class="row">

            <!-- start sidebar filters -->

            <aside class="col-12 col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end">

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

                    <div class="sidebar-filters-body pr-3 p-xl-4 ">



                        <div class="mb-4 border-bottom pb-4">

                            <div class="mb-3">

                                <h4 class="fs-5 fw-semibold mb-1">State</h4>

                            </div>

                            <!-- Start Select2 -->

                            <select class="form-select mb-3" name="state" id="state">

                                <option value="">Select State</option>

                                <!-- <option selected disabled>Select State</option> -->

                                <?php



                                $country_code = 'IN';

                                // Use state_code so get_cities.php can load cities

                                $state = isset($Row->state) ? $Row->state : '';

                                if ($country_code) {

                                    $stateQuery = "SELECT DISTINCT s.state_code, s.state_name
                                        FROM state s
                                        INNER JOIN temples t ON t.status = 'approved'
                                            AND t.country = 'IN'
                                            AND (t.state = s.state_code OR t.state = CAST(s.state_id AS CHAR))
                                        WHERE s.country_code = '$country_code'
                                        ORDER BY s.state_name ASC";

                                    $stateResult = mysqli_query($DatabaseCo->dbLink, $stateQuery);

                                    while ($stateRow = mysqli_fetch_object($stateResult)) {

                                        $selected = ($stateRow->state_code == $state) ? 'selected' : '';

                                        echo "<option value='" . htmlspecialchars($stateRow->state_code) . "' $selected>" . htmlspecialchars($stateRow->state_name) . "</option>";

                                    }

                                }

                                ?>

                            </select>

                            <!-- /.End Select2 -->

                        </div>

                        <div class="mb-4 border-bottom pb-4">

                            <div class="mb-3">

                                <h4 class="fs-5 fw-semibold mb-1">City</h4>

                            </div>

                            <!-- Start Select2 -->

                            <select class="form-select" aria-label="City" name="city" id="city">

                                <option value="" selected>Select City</option>

                            </select>

                            <!-- /.End Select2 -->

                        </div>

                        <div class="mb-4 border-bottom pb-4">

                            <div class="mb-3">

                                <h4 class="fs-5 fw-semibold mb-1">Town</h4>

                            </div>

                            <!-- Start Select2 -->

                            <select class="form-select" aria-label="Town" name="town" id="town">

                                <option value="" selected>Select Town</option>

                            </select>

                            <!-- /.End Select2 -->

                        </div>

                        <div class="mb-4 border-bottom pb-4 temple-god-filter-wrap">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-1">Filter by</h4>
                            </div>
                            <ul class="nav nav-tabs temple-god-filter-tabs mb-3" role="tablist">
                                <li class="nav-item flex-fill" role="presentation">
                                    <button type="button" class="nav-link active w-100" id="filter-tab-temples-btn" data-filter-tab="temples" role="tab" aria-selected="true">Temples</button>
                                </li>
                                <li class="nav-item flex-fill" role="presentation">
                                    <button type="button" class="nav-link w-100" id="filter-tab-god-btn" data-filter-tab="god" role="tab" aria-selected="false">God</button>
                                </li>
                            </ul>

                            <div class="temple-god-filter-panel active" id="filter-panel-temples" role="tabpanel">
                                <input type="search" class="form-control form-control-sm mb-2" id="templeFilterSearch" placeholder="Search temples..." autocomplete="off">
                                <div class="temple-god-filter-scroll" id="templeFilterList">
                                    <?php
                                    $templeQuery = "SELECT index_id, title FROM temples WHERE status = 'approved' AND title != '' ORDER BY title ASC";
                                    $templeResult = mysqli_query($DatabaseCo->dbLink, $templeQuery);
                                    if ($templeResult && mysqli_num_rows($templeResult) > 0) {
                                        while ($templeRow = mysqli_fetch_assoc($templeResult)) {
                                            $temple_id = (int) $templeRow['index_id'];
                                            $temple_title = htmlspecialchars($templeRow['title'], ENT_QUOTES, 'UTF-8');
                                            $temple_search = htmlspecialchars(strtolower($templeRow['title']), ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <div class="form-check mb-2 filter-check-row" data-filter-label="<?php echo $temple_search; ?>">
                                            <input class="form-check-input temple-check-input" type="checkbox" value="<?php echo $temple_id; ?>" id="temple<?php echo $temple_id; ?>">
                                            <label class="form-check-label" for="temple<?php echo $temple_id; ?>"><?php echo $temple_title; ?></label>
                                        </div>
                                    <?php
                                        }
                                    } else {
                                        echo "<p class='small text-muted mb-0'>No temples found.</p>";
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="temple-god-filter-panel" id="filter-panel-god" role="tabpanel" hidden>
                                <input type="search" class="form-control form-control-sm mb-2" id="godFilterSearch" placeholder="Search gods..." autocomplete="off">
                                <div class="temple-god-filter-scroll" id="godFilterList">
                                    <?php
                                    $godQuery = "SELECT DISTINCT g.index_id, g.god_name
                                        FROM temples t
                                        INNER JOIN god g ON g.index_id = t.god_id
                                        WHERE t.status = 'approved' AND t.god_id > 0
                                        ORDER BY g.god_name ASC";
                                    $godResult = mysqli_query($DatabaseCo->dbLink, $godQuery);
                                    if ($godResult && mysqli_num_rows($godResult) > 0) {
                                        while ($godRow = mysqli_fetch_assoc($godResult)) {
                                            $god_name = htmlspecialchars($godRow['god_name'], ENT_QUOTES, 'UTF-8');
                                            $index_id = (int) $godRow['index_id'];
                                            $god_search = htmlspecialchars(strtolower($godRow['god_name']), ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <div class="form-check mb-2 filter-check-row" data-filter-label="<?php echo $god_search; ?>">
                                            <input class="form-check-input god-check-input" type="checkbox" value="<?php echo $index_id; ?>" id="god<?php echo $index_id; ?>">
                                            <label class="form-check-label" for="god<?php echo $index_id; ?>"><?php echo $god_name; ?></label>
                                        </div>
                                    <?php
                                        }
                                    } else {
                                        echo "<p class='small text-muted mb-0'>No gods found.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <style>
                        .temple-god-filter-tabs .nav-link {
                            font-weight: 600;
                            text-align: center;
                            color: #333;
                            border-color: #dee2e6;
                        }
                        .temple-god-filter-tabs .nav-link.active {
                            color: #fff;
                            background-color: var(--bs-primary, #862c71);
                            border-color: var(--bs-primary, #862c71);
                        }
                        .temple-god-filter-panel { display: none; }
                        .temple-god-filter-panel.active { display: block; }
                        .temple-god-filter-scroll {
                            max-height: 28rem;
                            overflow-y: auto;
                            overflow-x: hidden;
                            padding-right: 4px;
                            -webkit-overflow-scrolling: touch;
                        }
                        .temple-god-filter-scroll .form-check {
                            min-height: 2rem;
                        }
                        #viewmore.is-loading {
                            opacity: 0.55;
                            pointer-events: none;
                        }
                        </style>



                        <!-- start apply button -->

                        <!-- <button type="button" class="btn btn-primary w-100">Apply filters</button> -->

                        <!-- end /. apply button -->

                        <!-- start clear filters -->

                        <a href="temple.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">

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

            <div class="col-12 col-xl-9 px-2 px-md-3 ps-lg-4 ps-xl-5 sidebar">

                <?php
                $india_per_page = 50;
                $approved_count_result = mysqli_query($DatabaseCo->dbLink, "SELECT COUNT(*) AS total FROM temples WHERE status='approved' AND country='IN'");
                $approved_count_row = mysqli_fetch_assoc($approved_count_result);
                $approved_temple_count = (int) ($approved_count_row['total'] ?? 0);
                ?>

                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">

                    <div class="fs-1 font-caveat page-header-title fw-bold m-0 m-md-2 pb-2 pb-md-3 text-primary text-center text-xl-start">
                        Temples in India
                        <span id="temple-listing-count" class="fs-4 fw-semibold text-dark">(<?php echo number_format($approved_temple_count); ?>)</span>
                    </div>

                    <!-- start button group -->



                    <!-- end /. button group -->

                </div>

                <div id="viewmore" class="listings grid-view">

                    <?php

                    $total_records = $approved_temple_count;

                    

                    $select = "SELECT * FROM temples WHERE status='approved' AND country='IN' ORDER BY order_by ASC LIMIT 0," . (int) $india_per_page;

                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    

                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                            $photos = $Row['photos'];

                            $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . $Row['city'] . "'");

                            $cff = mysqli_fetch_array($ccc);

                            $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . $Row['state'] . "' AND country_code='" . $Row['country'] . "'");

                            $fff = mysqli_fetch_array($sss);

                    ?>

                            <div class="listing">
                                <a href="temple-details.php?id=<?php echo $Row['index_id']; ?>">
                                    <img src="app/uploads/temple/<?php echo $photos; ?>" alt="<?php echo htmlspecialchars($Row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                </a>
                                <div class="listing-details">
                                    <a href="temple-details.php?id=<?php echo $Row['index_id']; ?>">
                                        <div class="listing-title"><?php echo $Row['title'];
                                            echo !empty($cff['city_name']) ? ', ' . $cff['city_name'] : '';
                                            echo !empty($fff['state_name']) ? ', ' . $fff['state_name'] : ''; ?></div>
                                    </a>
                                    <div class="listing-rating text-dark"><a href="temple-details.php?id=<?php echo $Row['index_id']; ?>">Read more</a></div>
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

                <?php if ($total_records > $india_per_page) { ?>

                <div class="show_more_main m-3" id="show_more_main1" align="center">

        <span id="getID" data-id="1" data-category="<?php echo "india";?>" class="show_more btn btn-primary btn-lg" title="Load more Images">Load More</span>

        <span class="loding btn btn-info btn-lg text-white" style="display: none;"><span class="loding_txt">Loading...</span></span>

    </div><?php }?>

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

$(document).on('click', '.show_more', function () {

    var button = $(this);

    var ID = $('#getID').data('id');//alert(ID);

    var newID = parseInt(ID) + 1;

    var category = button.data('category');//alert(category);

    $('.show_more').hide();

    $('.loding').show();

    $.ajax({

        type: 'POST',

        url: 'ajax.php',

        data: 'pageid=' + ID + '&type=' + category,

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

    function loadCitiesForState(stateCode) {
        if (!stateCode || stateCode === '') {
            $('#city').html("<option value=''>Select City</option>");
            $('#town').html("<option value=''>Select Town</option>");
            fetchListingsByCountryStateCity();
            return;
        }
        $.ajax({
            url: './app/get_cities.php',
            type: 'POST',
            data: { state_code: stateCode },
            success: function(response) {
                $('#city').html(response);
                $('#town').html("<option value=''>Select Town</option>");
                fetchListingsByCountryStateCity();
            },
            error: function() {
                $('#city').html("<option value=''>Select City</option>");
                $('#town').html("<option value=''>Select Town</option>");
                fetchListingsByCountryStateCity();
            }
        });
    }

    $('#state').off('change').on('change', function() {
        loadCitiesForState($(this).val());
    });

    // Load city options on page load when a state is already selected
    (function() {
        var initialState = $('#state').val();
        if (initialState && initialState !== '') {
            loadCitiesForState(initialState);
        }
    })();

    $('#city').off('change').on('change', function() {
        var city_name = $(this).find('option:selected').text();
        if (!city_name || city_name === '' || city_name === '-Select City-') {
            $('#town').html("<option value=''>Select Town</option>");
            fetchListingsByCountryStateCity();
            return;
        }
        $.ajax({
            url: './app/get_towns.php',
            type: 'POST',
            data: { city_name: city_name },
            success: function(response) {
                $('#town').html(response);
            }
        });
        fetchListingsByCountryStateCity();
    });

    $('#town').change(function() {
        fetchListingsByCountryStateCity();
    })
    

    function hasActiveFilters() {
        var state = ($('#state').val() || '').toString().trim();
        var city = ($('#city').val() || '').toString().trim();
        var town = ($('#town').val() || '').toString().trim();
        var hasCheckbox = document.querySelector('.temple-check-input:checked, .god-check-input:checked');
        return !!(state || city || town || hasCheckbox);
    }

    function updateLoadMoreVisibility() {
        if (hasActiveFilters()) {
            $('#show_more_main1').hide();
        } else {
            $('#show_more_main1').show();
        }
    }

    function scrollToTempleListingCount() {
        var el = document.getElementById('temple-listing-count');
        if (!el) {
            return;
        }
        var headerOffset = 100;
        var top = el.getBoundingClientRect().top + window.pageYOffset - headerOffset;
        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    }

    // Fetch listings based on selected country, state, and city filters

    // Fetch listings based on selected country, state, and city

    function fetchListingsByCountryStateCity() {

        var country = ($('#country').length ? $('#country').val() : null) || 'IN';

        var state = ($('#state').val() || '').toString().trim();

        var city  = ($('#city').val() || '').toString().trim();

        var town  = ($('#town').val() || '').toString().trim();
        var hasLocationFilter = !!(state || city || town);

        $('.form-check-input, .temple-check-input').each(function() {

            this.checked = false;

        });



        $.ajax({

            url: 'fetch_listings_2.php',

            type: 'POST',

            dataType: 'json',

            data: {

                country: country,

                state: state,

                city: city,
                
                town: town

            },

            success: function(data) {
                var payload = typeof data === 'string' ? JSON.parse(data) : data;
                $('#viewmore').html(payload.html || '');
                if (payload.count !== undefined) {
                    $('#temple-listing-count').text('(' + Number(payload.count).toLocaleString('en-IN') + ')');
                }
                updateLoadMoreVisibility();
                if (hasLocationFilter) {
                    scrollToTempleListingCount();
                }
            },

            error: function(xhr, status, error) {

                console.error("An error occurred:", error);

                $('#viewmore').html("<p>An error occurred while fetching listings.</p>");

            }

        });

    }

    // Fetch listings based on selected filters (checkboxes) and pagination





    // Clear page and reload function

    function clearPage() {

        location.reload();

    }



    // Trigger fetchListingsByCountry on filter change (country, state, city, filter type)

    //$('#country, #state, #city').change(fetchListingsByCountry);

    //$('input[name="filter_type"]').change(fetchListingsByCountry);





    var indiaFilterXhr = null;

    function fetchListings() {
        var selectedFilters = [];
        var selectedTempleFilters = [];

        document.querySelectorAll('.temple-check-input:checked, .god-check-input:checked').forEach(function(checkbox) {
            if (checkbox.classList.contains('temple-check-input')) {
                selectedTempleFilters.push(checkbox.value);
            } else {
                selectedFilters.push(checkbox.value);
            }
        });

        if (selectedFilters.length === 0 && selectedTempleFilters.length === 0) {
            location.reload();
            return;
        }

        if (indiaFilterXhr) {
            indiaFilterXhr.abort();
        }

        $("#state").prop("selectedIndex", 0).val('');
        $('#city').html("<option value=''>Select City</option>");
        $('#town').html("<option value=''>Select Town</option>");

        var params = new URLSearchParams();
        params.append('country', 'IN');

        if (selectedFilters.length > 0) {
            params.append('selectedFilters', selectedFilters.join(','));
        }

        if (selectedTempleFilters.length > 0) {
            params.append('selectedTempleFilters', selectedTempleFilters.join(','));
        }

        var viewmore = document.getElementById('viewmore');
        viewmore.classList.add('is-loading');

        indiaFilterXhr = new XMLHttpRequest();
        indiaFilterXhr.open('POST', 'fetch_listings_india_filter.php', true);
        indiaFilterXhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        indiaFilterXhr.onload = function() {
            viewmore.classList.remove('is-loading');

            if (indiaFilterXhr.status !== 200) {
                return;
            }

            const response = JSON.parse(indiaFilterXhr.responseText);
            viewmore.innerHTML = response.listings || '<p>No listings found.</p>';

            if (response.total !== undefined) {
                document.getElementById('temple-listing-count').textContent =
                    '(' + Number(response.total).toLocaleString('en-IN') + ')';
            }

            updateLoadMoreVisibility();
            scrollToTempleListingCount();
        };

        indiaFilterXhr.onerror = function() {
            viewmore.classList.remove('is-loading');
        };

        indiaFilterXhr.send(params.toString());
    }



    // Temple / God filter tabs
    document.querySelectorAll('[data-filter-tab]').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function() {
            var target = tabBtn.getAttribute('data-filter-tab');
            document.querySelectorAll('[data-filter-tab]').forEach(function(btn) {
                btn.classList.toggle('active', btn === tabBtn);
                btn.setAttribute('aria-selected', btn === tabBtn ? 'true' : 'false');
            });
            document.querySelectorAll('.temple-god-filter-panel').forEach(function(panel) {
                var isActive = panel.id === 'filter-panel-' + target;
                panel.classList.toggle('active', isActive);
                panel.hidden = !isActive;
            });
        });
    });

    function filterCheckboxList(searchInput, listEl) {
        if (!searchInput || !listEl) return;
        var q = searchInput.value.toLowerCase().trim();
        listEl.querySelectorAll('.filter-check-row').forEach(function(row) {
            var label = (row.getAttribute('data-filter-label') || '').toLowerCase();
            row.style.display = !q || label.indexOf(q) !== -1 ? '' : 'none';
        });
    }

    var templeFilterSearch = document.getElementById('templeFilterSearch');
    var godFilterSearch = document.getElementById('godFilterSearch');
    var templeFilterList = document.getElementById('templeFilterList');
    var godFilterList = document.getElementById('godFilterList');

    if (templeFilterSearch) {
        templeFilterSearch.addEventListener('input', function() {
            filterCheckboxList(templeFilterSearch, templeFilterList);
        });
    }
    if (godFilterSearch) {
        godFilterSearch.addEventListener('input', function() {
            filterCheckboxList(godFilterSearch, godFilterList);
        });
    }

    // Event listener for checkboxes (god and temple) to fetch listings on change
    document.querySelectorAll('.form-check-input, .temple-check-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() { fetchListings(); });
    });

    updateLoadMoreVisibility();



</script>