<?php
include('./include/header.php');
include_once './include/saints_media.php';
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();

$poetsPageId = saints_poets_page_id($DatabaseCo->dbLink);
$id = (isset($_REQUEST['id']) && $_REQUEST['id'] !== '')
    ? $xssClean->clean_input($_REQUEST['id'])
    : $poetsPageId;
$saintsApprovedSql = saints_public_listing_status_sql();

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
                        <span class="text-primary fw-medium" onclick="clearPage()">Clear</span>
                    </div>
                    <div class="sidebar-filters-body p-3 p-xl-4">

                        <div class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <h4 class="fs-5 fw-semibold mb-2">Filter by name</h4>
                            </div>
                            <?php
    $select = "SELECT * FROM other_page WHERE page_id ='$id' $saintsApprovedSql ORDER BY title ASC";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
            $title = $Row['title'];
            $index_id = $Row['index_id'];
?>
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="<?php echo $index_id; ?>" id="god<?php echo $Row['index_id']; ?>">
                <label class="form-check-label" for="god<?php echo $Row['index_id']; ?>">
                    <?php echo $title; ?>
                </label>
            </div>
<?php
        }
    } else {
        echo "<p class='text-center'>No records found.</p>";
    }
?>
                        </div>
                        <a href="saints.php?id=<?php echo $id; ?>" class="align-items-center d-flex fw-medium gap-2 justify-content-center mt-2 small text-center">
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
                    <?php
                    $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT name FROM category WHERE index_id='" . $id . "'");
                    $res3 = mysqli_fetch_object($sql3);
                    $saintsListCount = 0;
                    $countResult = mysqli_query(
                        $DatabaseCo->dbLink,
                        "SELECT COUNT(*) AS total FROM other_page WHERE page_id = '$id' $saintsApprovedSql"
                    );
                    if ($countResult && ($countRow = mysqli_fetch_assoc($countResult))) {
                        $saintsListCount = (int) $countRow['total'];
                    }
                    ?>
                    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary">
                        Temples in <?php echo htmlspecialchars($res3->name ?? ''); ?>
                        <span class="fs-4 text-muted">(<?php echo $saintsListCount; ?>)</span>
                    </div>
                </div>
                <div id="listings-container" class="listings grid-view listings-yellow-border" data-page-id="<?php echo htmlspecialchars((string) $id); ?>">
                    <?php
                    $select = "SELECT * FROM other_page WHERE page_id = '$id' $saintsApprovedSql ORDER BY order_by ASC";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                            $photos = $Row['photos'];
                            $title = $Row['title'];
                            $photoSrc = saints_photo_src($photos, $Row['page_id'], $DatabaseCo->dbLink, $title);
                    ?>
                            <div class="listing">
                                <a href="saints-details.php?id=<?php echo $Row['index_id']; ?>&page_id=<?php echo $Row['page_id']; ?>" class="d-block" aria-label="<?php echo htmlspecialchars($title); ?>">
                                    <div class="listing-img-bg" style="background-image: url('<?php echo htmlspecialchars($photoSrc, ENT_QUOTES, 'UTF-8'); ?>');"></div>
                                </a>
                                <div class="listing-details">
                                    <a href="saints-details.php?id=<?php echo $Row['index_id']; ?>&page_id=<?php echo $Row['page_id']; ?>">
                                        <div class="listing-title"><?php echo htmlspecialchars($title); ?></div>
                                    </a>
                                    <div class="listing-rating text-dark">
                                        <a href="saints-details.php?id=<?php echo htmlspecialchars($Row['index_id']); ?>&page_id=<?php echo htmlspecialchars($Row['page_id']); ?>">Read more</a>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once './include/footer.php' ?>
<script>
    $('#country').change(function() {
        let countryCode = $(this).val();
        $.ajax({
            url: './app/get_states.php',
            type: 'POST',
            data: { country_code: countryCode },
            success: function(response) {
                $('#state').html(response);
                $('#city').html('<option selected disabled>Select City</option>');
            }
        });
    });

    $('#state').change(function() {
        let stateCode = $(this).val();
        $.ajax({
            url: './app/get_cities.php',
            type: 'POST',
            data: { state_code: stateCode },
            success: function(response) {
                $('#city').html(response);
            }
        });
    });
</script>
<script>
    function getSaintsPageId() {
        const container = document.getElementById('listings-container');
        const fromData = container && container.getAttribute('data-page-id');
        const fromUrl = new URLSearchParams(window.location.search).get('id');
        return fromData || fromUrl || <?php echo json_encode($poetsPageId); ?>;
    }

    function fetchListings() {
        const selectedFilters = [];
        document.querySelectorAll('.sidebar-filters .form-check-input:checked').forEach(checkbox => {
            selectedFilters.push(checkbox.value);
        });

        const params = new URLSearchParams();
        params.append('page_id', getSaintsPageId());
        params.append('saints_listing', '1');

        if (selectedFilters.length > 0) {
            params.append('selectedFilters_saints', selectedFilters.join(','));
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'fetch_listings.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status !== 200) {
                return;
            }
            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                return;
            }
            const container = document.getElementById('listings-container');
            if (container) {
                container.innerHTML = response.listings || '<p class="text-center">No records found.</p>';
            }
        };
        xhr.send(params.toString());
    }

    document.querySelectorAll('.sidebar-filters .form-check-input').forEach(checkbox => {
        checkbox.addEventListener('change', () => fetchListings());
    });
</script>
<script>
    function clearPage() {
        location.reload();
    }
</script>
