<?php ob_start();
include_once './includes/temple_listing_query.php';

$list_temple_status = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], temple_listing_valid_tabs(), true))
    ? (string) $_REQUEST['temple_status']
    : '';

include_once './includes/header.php';
$db = $DatabaseCo->dbLink;
$optWhere = temple_listing_opt_where($list_temple_status);

$filters = temple_listing_parse_filters($db, $_GET);
$f_state = $filters['f_state'];
$f_place = $filters['f_place'];
$f_type = $filters['f_type'];
$f_god = $filters['f_god'];
$f_tid = $filters['f_tid'];
$filter_sql = $filters['filter_sql'];
$has_listing_filters = $filters['has_listing_filters'];

$listing_filter_qs = [];
if ($list_temple_status !== '') {
    $listing_filter_qs['temple_status'] = $list_temple_status;
}
$listing_clear_url = 'temple-listing.php' . ($list_temple_status !== '' ? '?temple_status=' . urlencode($list_temple_status) : '');
$listing_place_label = 'City';
$listing_type_label = 'Temple Type';
$opt_types_mode = 'list';
$opt_types = array_map(static function ($type) {
    return ['type_id' => $type, 'type_label' => $type];
}, temple_table_type_options());
$opt_states = mysqli_query($db, "SELECT DISTINCT t.state AS state_code, s.state_name FROM temples t LEFT JOIN state s ON s.state_code = t.state WHERE $optWhere AND TRIM(COALESCE(t.state,'')) != '' ORDER BY s.state_name");
$optPlacesWhere = $optWhere;
if ($f_state !== '' && strtoupper($f_state) !== 'ALL') {
    $optPlacesWhere .= " AND t.`state` = '" . $db->real_escape_string($f_state) . "' ";
}
$opt_places = mysqli_query($db, "SELECT DISTINCT t.city AS place_value, c.city_name AS place_label FROM temples t INNER JOIN city c ON c.city_id = t.city WHERE $optPlacesWhere AND TRIM(COALESCE(c.city_name,'')) != '' ORDER BY c.city_name");
$opt_gods = mysqli_query($db, "SELECT DISTINCT t.god_id, g.god_name FROM temples t INNER JOIN god g ON g.index_id = t.god_id WHERE $optWhere AND t.god_id > 0 ORDER BY g.god_name");
$opt_temples = false;
$optTempleWhere = $optWhere;
if ($f_state !== '' && strtoupper($f_state) !== 'ALL') {
    $optTempleWhere .= " AND t.`state` = '" . $db->real_escape_string($f_state) . "' ";
}
if ($f_place !== '' && strtoupper($f_place) !== 'ALL') {
    $optTempleWhere .= " AND t.`city` = '" . $db->real_escape_string($f_place) . "' ";
}
if ($f_type !== '' && strtoupper($f_type) !== 'ALL') {
    $optTempleWhere .= " AND t.`table_type` = '" . $db->real_escape_string($f_type) . "' ";
}
if ($f_god > 0) {
    $optTempleWhere .= ' AND t.`god_id` = ' . $f_god . ' ';
}
$optTempleSql = "SELECT t.index_id, t.title FROM temples t WHERE $optTempleWhere ORDER BY t.title LIMIT 5000";
if ($f_tid > 0) {
    $optTempleSql = "(SELECT t.index_id, t.title FROM temples t WHERE $optTempleWhere)
        UNION
        (SELECT t.index_id, t.title FROM temples t WHERE $optWhere AND t.index_id = " . $f_tid . ")
        ORDER BY title LIMIT 5001";
}
$opt_temples = mysqli_query($db, $optTempleSql);
if (!empty($_REQUEST['del_t'])) {
    $del_id = $_REQUEST['del_t'];

    $query = "DELETE FROM `temples` WHERE `temples`.`index_id` = '$del_id'";

    if ($DatabaseCo->dbLink->query($query)) {
        $qs = [];
        if ($list_temple_status !== '') {
            $qs['temple_status'] = $list_temple_status;
        }
        foreach (['f_state', 'f_place', 'f_type', 'f_god', 'f_tid'] as $fk) {
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
            if ($fk === 'f_type' && strtoupper((string) $v) === 'ALL') {
                continue;
            }
            $qs[$fk] = $v;
        }
        $redir = 'temple-listing.php' . (!empty($qs) ? '?' . http_build_query($qs) : '');
        header('Location: ' . $redir);
        exit;
    } else {
        die("Error: " . mysqli_error($DatabaseCo->dbLink));
    }
}

// Check if required POST data is present


?>
<style>
    .icon-container {
    display: flex;
    justify-content: center; /* Align horizontally */
    align-items: center; /* Align vertically */
    height: 100%; /* Ensure container height is enough for vertical centering */
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
                    echo 'Temples in India';
                }
            ?></h6>
            <!-- <p class="mb-0">Temples Listing.</p> -->

        </div>
        <div class="text-end">
            <a href="add-temple.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Temples</a>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php include __DIR__ . '/includes/listing_temple_filters_ui.php'; ?>
                <table id="temple-listing-table" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-15">Temple Photo</th>
                            <th class="w-25">Name</th>
                            <th class="w-20">God</th>
                            <th class="w-15">Temple Type</th>
                            <th class="w-5">Status</th>
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
                    foreach (['f_state', 'f_place', 'f_type', 'f_god', 'f_tid'] as $fh) {
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
    // $("#add_form").submit(function(event) {
    //     event.preventDefault();
    //     var post_url = $(this).attr("action");
    //     var request_method = $(this).attr("method");
    //     var form_data = $("#add_form").serialize();
    //     //alert(form_data);
    //     $.ajax({
    //         url: post_url,
    //         type: request_method,
    //         dataType: "text",
    //         data: form_data
    //     }).done(function(response) {
    //         console.log(response);
    //         //window.location.reload();
    //     });
    // });
    $("#add_form").submit(function(event) {
        event.preventDefault();
        var status = this.value;
        const id = this.getAttribute('data-id');
        var request_method = $(this).attr("method");


        //alert(data);
        $.ajax({
            type: "POST",
            url: "packages-process.php",
            data: {
                id: id,
                status: status
            },
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(data) {
                var newPatient = $.trim(data);
                //console.log(newPatient);
                // $("#new_patient_id").val(newPatient);
                // $("#hideDate").hide();             
                // $("#hideTrouble").hide();              
                // $('#patient_details').modal('hide');
                // $('#appointment_add').modal('show');
                //   window.location.href="billing.php?type=1";
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
        }).done(function(html) { //alert(html);
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
            f_state: <?php echo json_encode($f_state); ?>,
            f_place: <?php echo json_encode($f_place); ?>,
            f_type: <?php echo json_encode($f_type); ?>,
            f_god: <?php echo (int) $f_god; ?>,
            f_tid: <?php echo (int) $f_tid; ?>,
            temple_status: <?php echo json_encode($list_temple_status); ?>
        };

        $('#temple-listing-table').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            pageLength: 25,
            lengthChange: false,
            order: [[0, 'desc']],
            ajax: {
                url: 'temple-listing-data.php',
                type: 'GET',
                data: function(d) {
                    d.f_state = ajaxData.f_state;
                    d.f_place = ajaxData.f_place;
                    d.f_type = ajaxData.f_type;
                    d.f_god = ajaxData.f_god;
                    d.f_tid = ajaxData.f_tid;
                    d.temple_status = ajaxData.temple_status;
                }
            },
            columnDefs: [
                { orderable: false, searchable: false, targets: [1, 5, 6] }
            ]
        });
    });
</script>
<script>
    $(function() {
        var filterStatus = <?php echo json_encode($list_temple_status); ?>;

        function loadListingCities(stateCode, selectedPlace) {
            $.get('temple-listing-filter-cities.php', {
                state_code: stateCode || 'ALL',
                temple_status: filterStatus,
                selected: selectedPlace || 'ALL'
            }).done(function(html) {
                $('#f_place').html(html);
            });
        }

        $('#f_state').on('change', function() {
            loadListingCities($(this).val(), 'ALL');
        });
    });
</script>
<!-- Include the necessary CDN for Fetch API (most browsers already support it) -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    $(".status-dropdown").on("change", function(event) {
        event.preventDefault();

        // Get necessary data attributes
        var postUrl = $(this).data("action"); // URL to send the request
        var id = $(this).data("id"); // Record ID
        var status = $(this).val(); // Selected status value

        // Prepare data to send
        var data = {
            id: id,
            status: status
        };

        // Send POST request
        $.ajax({
            url: 'status_approved.php', // URL from data-action attribute
            type: "POST",
            data: data,
            dataType: "json", // Expecting JSON response
            success: function(response) {
                console.log(response.message);
                if (response.success) {
                    // Display success toast message
                    toastr.success("Status updated successfully");
                    // window.location.reload(); // Optional: Reload the page
                } else {
                    // Display error toast message
                    toastr.error(response.message || "An error occurred while updating the status.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                // Display error toast message
                toastr.error("An error occurred: " + (xhr.responseJSON?.message || xhr.responseText || "Unknown error"));
            }
        });
    });

    // Customize Toastr (optional)
    toastr.options = {
        "closeButton": true, // Add close button
        "debug": false,
        "newestOnTop": true, // Display newest message on top
        "progressBar": true, // Show progress bar
        "positionClass": "toast-top-right", // Set position on screen
        "preventDuplicates": true, // Prevent duplicate messages
        "onclick": null,
        "showDuration": "300", // Toast display duration (in ms)
        "hideDuration": "1000", // Duration to hide (in ms)
        "timeOut": "5000", // Time before auto close (in ms)
        "extendedTimeOut": "1000", // Extended time for hovering (in ms)
        "showEasing": "swing", // Animation for showing toast
        "hideEasing": "linear", // Animation for hiding toast
        "showMethod": "fadeIn", // Show animation
        "hideMethod": "fadeOut" // Hide animation
    };
    // Wait for the DOM to be fully loaded
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        const statusDropdowns = document.querySelectorAll('.status-dropdown');

        // Loop through all the select elements
        statusDropdowns.forEach(function(dropdown) {
            // Set initial background color based on the default selection
            updateSelectBackground(dropdown);

            // Add event listener to change the background when selection changes
            dropdown.addEventListener('change', function() {
                updateSelectBackground(dropdown);
            });
        });

        // Function to update background based on the selected value(s)
        function updateSelectBackground(dropdown) {
            // Remove all background classes first
            dropdown.classList.remove('approved', 'unapproved');

            // If it's a multiple select
            if (dropdown.hasAttribute('multiple')) {
                // Check for "approved" or "unapproved" in selected values
                if (dropdown.selectedOptions.length > 0) {
                    dropdown.classList.add('approved', 'unapproved');
                }
            } else {
                // Single select - check the selected value
                if (dropdown.value === 'approved') {
                    dropdown.classList.add('approved');
                } else if (dropdown.value === 'unapproved') {
                    dropdown.classList.add('unapproved');
                }
            }
        }
    });
</script>