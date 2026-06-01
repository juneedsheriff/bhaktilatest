<?php ob_start();
include_once './includes/header.php';
error_reporting(1);
if (!empty($_REQUEST['del_t'])) {
    $del_id = $_REQUEST['del_t'];
    echo $del_id; // For debugging, you can remove this later.

    $query = "DELETE FROM `mystery` WHERE `mystery`.`index_id` = '$del_id'";

    if ($DatabaseCo->dbLink->query($query)) {
        header('Location:temple-mystery-listing.php');
        exit;
    } else {
        die("Error: " . mysqli_error($DatabaseCo->dbLink));
    }
}
?>
<style>

</style>
<!-- Page-Title -->
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1"> Mystery Temples</h6>
            <!-- <p class="mb-0">Temple Listing.</p> -->

        </div>
        <div class="text-end">
            <!-- <a href="add-mystery-temple.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Temples</a> -->
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-15">Temple Photo</th>

                            <th class="w-25">Name</th>
                            <th class="w-25">God Name</th>
                            <!-- <th class="w-20">Short Description</th> -->
                            <!-- <th class="w-10">Date</th> -->
                            <!-- <th class="w-25">Description</th> -->
                            <!-- <th class="w-5">Action</th> -->
                        </tr>
                    </thead>
                    <tbody>

<!-- Temples Section -->
<?php
$select = "SELECT * FROM `temples` WHERE my_stery = 1 ORDER BY index_id DESC";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
$num_rows = mysqli_num_rows($SQL_STATEMENT);

if ($num_rows != 0) {
    $i = 1;
    while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
        $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT god_name FROM god WHERE index_id='" . $Row->god_id . "'");
        $res1 = mysqli_fetch_object($sql3);
?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>
                <?php if (!empty($Row->photos)) { ?>
                    <a href="./uploads/Temple/<?php echo $Row->photos; ?>" target="_blank">
                        <img src="./uploads/temple/<?php echo $Row->photos; ?>" class="header-profile-user" width="60" alt="">
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $Row->title; ?></td>
            <td><?php echo $res1->god_name; ?></td>
           
          
        
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="7">
            <div align="center"><strong>No Records!</strong></div>
        </td>
    </tr>
<?php } ?>

<!-- Iconic Section -->
<?php
$select = "SELECT * FROM `iconic` WHERE my_stery = 1 ORDER BY index_id DESC";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
$num_rows = mysqli_num_rows($SQL_STATEMENT);

if ($num_rows != 0) {
 
    while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
        $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT god_name FROM god WHERE index_id='" . $Row->god_id . "'");
        $res2 = mysqli_fetch_object($sql3);
?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>
                <?php if (!empty($Row->photos)) { ?>
                    <a href="./uploads/mystery/<?php echo $Row->photos; ?>" target="_blank">
                        <img src="./uploads/iconic/<?php echo $Row->photos; ?>" class="header-profile-user" width="60" alt="">
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $Row->title; ?></td>
            <td><?php echo $res2->god_name; ?></td>
        
           
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="7">
            <div align="center"><strong>No Records!</strong></div>
        </td>
    </tr>
<?php } ?>

<!-- Abroad Section -->
<?php
$select = "SELECT * FROM `abroad` WHERE my_stery = 1 ORDER BY index_id DESC";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
$num_rows = mysqli_num_rows($SQL_STATEMENT);

if ($num_rows != 0) {
   
    while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
        $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT god_name FROM god WHERE index_id='" . $Row->god_id . "'");
        $res3 = mysqli_fetch_object($sql3);
?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>
                <?php if (!empty($Row->photos)) { ?>
                    <a href="./uploads/mystery/<?php echo $Row->photos; ?>" target="_blank">
                        <img src="./uploads/abroad/<?php echo $Row->photos; ?>" class="header-profile-user" width="60" alt="">
                    </a>
                <?php } ?>
            </td>
            <td><?php echo $Row->title; ?></td>
            <td><?php echo $res3->god_name; ?></td>
           
         
   
        </tr>
    <?php }
} else { ?>
    <tr>
        <td colspan="7">
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
                    <h5 class="header-title">Delete Mystery Temple</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <h5 class="text-center">Delete Mystery Temple Details?</h5>
                    <p>Are you sure you want to delete this Mystery Temple? All data will be lost.</p>
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
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form = $('#add_form')[0];
        var data = new FormData(form);
        //alert(data);
        $.ajax({
            type: "POST",
            url: "packages-process.php",
            data: data,
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