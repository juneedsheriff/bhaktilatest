<?php ob_start();
include_once './includes/header.php';
if (!empty($_REQUEST['del_t'])) {
    $del_id = $_REQUEST['del_t'];
    echo $del_id; // For debugging, you can remove this later.

    $query = "DELETE FROM `temples` WHERE `temples`.`index_id` = '$del_id'";

    if ($DatabaseCo->dbLink->query($query)) {
        header('Location: temple-listing.php');
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
            <h6 class="fs-17 fw-semi-bold my-1">Temples in India</h6>
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
                        <?php $select = "SELECT * FROM `temples` WHERE index_id!='0' ORDER BY index_id DESC";
                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
                        $num_rows = mysqli_num_rows($SQL_STATEMENT);
                        if ($num_rows != 0) {
                            $i = 1;
                            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
                                // Fetch all data from courses table based on category_id
                                $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT god_name FROM god WHERE index_id='" . $Row->god_id . "'");
                                $res3 = mysqli_fetch_object($sql3);
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
                                    <td><?php echo $res3->god_name; ?></td>
                               
                                    <td>
                                    <?php if ($Row->status === 'approved') { ?>
    <div class="icon-container">
        <i class="fa fa-thumbs-up text-success" style="font-size: 20px;"></i>
    </div>
<?php } else { ?>
    <div class="icon-container">
        <i class="fa fa-thumbs-down text-danger" style="font-size: 20px;"></i>
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
    if ($Row->status === 'unapproved'): ?>
        <!-- Staff can edit if status is unapproved -->
        <a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" 
           href="add-temple.php?id=<?php echo $Row->index_id; ?>">
            <i class="fas fa-pencil-alt"></i>
        </a>
    <?php 
    elseif ($Row->status === 'approved'): ?>
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
                                <td colspan="9">
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