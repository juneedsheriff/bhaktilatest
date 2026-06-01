<?php ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once './includes/header.php';

// if (isset($_GET['del_t']) && is_numeric($_GET['del_t'])) {
//     $del_id = intval($_GET['del_t']); // Sanitize the input

//     // Prepare statement
//     $stmt = $DatabaseCo->dbLink->prepare("DELETE FROM private_ads WHERE id = ?");
//     $stmt->bind_param("i", $del_id);

//     if ($stmt->execute()) {
//         header('Location: private-ads.php');
//         exit;
//     } else {
//         die("Error: " . $stmt->error);
//     }

//     $stmt->close();
// } else {
//     die("Invalid Request.");
// }

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

            <h6 class="fs-17 fw-semi-bold my-1"> Priests </h6>

            <!-- <p class="mb-0">Temple Listing.</p> -->



        </div>

        <div class="text-end">

            <a href="add-priest.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New Priest</a>

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
            <th class="w-15">Photo</th>
            <th class="w-20">Full Name</th>
            <th class="w-10">Experience</th>
            <th class="w-15">Specialization</th>
            <th class="w-10">Email</th>
            <th class="w-10">Phone</th>
            <th class="w-10">Available?</th>
            <th class="w-5">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $select = "SELECT * FROM `priests` ORDER BY id DESC";
        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
        $num_rows = mysqli_num_rows($SQL_STATEMENT);

        if ($num_rows != 0) {
            $i = 1;
            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
        ?>
                <tr>
                    <td><?= $i++; ?></td>

                    <td>
                        <?php if (!empty($Row->photo)) { ?>
                            <a href="<?= $Row->photo; ?>" target="_blank">
                                <img src="<?= $Row->photo; ?>" class="header-profile-user" width="60" alt="Photo">
                            </a>
                        <?php } else { echo 'No Image'; } ?>
                    </td>

                    <td><?= htmlspecialchars($Row->full_name); ?></td>
                    <td><?= (int)$Row->experience_years; ?> yrs</td>
                    <td><?= htmlspecialchars($Row->specialization); ?></td>
                    <td><?= htmlspecialchars($Row->email); ?></td>
                    <td><?= htmlspecialchars($Row->contact_number); ?></td>
                    <td>
                        <?= $Row->available ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>'; ?>
                    </td>

                    <td>
                        <a class="btn btn-sm btn-primary" href="edit-priest.php?id=<?= $Row->id; ?>">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a class="btn btn-sm btn-danger"
                        href="#"
                        data-toggle="modal"
                        data-target="#delete-modal"
                        data-id="<?= $Row->id; ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr>
                <td colspan="9">
                    <div align="center"><strong>No Records Found!</strong></div>
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

                    <h5 class="header-title">Delete Mantras Stotras Temple</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body" align="center">

                    <h5 class="text-center">Delete Mantras Stotras Temple Details?</h5>

                    <p>Are you sure you want to delete this Mantras Stotras Temple? All data will be lost.</p>

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