<?php

require_once __DIR__ . '/app/class/databaseConn.php';
require_once __DIR__ . '/include/mystery_table_helpers.php';

include('./include/header.php');

error_reporting(0);

$DatabaseCo = new DatabaseConn();
$db = $DatabaseCo->dbLink;

$allMysteryItems = mystery_table_load_all($db);

$mysteryItems = $allMysteryItems;

$mysteryGods = mystery_table_fetch_gods($db);

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

                    <div class="border-bottom d-flex justify-content-between align-items-center p-3 sidebar-filters-header d-xl-none">

                        <div class="align-items-center btn-icon d-flex filter-close justify-content-center rounded-circle">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewbox="0 0 16 16">

                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"></path>

                            </svg>

                        </div>

                        <span class="text-primary fw-medium" onclick="clearPage()">Clear</span>

                    </div>

                    <div class="sidebar-filters-body p-3 p-xl-4">

                        <div class="mb-4 border-bottom pb-4">

                            <div class="mb-3">

                                <h4 class="fs-5 fw-semibold mb-2">Filter by God Name</h4>

                            </div>

                            <?php

                            if (!empty($mysteryGods)) {

                                foreach ($mysteryGods as $godRow) {

                                    $god_name = htmlspecialchars($godRow['god_name'], ENT_QUOTES, 'UTF-8');

                                    $god_id = (int) ($godRow['index_id'] ?? 0);

                            ?>

                                    <div class="form-check mb-2">

                                        <input class="form-check-input" type="checkbox" value="<?php echo $god_id; ?>" id="god<?php echo $god_id; ?>">

                                        <label class="form-check-label" for="god<?php echo $god_id; ?>">

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

                        <a href="mystery.php" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">

                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewbox="0 0 16 16">

                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"></path>

                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"></path>

                            </svg>

                            Clear filters

                        </a>

                    </div>

                </div>

            </aside>

            <div class="col-xl-9 ps-lg-4 ps-xl-5 sidebar">

                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">

                    <div class="fs-1 font-caveat page-header-title fw-bold m-2 pb-3 text-primary">Mystery Temples</div>

                </div>

                <div id="viewmore" class="listings grid-view listings-yellow-border">

                    <?php

                    if (!empty($mysteryItems)) {

                        foreach ($mysteryItems as $Row) {

                            echo mystery_table_listing_html($Row);

                        }

                    } else {

                        echo "<p class='text-muted'>No mystery temples found.</p>";

                    }

                    ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include_once './include/footer.php' ?>

<script>

function fetchListings() {

    let selectedFilters = [];

    document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {

        selectedFilters.push(checkbox.value);

    });



    const params = new URLSearchParams();

    params.append('selectedFilters_mystery', selectedFilters.join(','));



    const xhr = new XMLHttpRequest();

    xhr.open('POST', 'fetch_listings.php', true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {

        if (xhr.status === 200 && xhr.responseText) {

            try {

                const response = JSON.parse(xhr.responseText);

                document.getElementById('viewmore').innerHTML = response.listings || '';

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

