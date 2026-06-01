<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';

// Only Admin can add/update staff
if (isset($_REQUEST['form_action']) && $_REQUEST['form_action'] == 'Add' && empty($_SESSION['admin_id'])) {
    $base = rtrim(dirname($_SERVER['PHP_SELF'] ?? ''), '/');
    header('Location: ' . ($base ? $base . '/' : '') . 'dashboard.php');
    exit;
}

if($_REQUEST['form_action'] == "Add")
{
	$id= $xssClean->clean_input($_REQUEST['get_id']);	
	$name= $xssClean->clean_input($_REQUEST['name']);
	$mobile=$xssClean->clean_input($_REQUEST['mobile']);
	$address=  $xssClean->clean_input($_REQUEST['address']);
	$username= trim($xssClean->clean_input($_REQUEST['username']));
	$designation=$xssClean->clean_input($_REQUEST['designation']);
	$password=base64_encode($_REQUEST['password']);
	$allowed_menus = '';
	if (!empty($_POST['allowed_menus']) && is_array($_POST['allowed_menus'])) {
		$allowed_menus = implode(',', array_map(function($v) use ($DatabaseCo) {
			return $DatabaseCo->dbLink->real_escape_string(trim($v));
		}, $_POST['allowed_menus']));
	}

	// Ensure allowed_menus column exists (for role-based menu)
	$chk = @$DatabaseCo->dbLink->query("SHOW COLUMNS FROM `staff` LIKE 'allowed_menus'");
	if (!$chk || mysqli_num_rows($chk) == 0) {
		@$DatabaseCo->dbLink->query("ALTER TABLE `staff` ADD COLUMN `allowed_menus` TEXT NULL DEFAULT NULL AFTER `designation`");
	}

	// Main INSERT/UPDATE
	if($id>0){
		$allowed_esc = $DatabaseCo->dbLink->real_escape_string($allowed_menus);
		$DatabaseCo->dbLink->query("UPDATE `staff` SET `name` = '$name', `mobile` = '$mobile', `address` = '$address', `designation` = '$designation', `password` = '$password', `allowed_menus` = '$allowed_esc' WHERE `index_id` = '$id'") or die(mysqli_error($DatabaseCo->dbLink));
	} else {
		$allowed_esc = $DatabaseCo->dbLink->real_escape_string($allowed_menus);
		$DatabaseCo->dbLink->query("INSERT INTO `staff` (`name`,`mobile`,`address`,`username`,`password`,`designation`,`allowed_menus`) VALUES ('$name','$mobile','$address','$username','$password','$designation','$allowed_esc')") or die(mysqli_error($DatabaseCo->dbLink));
    	$id=mysqli_insert_id($DatabaseCo->dbLink);
    }

	$uploadimage = new ImageUploader($DatabaseCo);
    $photos = is_uploaded_file($_FILES['photos']["tmp_name"])?$uploadimage->upload($_FILES['photos'],"staff"):'';
    if($photos!=''){
		$DatabaseCo->dbLink->query("UPDATE `staff` SET photos='$photos' WHERE index_id='$id'");
	}		
}

if($_REQUEST['action']=='Update')
{
	$id= $xssClean->clean_input($_REQUEST['staff_id']);
    $newpass=trim($_REQUEST['newpassword']);
    $newpass=base64_encode($newpass);    

    $sql="update staff set `password`='$newpass' where `index_id`='$id'";
    $go=mysqli_query($DatabaseCo->dbLink,$sql);
    echo "Password is updated successfully.";
}

if($_REQUEST['form_action']=='privilege_save')
{
	$id=$xssClean->clean_input($_REQUEST['staff_id']);
	$DatabaseCo->dbLink->query("UPDATE `staff_privilege` SET `driver_view`='".$_REQUEST['driver_view']."',`driver_add`='".$_REQUEST['driver_add']."',`driver_edit`='".$_REQUEST['driver_edit']."',`driver_delete`='".$_REQUEST['driver_delete']."',`driver_approval`='".$_REQUEST['driver_approval']."',`driver_reject`='".$_REQUEST['driver_reject']."',`agency_view`='".$_REQUEST['agency_view']."',`agency_add`='".$_REQUEST['agency_add']."',`agency_edit`='".$_REQUEST['agency_edit']."',`agency_delete`='".$_REQUEST['agency_delete']."',`agency_approval`='".$_REQUEST['agency_approval']."',`agency_reject`='".$_REQUEST['agency_reject']."',`vehicle_view`='".$_REQUEST['vehicle_view']."',`vehicle_add`='".$_REQUEST['vehicle_add']."',`vehicle_edit`='".$_REQUEST['vehicle_edit']."',`vehicle_delete`='".$_REQUEST['vehicle_delete']."',`vehicle_approval`='".$_REQUEST['vehicle_approval']."',`vehicle_reject`='".$_REQUEST['vehicle_reject']."',`wallet_update`='".$_REQUEST['wallet_update']."',`wallet_approval`='".$_REQUEST['wallet_approval']."',`affiliates_view`='".$_REQUEST['affiliates_view']."',`affiliates_add`='".$_REQUEST['affiliates_add']."',`affiliates_edit`='".$_REQUEST['affiliates_edit']."',`affiliates_delete`='".$_REQUEST['affiliates_delete']."',`affiliates_approval`='".$_REQUEST['affiliates_approval']."',`packages_view`='".$_REQUEST['packages_view']."',`packages_update`='".$_REQUEST['packages_update']."',`packages_delete`='".$_REQUEST['packages_delete']."',`trips_live`='".$_REQUEST['trips_live']."',`trips_history`='".$_REQUEST['trips_history']."' WHERE `staff_id`='$id'");
}
?>