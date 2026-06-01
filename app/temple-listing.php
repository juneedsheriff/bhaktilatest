<?php ob_start();
$valid_temple_tabs = ['approved', 'pending', 'rejected'];
$list_temple_status = (!empty($_REQUEST['temple_status']) && in_array((string) $_REQUEST['temple_status'], $valid_temple_tabs, true)) ? (string) $_REQUEST['temple_status'] : '';
$india_temple_rejected_sql = "( LOWER(TRIM(COALESCE(`status`, ''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE(`status`, '')) = '' )";
$status_sql_fragment = '';
if ($list_temple_status === 'approved') {
    $status_sql_fragment = " AND LOWER(TRIM(COALESCE(`status`,''))) = 'approved' ";
} elseif ($list_temple_status === 'pending') {
    $status_sql_fragment = " AND LOWER(TRIM(COALESCE(`status`,''))) = 'unapproved' ";
} elseif ($list_temple_status === 'rejected') {
    $status_sql_fragment = ' AND ' . $india_temple_rejected_sql . ' ';
}
include_once './includes/header.php';
$db = $DatabaseCo->dbLink;
$optWhere = "t.index_id!='0' " . str_replace('`status`', 't.`status`', $status_sql_fragment);
$f_state = isset($_GET['f_state']) ? trim((string) $_GET['f_state']) : '';
$f_place = isset($_GET['f_place']) ? trim((string) $_GET['f_place']) : '';
$f_type = isset($_GET['f_type']) ? trim((string) $_GET['f_type']) : '';
$f_god = isset($_GET['f_god']) ? (int) $_GET['f_god'] : 0;
$f_tid = isset($_GET['f_tid']) ? (int) $_GET['f_tid'] : 0;
$filter_sql = '';
if ($f_state !== '' && strtoupper($f_state) !== 'ALL') {
    $filter_sql .= " AND `state` = '" . $db->real_escape_string($f_state) . "' ";
}
if ($f_place !== '' && strtoupper($f_place) !== 'ALL') {
    $filter_sql .= " AND `city` = '" . $db->real_escape_string($f_place) . "' ";
}
if ($f_type === '0' || $f_type === '1') {
    $filter_sql .= " AND `my_stery` = '" . $db->real_escape_string($f_type) . "' ";
}
if ($f_god > 0) {
    $filter_sql .= ' AND `god_id` = ' . $f_god . ' ';
}
if ($f_tid > 0) {
    $filter_sql .= ' AND `index_id` = ' . $f_tid . ' ';
}
$listing_filter_qs = [];
if ($list_temple_status !== '') {
    $listing_filter_qs['temple_status'] = $list_temple_status;
}
$listing_place_label = 'City';
$listing_type_label = 'Temple Type';
$opt_types_mode = 'mystery';
$opt_types = null;
$opt_states = mysqli_query($db, "SELECT DISTINCT t.state AS state_code, s.state_name FROM temples t LEFT JOIN state s ON s.state_code = t.state WHERE $optWhere AND TRIM(COALESCE(t.state,'')) != '' ORDER BY s.state_name");
$opt_places = mysqli_query($db, "SELECT DISTINCT t.city AS place_value, c.city_name AS place_label FROM temples t INNER JOIN city c ON c.city_id = t.city WHERE $optWhere AND TRIM(COALESCE(c.city_name,'')) != '' ORDER BY c.city_name");
$opt_gods = mysqli_query($db, "SELECT DISTINCT t.god_id, g.god_name FROM temples t INNER JOIN god g ON g.index_id = t.god_id WHERE $optWhere AND t.god_id > 0 ORDER BY g.god_name");
$opt_temples = mysqli_query($db, "SELECT t.index_id, t.title FROM temples t WHERE $optWhere ORDER BY t.title");
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
            if ($fk === 'f_type' && $v !== '0' && $v !== '1' && strtoupper((string) $v) === 'ALL') {
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
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-15">Temple Photo</th>

                            <th class="w-25">Name</th>
                            <th class="w-20">God</th>
                            <!-- <th class="w-10">Date</th> -->
                        
                            <th class="w-5">Status</th>
                          
                            <th class="w-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $select = "SELECT t.*, g.god_name AS listing_god_name FROM `temples` t
                            LEFT JOIN `god` g ON g.index_id = t.god_id
                            WHERE t.index_id!='0' " . str_replace('`status`', 't.`status`', $status_sql_fragment)
                            . str_replace(['`state`', '`city`', '`my_stery`', '`god_id`', '`index_id`'], ['t.`state`', 't.`city`', 't.`my_stery`', 't.`god_id`', 't.`index_id`'], $filter_sql)
                            . " ORDER BY t.index_id DESC";
                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
                        $listing_query_error = '';
                        if (!$SQL_STATEMENT) {
                            $listing_query_error = mysqli_error($DatabaseCo->dbLink);
                            $num_rows = 0;
                        } else {
                            $num_rows = mysqli_num_rows($SQL_STATEMENT);
                        }
                        if ($listing_query_error !== '') { ?>
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-danger mb-0">Unable to load temples. <?php echo htmlspecialchars($listing_query_error, ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                            </tr>
                        <?php } elseif ($num_rows != 0) {
                            $i = 1;
                            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
                                $rowStatus = strtolower(trim((string) ($Row->status ?? '')));
                                $is_rejected_row = ($rowStatus === 'rejected' || $rowStatus === 'reject' || $rowStatus === 'denied' || $rowStatus === 'disapproved' || $rowStatus === '');
                                // Fetch all data from courses table based on category_id
                                $godName = trim((string) ($Row->listing_god_name ?? ''));
                                if ($godName === '') {
                                    $godId = (int) ($Row->god_id ?? 0);
                                    if ($godId > 0) {
                                        $sql3 = mysqli_query($DatabaseCo->dbLink, 'SELECT god_name FROM god WHERE index_id=' . $godId . ' LIMIT 1');
                                        if ($sql3 && ($res3 = mysqli_fetch_object($sql3)) && !empty($res3->god_name)) {
                                            $godName = $res3->god_name;
                                        }
                                    }
                                }
                        ?>
                                <tr>
                                    <td><?php echo $i;
                                        $i++; ?></td>
                                    <td>
                                        <?php if ($Row->photos != '') { ?>
                                            <a href="./uploads/temple/<?php echo $Row->photos; ?>" target="_blank"><img src="./uploads/temple/<?php echo $Row->photos; ?>" class=" header-profile-user" width="60" alt="" data-demo-src="./uploads/temple/<?php echo $Row->photos; ?>"></a>
                                        <?php } ?>
                                    </td>

                                    <td><?php echo $Row->title; ?></td>
                                    <td><?php echo htmlspecialchars($godName, ENT_QUOTES, 'UTF-8'); ?></td>
                               
                                    <td>
                                    <?php if ($rowStatus === 'approved') { ?>
    <div class="icon-container">
        <i class="fa fa-thumbs-up text-success" style="font-size: 20px;" title="Approved"></i>
    </div>
<?php } elseif ($is_rejected_row) { ?>
    <div class="icon-container">
        <i class="fa fa-ban text-danger" style="font-size: 20px;" title="Rejected"></i>
    </div>
<?php } else { ?>
    <div class="icon-container">
        <i class="fa fa-clock text-warning" style="font-size: 20px;" title="Approval pending"></i>
    </div>
<?php } ?>




                                    </td>
                              
                                    <!-- <td><?php echo $Row->content; ?></td> -->
                                    <td>
                                    <?php 
if ($user_role === 'Admin'): ?>
    <!-- Admin has access to Edit and Delete options -->
    <a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" 
       href="add-temple.php?id=<?php echo $Row->index_id; ?>">
        <i class="fas fa-pencil-alt"></i>
    </a> &nbsp; &nbsp;
    
    <a class="btn btn-sm p-2 btn-danger delete-board alert-box-trigger waves-effect waves-light kill-drop text-white"
       data-modal="delete-board-alert"
       data-toggle="modal"
       data-target="#delete-board-alert"
       href="#0"
       data-id="<?php echo $Row->index_id; ?>"
       id="delete-board<?php echo $Row->index_id; ?>">
        <i class="fa fa-trash text-white"></i>
    </a>
<?php 
elseif ($user_role === 'Staff'):
    if ($rowStatus === 'unapproved'): ?>
        <!-- Staff can edit if status is unapproved -->
        <a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" 
           href="add-temple.php?id=<?php echo $Row->index_id; ?>">
            <i class="fas fa-pencil-alt"></i>
        </a>
    <?php 
    elseif ($rowStatus === 'approved'): ?>
        <!-- Staff has no options for approved status -->
        <!-- No buttons displayed -->
    <?php 
    endif; 
endif; 
?>

                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6">
                                    <div align="center"><strong>No Records!</strong></div>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
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
    document.querySelectorAll('.delete-board').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('delid').value = id;
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