<?php ob_start();
include_once './includes/header.php';
error_reporting(0);

if (isset($_POST['delete_now']) && !empty($_POST['del_c'])) {
    $del_id = intval($_POST['del_c']); // Sanitize input to avoid SQL injection

    $query = "DELETE FROM `category` WHERE `index_id` = ?";
    $stmt = $DatabaseCo->dbLink->prepare($query);
    $stmt->bind_param("i", $del_id);

    if ($stmt->execute()) {
        echo "<script>
              
                window.location.href = 'category.php';
              </script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $DatabaseCo->dbLink->close();
}
// Check if delete ID is available in the request

?>





<!-- Page-Title -->
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1">Categories Page List</h6>
            <!-- <p class="mb-0">Countrys Listing.</p> -->

        </div>
     
        <div class="text-end">
            <a href="category_add.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Category Page</a>
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
                        
                        

                            <th class="w-50">Category Page Name</th>
                            <!-- <th class="w-20">tag Name</th> -->
                            <th class="w-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $select = "SELECT * FROM `category` WHERE index_id !='0' ORDER BY index_id  DESC";
                        $SQL_categoryMENT = mysqli_query($DatabaseCo->dbLink, $select);
                        $num_rows = mysqli_num_rows($SQL_categoryMENT);
                        if ($num_rows != 0) {
                            $i = 1;
                            while ($Row = mysqli_fetch_object($SQL_categoryMENT)) {
                        ?>
                                <tr>
                                    <td><?php echo $i;
                                        $i++; ?></td>

                                    <td><?php echo $Row->name; ?></td>
                                    <!-- <td><?php echo $Row->tag_name; ?></td> -->
                                   

                                    <td>
                                        <a class="btn btn-sm p-2 btn-primary text-white  edit-board alert-box-trigger waves-effect waves-light kill-drop" href="category_add.php?index_id=<?php echo $Row->index_id; ?>"><i class="fas fa-pencil-alt"></i></a> &nbsp; &nbsp;
                                        <a class="btn btn-sm p-2 btn-danger delete-board waves-effect waves-light text-white"
                                            data-toggle="modal"
                                            data-target="#delete-board-alert"
                                            data-id="<?php echo $Row->index_id; ?>"
                                            id="delete-board<?php echo $Row->index_id; ?>">
                                            <i class="fa fa-trash text-white"></i>
                                        </a>

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
    <form method="POST" id="delete_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Delete category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h5 class="text-center">Delete category Details?</h5>
                    <p>Are you sure you want to delete this category? All related data will be lost.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="del_c" id="delid" value="" />
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button name="delete_now" type="submit" class="btn btn-danger">Delete</button>
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
    $(document).on('click', '.delete-board', function() {
        var countryId = $(this).data('id'); // Get the data-id from the button
        $('#delid').val(countryId); // Assign it to the hidden input field
    });
</script>