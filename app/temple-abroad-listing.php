<?php ob_start();
include_once './includes/abroad_temple_listing_query.php';

$list_temple_status = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], abroad_temple_listing_valid_tabs(), true))
    ? (string) $_REQUEST['temple_status']
    : '';

include_once './includes/header.php';
$db = $DatabaseCo->dbLink;
$optWhere = abroad_temple_listing_opt_where($list_temple_status);

$filters = abroad_temple_listing_parse_filters($db, $_GET);
$f_country = $filters['f_country'];
$f_place = $filters['f_place'];
$f_god = $filters['f_god'];
$f_tid = $filters['f_tid'];
$filter_sql = $filters['filter_sql'];
$has_listing_filters = $filters['has_listing_filters'];

$listing_filter_qs = [];
if ($list_temple_status !== '') {
    $listing_filter_qs['temple_status'] = $list_temple_status;
}
$listing_clear_url = 'temple-abroad-listing.php' . ($list_temple_status !== '' ? '?temple_status=' . urlencode($list_temple_status) : '');
$listing_primary_field = 'f_country';
$listing_primary_label = 'Country';
$listing_primary_value_key = 'country_code';
$listing_primary_label_key = 'country_name';
$listing_place_label = 'Place Name';
$listing_show_type_filter = false;
$opt_states = mysqli_query($db, "SELECT DISTINCT a.country AS country_code, c.country_name FROM abroad a LEFT JOIN country c ON c.country_code = a.country WHERE $optWhere AND TRIM(COALESCE(a.country,'')) != '' ORDER BY c.country_name");
$optPlacesWhere = $optWhere;
if ($f_country !== '' && strtoupper($f_country) !== 'ALL') {
    $optPlacesWhere .= " AND a.`country` = '" . $db->real_escape_string($f_country) . "' ";
}
$opt_places = mysqli_query($db, "SELECT DISTINCT a.temple_place AS place_value, a.temple_place AS place_label FROM abroad a WHERE $optPlacesWhere AND TRIM(COALESCE(a.temple_place,'')) != '' ORDER BY a.temple_place");
$opt_gods = mysqli_query($db, "SELECT DISTINCT a.god_id, g.god_name FROM abroad a INNER JOIN god g ON g.index_id = a.god_id WHERE $optWhere AND a.god_id > 0 ORDER BY g.god_name");
$opt_temples = false;
$optTempleWhere = $optWhere;
if ($f_country !== '' && strtoupper($f_country) !== 'ALL') {
    $optTempleWhere .= " AND a.`country` = '" . $db->real_escape_string($f_country) . "' ";
}
if ($f_place !== '' && strtoupper($f_place) !== 'ALL') {
    $optTempleWhere .= " AND a.`temple_place` = '" . $db->real_escape_string($f_place) . "' ";
}
if ($f_god > 0) {
    $optTempleWhere .= ' AND a.`god_id` = ' . $f_god . ' ';
}
$optTempleSql = "SELECT a.index_id, a.title FROM abroad a WHERE $optTempleWhere ORDER BY a.title LIMIT 5000";
if ($f_tid > 0) {
    $optTempleSql = "(SELECT a.index_id, a.title FROM abroad a WHERE $optTempleWhere)
        UNION
        (SELECT a.index_id, a.title FROM abroad a WHERE $optWhere AND a.index_id = " . $f_tid . ")
        ORDER BY title LIMIT 5001";
}
$opt_temples = mysqli_query($db, $optTempleSql);
if (!empty($_REQUEST['del_t'])) {
    $del_id = $_REQUEST['del_t'];

    $query = "DELETE FROM `abroad` WHERE `abroad`.`index_id` = '$del_id'";

    if ($DatabaseCo->dbLink->query($query)) {
        $qs = [];
        if ($list_temple_status !== '') {
            $qs['temple_status'] = $list_temple_status;
        }
        foreach (['f_country', 'f_place', 'f_god', 'f_tid'] as $fk) {
            if (!isset($_REQUEST[$fk])) {
                continue;
            }
            $v = $_REQUEST[$fk];
            if ($v === '' || $v === null) {
                continue;
            }
            if (is_string($v) && strtoupper($v) === 'ALL') {
                continue;
            }
            if (($fk === 'f_god' || $fk === 'f_tid') && (int) $v <= 0) {
                continue;
            }
            $qs[$fk] = $v;
        }
        $redir = 'temple-abroad-listing.php' . (!empty($qs) ? '?' . http_build_query($qs) : '');
        header('Location: ' . $redir);
        exit;
    } else {
        die("Error: " . mysqli_error($DatabaseCo->dbLink));
    }
}
?>
<style>
    .icon-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        text-align: center;
    }
</style>
<!-- Page-Title -->
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1"><?php
                if ($list_temple_status === 'approved') {
                    echo 'Approved Temple';
                } elseif ($list_temple_status === 'pending') {
                    echo 'Approval Pending';
                } elseif ($list_temple_status === 'rejected') {
                    echo 'Rejected Temple';
                } else {
                    echo 'Temples in Abroad';
                }
            ?></h6>

        </div>
        <div class="text-end">
            <a href="add-abroad-temple.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Temples</a>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php include __DIR__ . '/includes/listing_temple_filters_ui.php'; ?>
                <table id="temple-abroad-listing-table" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-15">Temple Photo</th>
                            <th class="w-25">Name</th>
                            <th class="w-20">Temple Place</th>
                            <th class="w-20">Status</th>
                            <th class="w-5">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div id="delete-board-alert" class="modal fade alert-box">
    <form action="" method="post" name="delete_form" id="delete_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Delete Temple</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <h5 class="text-center">Delete Temple Details?</h5>
                    <p>Are you sure you want to delete this Temple? All data will be lost.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="form_action" value="Delete" />
                    <input type="hidden" name="del_t" id="delid" value="" />
                    <?php if ($list_temple_status !== ''): ?>
                    <input type="hidden" name="temple_status" value="<?php echo htmlspecialchars($list_temple_status, ENT_QUOTES, 'UTF-8'); ?>" />
                    <?php endif; ?>
                    <?php
                    foreach (['f_country', 'f_place', 'f_god', 'f_tid'] as $fh) {
                        if (!isset($_GET[$fh])) {
                            continue;
                        }
                        $fv = $_GET[$fh];
                        if ($fv === '' || $fv === null) {
                            continue;
                        }
                        if (is_string($fv) && strtoupper($fv) === 'ALL') {
                            continue;
                        }
                        if (($fh === 'f_god' || $fh === 'f_tid') && (int) $fv <= 0) {
                            continue;
                        }
                        echo '<input type="hidden" name="' . htmlspecialchars($fh, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars((string) $fv, ENT_QUOTES, 'UTF-8') . '" />';
                    }
                    ?>
                    <button class="btn raised bg-primary text-white ml-2 mt-2" data-dismiss="modal">Cancel</button>
                    <button name="delete_now" type="submit" class="btn mt-2 btn-dash btn-danger raised has-icon" id="modalDelete" value="Delete">Delete</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php
include_once './includes/footer.php';
?>

<script type="text/javascript">
    $("#add_form").submit(function(event) {
        event.preventDefault();
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form_data = $("#add_form").serialize();
        $.ajax({
            url: post_url,
            type: request_method,
            dataType: "text",
            data: form_data
        }).done(function(response) {
            console.log(response);
        });
    });
    $("#add_form").submit(function(event) {
        event.preventDefault();
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form = $('#add_form')[0];
        var data = new FormData(form);
        $.ajax({
            type: "POST",
            url: "packages-process.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(data) {
                window.location.reload();
            },
            error: function(event) {
                console.log("ERROR : ", event);
                window.location.reload();
            }
        });
    })
    $('.drop-edit-board').click(function() {
        var id = $(this).data('id');
        $("#pget_id").val(id);
        $("#vcategory").hide();
        var dataString = 'TourAddedit=' + id;
        $("#hidden_id").val(id);
        $.ajax({
            url: "packages-process.php",
            type: "POST",
            dataType: "text",
            data: dataString
        }).done(function(html) {
            var arr = html.split("|");
            $("#package_name").val(arr[0]);
            $("#package_price").val(arr[1]);
            $("#number_of_nights").val(arr[2]);
            $("#others_details").val(arr[3]);
            $("#number_of_days").val(arr[4]);

        });

    });
</script>
<script>
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.delete-board');
        if (!button) {
            return;
        }
        const id = button.getAttribute('data-id');
        document.getElementById('delid').value = id;
    });
</script>
<script>
    $(function() {
        var ajaxData = {
            f_country: <?php echo json_encode($f_country); ?>,
            f_place: <?php echo json_encode($f_place); ?>,
            f_god: <?php echo (int) $f_god; ?>,
            f_tid: <?php echo (int) $f_tid; ?>,
            temple_status: <?php echo json_encode($list_temple_status); ?>
        };

        $('#temple-abroad-listing-table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 25,
            lengthChange: false,
            order: [[0, 'desc']],
            ajax: {
                url: 'temple-abroad-listing-data.php',
                type: 'GET',
                data: function(d) {
                    d.f_country = ajaxData.f_country;
                    d.f_place = ajaxData.f_place;
                    d.f_god = ajaxData.f_god;
                    d.f_tid = ajaxData.f_tid;
                    d.temple_status = ajaxData.temple_status;
                }
            },
            columnDefs: [
                { orderable: false, searchable: false, targets: [1, 4, 5] }
            ]
        });

        function loadAbroadPlaces(countryCode, selectedPlace) {
            $.get('temple-abroad-listing-filter-places.php', {
                country_code: countryCode || 'ALL',
                temple_status: ajaxData.temple_status,
                selected: selectedPlace || 'ALL'
            }).done(function(html) {
                $('#f_place').html(html);
            });
        }

        $('#f_country').on('change', function() {
            loadAbroadPlaces($(this).val(), 'ALL');
        });
    });
</script>
<script>
    $(".status-dropdown").on("change", function(event) {
        event.preventDefault();

        var postUrl = $(this).data("action");
        var id = $(this).data("id");
        var status = $(this).val();

        var data = {
            abroad_id: id,
            abroad_status: status
        };

        $.ajax({
            url: 'status_approved.php',
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
                console.log(response.message);
                if (response.success) {
                    toastr.success("Status updated successfully");
                } else {
                    toastr.error(response.message || "An error occurred while updating the status.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                toastr.error("An error occurred: " + (xhr.responseJSON?.message || xhr.responseText || "Unknown error"));
            }
        });
    });

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    document.addEventListener('DOMContentLoaded', function() {
        const statusDropdowns = document.querySelectorAll('.status-dropdown');

        statusDropdowns.forEach(function(dropdown) {
            updateSelectBackground(dropdown);

            dropdown.addEventListener('change', function() {
                updateSelectBackground(dropdown);
            });
        });

        function updateSelectBackground(dropdown) {
            dropdown.classList.remove('approved', 'unapproved');

            if (dropdown.hasAttribute('multiple')) {
                if (dropdown.selectedOptions.length > 0) {
                    dropdown.classList.add('approved', 'unapproved');
                }
            } else {
                if (dropdown.value === 'approved') {
                    dropdown.classList.add('approved');
                } else if (dropdown.value === 'unapproved') {
                    dropdown.classList.add('unapproved');
                }
            }
        }
    });
</script>
