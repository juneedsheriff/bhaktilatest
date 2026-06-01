<?php 
include_once './includes/header.php';

if($_GET['id']) {
    $id=base64_decode($_GET['id']);
}
$select = "SELECT * FROM staff WHERE index_id='$id'";//echo $select;
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink,$select);
$Row =mysqli_fetch_object($SQL_STATEMENT);

// $Pselect = "SELECT * FROM staff_privilege WHERE staff_id='$id'";//echo $select;
// $PSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink,$Pselect);
// $PRow =mysqli_fetch_object($PSQL_STATEMENT);

$photos = ($Row->photos !='')?'./uploads/staff/'.$Row->photos:'./assets/images/nophoto.png';

//ID 
if($Row->index_id<=9){$staffID='00'.$Row->index_id;}
elseif($Row->index_id>9 && $Row->index_id<=99){$staffID='0'.$Row->index_id;}
else{$staffID=$Row->index_id;}
?>                    
                    <!-- Page-Title -->
                    <!-- <div class="page-title-box">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="page-title mb-1">Staff</h4>
                                    <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item active">Staff Details</li>
                                    </ol>
                                </div>
                                <div class="col-md-4" align="right">
                                    <a class="btn btn btn-primary fw-medium waves-light" data-ripple-color="" href="stafflist.php">
                                    <i class="mdi mdi-account-group mr-1"></i>Staff List
                                </a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="card-header position-relative " >
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1">Staff Details</h6>
            <!-- <p class="mb-0">Countrys Listing.</p> -->

        </div>
     
        <div class="text-end">
            <a href="stafflist.php" class="btn btn-primary fw-medium">Staff List</a>
        </div>
    </div>
</div>
                    <!-- end page title end breadcrumb -->

                     
                                        <div class="row">
                                            <!-- Avatar Card -->
                                            <div class="col-sm-4">
                                                <div class="card card-body pb-5" align="center">
                                                    <div class="inner">
                                                        <div class="contact-block">
                                                            <!-- Avatar -->
                                                            <div class="avatar-wrapper w-100">
                                                                <img class="rounded-circle header-profile-user avatar-xl" src="<?php echo $photos;?>">
                                                            </div>
                                                            <!-- Meta -->
                                                            <h4 class="card-title font-size-16 mt-0"><?php echo $Row->name; ?></h4>
                                                            <div class="contact-company mb-3">BKPID<?php echo $staffID; ?></div>
                                                            <div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="card">
                                        <div class="card-body">
                                            <h4 class="header-title"><?php echo $Row->name; ?></h4>
                                            <p class="card-title-desc">Staff Profile</p>
            
                                            <!-- Nav tabs -->
                                            <ul class="nav nav-pills" role="tablist">
                                                <li class="nav-item waves-effect waves-light">
                                                    <a class="nav-link active" data-toggle="tab" href="#home-1" role="tab">
                                                        <i class="fas fa-user mr-1"></i> <span class="d-none d-md-inline-block">Profile</span> 
                                                    </a>
                                                </li>
                                                <!-- <li class="nav-item waves-effect waves-light">
                                                    <a class="nav-link" data-toggle="tab" href="#profile-1" role="tab">
                                                        <i class="fas fa-cog mr-1"></i> <span class="d-none d-md-inline-block">Permission</span>
                                                    </a>
                                                </li> -->
                                            </ul>
            
                                            <!-- Tab panes -->
                                            <div class="tab-content p-3">
                                                <div class="tab-pane active" id="home-1" role="tabpanel">
                                                    <div class="row">
                                                                <div class="col-sm-6">
                                                                    <!-- Info block -->
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Name
                                                                        </div>
                                                                        <div class="info-content mb-3 is-email">
                                                                            <?php echo $Row->name; ?>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Info block -->
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Email
                                                                        </div>
                                                                        <div class="info-content mb-3">
                                                                            <?php echo $Row->username; ?>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Info block -->
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Contact
                                                                        </div>
                                                                        <div class="info-content mb-3">
                                                                            <?php echo $Row->mobile; ?>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <!-- Contact summary -->
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Designation
                                                                        </div>
                                                                        <div class="info-content mb-3">
                                                                            <?php echo $Row->designation;?></div>
                                                                       
                                                                    </div>
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Password
                                                                        </div>
                                                                        <div class="info-content mb-3">
                                                                            <?php echo base64_decode($Row->password); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info-block">
                                                                        <div class="header-title">
                                                                            Address
                                                                        </div>
                                                                        <div class="info-content mb-3">
                                                                            <?php echo $Row->address; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                </div>
                                                <div class="tab-pane" id="profile-1" role="tabpanel">
                                                    <form action="staff.php" method="post" name="privilege_form" id="privilege_form">
                                                             <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">View</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Add</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Update</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Delete</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Approval</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Reject</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php /*<div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Driver</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_view" value="1" <?php echo ($PRow->driver_view=='1')?'checked':'';?>></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_add" value="1" <?php echo ($PRow->driver_add=='1')?'checked':'';?>></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_edit" value="1" <?php echo ($PRow->driver_edit=='1')?'checked':'';?>></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_delete" value="1" <?php echo ($PRow->driver_delete=='1')?'checked':'';?>></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_approval" <?php echo ($PRow->driver_approval=='1')?'checked':'';?> value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="driver_reject" <?php echo ($PRow->driver_reject=='1')?'checked':'';?> value="1"></div>
                                                                    </div>
                                                                </div>
                                                            </div>*/?>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Owners</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_view" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_add" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_edit" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_delete" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_approval" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="agency_reject" value="1"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Vehicle</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_view" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_add" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_edit" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_delete" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_approval" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="vehicle_reject" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Wallet</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="wallet_update" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="wallet_approval" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Packages</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="packages_view" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="packages_update" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="packages_delete" value="1"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Affiliates</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="affiliates_view" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="affiliates_add" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="affiliates_edit" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="affiliates_delete" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="affiliates_approval" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">&nbsp;-</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row mt-2">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Trip Booking</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="trips_live" value="1"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-2 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title">Trip History</div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-1 mb-2">
                                                                    <div class="info-block">
                                                                        <div class="header-title"><input type="checkbox" name="trips_history" value="1" ></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <div class="text-center" align="center">
                                                                        <input type="hidden" name="staff_id" value="<?php echo $id;?>">
                                                                        <input type="hidden" name="form_action" value="privilege_save">
                                                                        <button type="submit" name="update_privilege" id="update_privilege" class="btn btn-secondary">SAVE</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                </div>
                                            </div>
            
                                        </div>
                                    </div>

</div></div>



<?php 
include_once 'includes/footer.php';
?>
<script type="text/javascript">

$('#newpassword, #newpassword1').on('keyup', function () {
if ($('#newpassword').val() == $('#newpassword1').val()) {
$("#newpassword1").css("border-color", "green");
return true;
} else 
$("#newpassword1").css("border-color", "red");
return false;
});

$("#privilege_form").submit(function(event){
    event.preventDefault(); 
    var post_url = $(this).attr("action"); 
    var request_method = $(this).attr("method");
    var form_data = $("#privilege_form").serialize();
    //alert(form_data);
    $.ajax({
        url : post_url,
        type: request_method,
        dataType:"text",
        data : form_data
    }).done(function(response){ 
       window.location.reload();
    });
});

</script>  