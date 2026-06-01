<?php
include_once './includes/header.php';
error_reporting(1);
if ($_REQUEST['form_action'] == "Delete") {
    $id = $xssClean->clean_input($_REQUEST['delid']);

    $DatabaseCo->dbLink->query("DELETE FROM `staff` WHERE `staff`.`index_id` = '$id'") or die(mysqli_error($DatabaseCo->dbLink));
}
?>
<!-- Page-Title -->

<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1">All Staffs</h6>
            <!-- <p class="mb-0">Temples Listing.</p> -->

        </div>
        <div class="text-end">
            <a class="btn btn-primary fw-medium waves-light" data-ripple-color="" href="#0" data-modal="create-contact-modal" data-toggle="modal" data-target="#create-contact-modal">
                <i class="fa-solid fa-plus me-1"></i>Add New Staff
            </a>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="page-content-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <p class="card-title-desc">Listing of All Staffs
                        </p>

                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="w-15">Sno</th>
                                    <th class="w-20">Photo</th>
                                    <th class="w-20">Name</th>
                                    <th class="w-30">Contact</th>
                                    <th class="w-30">Password</th>
                                    <!--<th>Owners</th>-->
                                    <!--<th>Vehicles</th>-->
                                    <th>Designation</th>
                                    <th class="w-5">Action</th>
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody>
                                <!-- Table row -->
                                <?php $select = "SELECT * FROM staff ORDER BY index_id";
                                $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
                                $num_rows = mysqli_num_rows($SQL_STATEMENT);
                                if ($num_rows != 0) {
                                    $i = 1;
                                    while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
                                        if ($Row->index_id <= 9) {
                                            $staffID = '00' . $Row->index_id;
                                        } elseif ($Row->index_id > 9 && $Row->index_id <= 99) {
                                            $staffID = '0' . $Row->index_id;
                                        } else {
                                            $staffID = $Row->index_id;
                                        }
                                        $photos = ($Row->photos != '') ? './uploads/staff/' . $Row->photos : './assets/images/nophoto.png';
                                ?>
                                        <tr>
                                            <td>
                                                <span class="inner"> <?php echo $i; ?> </span>
                                            </td>
                                            <td>
                                                <span class="inner">
                                                    <div class="avatar-wrapper">
                                                        <a href="staffview.php?id=<?php echo base64_encode($Row->index_id); ?>"><img src="<?php echo $photos; ?>" alt="" class="rounded-circle header-profile-user avatar-lg" data-demo-src="<?php echo $photos; ?>"></a>
                                                    </div>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="inner"><a href="staffview.php?id=<?php echo base64_encode($Row->index_id); ?>"><strong> <?php echo 'BKPID' . $staffID . '<br>' . $Row->name; ?> </strong></a></span>
                                            </td>
                                            <td>
                                                <span class="date"><?php echo $Row->mobile . '<br>' . $Row->username; ?></span>
                                            </td>
                                            <td>
                                                <span class="date"><?php echo base64_decode($Row->password); ?></span>
                                            </td>
                                            <td>
                                                <span class="date"><?php echo $Row->designation; ?></span>
                                            </td>
                                            <!--<td>-->
                                            <!--    <span class="date"><?php echo $Row->username; ?></span> -->
                                            <!--</td>-->
                                            <td>
                                                <div class="">
                                                    <div class="dropdown">
                                                        <button class="btn btn-light btn-rounded dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="mdi mdi-tune"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-animated" style="">
                                                            <a class="edit-board dropdown-item" href="#0" data-modal="create-contact-modal" data-toggle="modal" data-target="#create-contact-modal" data-id="<?php echo $Row->index_id; ?>" data-name="<?php echo $Row->name; ?>" data-mobile="<?php echo $Row->mobile; ?>" data-address="<?php echo $Row->address; ?>" data-username="<?php echo $Row->username; ?>" data-designation="<?php echo $Row->designation; ?>" data-password="<?php echo base64_decode($Row->password); ?>"><i class="fas fa-pencil-alt"></i> Edit</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item delete-board alert-box-trigger" data-modal="delete-board-alert" data-toggle="modal" data-target="#delete-board-alert" href="#0" data-id="<?php echo $Row->index_id; ?>" id="delete-board<?php echo $Row->index_id; ?>">
                                                                <i class="fas fa-trash-alt"></i> Delete</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="staffview.php?id=<?php echo base64_encode($Row->index_id); ?>"><i class="fas fa-eye"></i> View Profile</a>
                                                            <div class="dropdown-divider"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php $i++;
                                    }
                                } else { ?>
                                    <tr>
                                        <td colspan="12">
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
    </div>
</div>

<div id="create-contact-modal" class="modal fade alert-box">
    <form action="staff.php" method="post" name="create_staff_form" id="create_staff_form" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Staff Details</h5>
                    <button type="btn" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="body-inner">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Name</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="text" name="name" id="name" placeholder="Name" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Contact Number</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" name="mobile" id="mobile" type="text" placeholder="Contact" pattern="[0-9]{10}" maxlength="10" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Address</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="text" name="address" id="address" placeholder="Address" required="">

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Email</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="email" name="username" id="username" autocomplete="off" placeholder="Email" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Photo</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="file" name="photos" id="photos" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="field">
                                    <label class="pull-left">Designation</label>
                                    <div class="control has-icons-left">
                                        <select name="designation" id="designation" required="" class="form-control">
                                            <option value="">Select Designation</option>
                                            <option value="Director">Director</option>
                                            <option value="Branch Manager">Branch Manager</option>
                                            <option value="Accountant">Accountant</option>
                                            <option value="Customer Support Executive">Customer Support Executive</option>
                                            <option value="Support Team">Support Team</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3" id="pass1">
                                <div class="field">
                                    <label class="pull-left">Password</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="text" name="password" id="password" placeholder="Password">
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6 mb-3" id="pass2">
                                <div class="field">
                                    <label class="pull-left">Confirm Password</label>
                                    <div class="control has-icons-left">
                                        <input class="form-control" type="text" name="cpassword" id="cpassword" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <!-- <div class="is-prospect">
                <div class="field">
                    <input id="prospectSwitch" type="checkbox" name="prospectSwitch" class="switch is-secondary">
                    <label for="prospectSwitch"><span>Informations verified</span></label>
                </div>
            </div> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="form_action" value="Add" />
                    <input type="hidden" name="get_id" id="get_id" />
                    <button type="submit" name="create_staff" id="create_staff" class="btn btn-primary ">Save Details</button>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- /Invite User Modal -->
<div id="delete-board-alert" class="modal fade alert-box">
    <form action="stafflist.php" method="post" name="delete_form" id="delete_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Delete Staff?</h5>
                    <button type="btn" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" align="center">
                    <h5 class="text-center">Delete Staff Details ?</h5>
                    <p>Are you sure you want to delete this Staff? All data and attached deals will be lost.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="form_action" value="Delete" />
                    <input type="hidden" name="delid" id="delid" value="" />
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
<script>
    $("#create_staff_form").submit(function(event) {
        event.preventDefault();
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var formData = $("#create_staff_form").submit(function(event) {
            return;
        });
        var formData = new FormData(formData[0]);
        $.ajax({
            url: post_url,
            type: request_method,
            data: formData,
            success: function(response) {
                console.log("Staff was created successfully");
                window.location.reload();
            },
            contentType: false,
            processData: false,
            cache: false
        });
        return false;
    });
    $('.edit-board').click(function() {
        $("#pass2").hide();
        var id = $(this).data('id');
        var name = $(this).data('name');
        var mobile = $(this).data('mobile');
        var address = $(this).data('address');
        var username = $(this).data('username');
        var designation = $(this).data('designation');
        var password = $(this).data('password');
        var percent = $(this).data('percent');
        var amount = $(this).data('amount');
        $("#get_id").val(id);
        $("#name").val(name);
        $("#mobile").val(mobile);
        $("#address").val(address);
        $("#username").val(username);
        $("#designation").val(designation);
        $("#password").val(password);
        $("#percent").val(percent);
        $("#discamount").val(amount);
    });
    $('.delete-board').click(function() {
        var id = $(this).data('id');
        $("#delid").val(id);
    });
</script>