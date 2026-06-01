<?php
$__mysteryHelpers = __DIR__ . '/include/mystery_helpers.php';
if (is_file($__mysteryHelpers)) {
    include_once $__mysteryHelpers;
}
include('./include/header.php');
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
        <div class="row">
            <!-- start sidebar filters -->
            <aside class="col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end">
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
                                <h4 class="fs-5 fw-semibold mb-2">Filter by God Name</h4>
                            </div>
                            <!-- Start Form Check -->
                            <?php
                            $mysteryGods = [];
                            try {
                                if (function_exists('mystery_fetch_gods')) {
                                    $mysteryGods = mystery_fetch_gods($DatabaseCo->dbLink);
                                }
                            } catch (Throwable $e) {
                                $mysteryGods = [];
                            }

                            if (!empty($mysteryGods)) {
                                foreach ($mysteryGods as $godRow) {
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
                        <a href="mystery.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">
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
                    <div class="fs-1 font-caveat page-header-title fw-bold m-2 pb-3 text-primary">Mystery Temples</div>
                </div>
                <div id="viewmore" class="listings grid-view">
                    <?php
                    $db = $DatabaseCo->dbLink;
                    $allMysteryItems = [];
                    $mysteryItems = [];
                    if (function_exists('mystery_collect_items')) {
                        try {
                            $allMysteryItems = mystery_collect_items($db);
                            $mysteryItems = array_slice($allMysteryItems, 0, 9);
                        } catch (Throwable $e) {
                            $allMysteryItems = [];
                            $mysteryItems = [];
                        }
                    }
                    $total_records = count($allMysteryItems);

                    if (!empty($mysteryItems)) {
                        foreach ($mysteryItems as $Row) {
                            echo mystery_listing_html($db, $Row);
                        }
                    }
                    ?>
                </div>
                <?php if ($total_records > 9) { ?>
                <div class="show_more_main m-3" id="show_more_main1" align="center">
                    <span id="getID" data-id="1" data-category="mystery" class="show_more btn btn-primary btn-lg" title="Load more">Load More</span>
                    <span class="loding btn btn-info btn-lg text-white" style="display: none;"><span class="loding_txt">Loading...</span></span>
                </div>
                <?php } ?>
                <div id="paginationControls" class="d-none"></div>

                <div class="row d-none">
                    <div class="col-lg-12 mt--60">
                        <nav class="custom-pagination mt-5" aria-label="Page navigation">
                            <!-- Previous Button -->
                            <?php
                            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
                            $total_pages = ($total_records > 0) ? (int) ceil($total_records / 9) : 0;
                            if ($page > 1) { ?>
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
    var ID = $('#getID').data('id');
    var newID = parseInt(ID, 10) + 1;
    var category = button.data('category');
    $('.show_more').hide();
    $('.loding').show();
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        data: 'pageid=' + ID + '&type=' + category,
        success: function (html) {
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

function fetchListings(page = 1) {
    let selectedFilters = [];
    document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {
        selectedFilters.push(checkbox.value);
    });

    const params = new URLSearchParams();
    params.append('page', page);
    params.append('selectedFilters_mystery', selectedFilters.join(','));

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_listings.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200 && xhr.responseText) {
            try {
                const response = JSON.parse(xhr.responseText);
                document.getElementById('viewmore').innerHTML = response.listings || '';
                document.getElementById('paginationControls').innerHTML = response.pagination || '';
                var loadMoreEl = document.getElementById('show_more_main1');
                if (loadMoreEl) {
                    loadMoreEl.style.display = 'none';
                }
            } catch (e) {
                console.error('Could not parse listings response', e);
            }
        }
    };
    xhr.send(params.toString());
}

document.querySelectorAll('.form-check-input').forEach(checkbox => {
    checkbox.addEventListener('change', () => fetchListings());
});

function clearPage() {
    location.reload();
}
</script>