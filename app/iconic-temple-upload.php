<?php ob_start();
include_once './includes/header.php';
if(isset($_REQUEST['upload'])){
    $log_date = date("Y-m-d");
    $created_staff=$_SESSION["staff_id"];
    $filename=$_FILES["upload_file"]["tmp_name"];	    
    $ext = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);
	if(strtolower($ext) == "csv")
	{
	  	$file = fopen($filename, "r");
	  	$i=0;$j=0;
        while (($getData = fgetcsv($file)) !== FALSE) {
	        if($i>0 && $getData[3] != ''){
           		$DatabaseCo->dbLink->query("INSERT INTO `iconic_temples` (`banner`, `my_stery`, `categories_id`, `god_id`, `title`, `sthalam`, `puranam`, `photos`, `varnam`, `highlights`, `sevas`, `time`, `gallery_image`, `country`, `state`, `city`, `address`, `order_by`, `speciality`, `log_date`, `status`) VALUES ('$getData[0]','$getData[1]','$getData[2]','$getData[3]','$getData[4]','$getData[5]','$getData[6]','$getData[7]','$getData[8]','$getData[9]','$getData[10]','$getData[11]','$getData[12]','$getData[13]','$getData[14]','$getData[15]','$getData[16]','$getData[17]','$getData[18]','$log_date','unapproved')");$j++;
	       	}$i++;
	   	}
	}
	header("location:iconic-temple-upload.php?count=$j");
}
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
<div class="body-content">
  <div class="decoration blur-2"></div>
  <div class="decoration blur-3"></div>
  <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Iconic Temples  - Bulk Upload</div>
  <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bolder">Upload multiple temples details</h2>
            </div>
            <?php if(isset($_REQUEST['count']) && $_REQUEST['count']>0){?>
            <div class="bg-success text-white m-5 p-3"><?php echo $_REQUEST['count'];?> number of temples have been imported!</div>
            <?php } if(isset($_REQUEST['count']) && $_REQUEST['count']==0){?>
            <div class="bg-danger text-white m-5 p-3">No details have been imported. Follow the instructions provided and upload again!</div>
            <?php }?>
        <div class="text-end">
            <a href="iconic-temples.csv" class="btn btn-primary fw-medium mr-2 pr-2 mx-1"  download="iconic-temples.csv" type="text/csv"><i class="fa-solid fa-download me-1"></i>Download Sample File</a>
        </div>
            <div class="card-body align-items-center" align="left">
										<h6><strong>Instructions:-</strong></h6>
										<ul>
										  <li>Fill all the fields provided</li>
										  <li>Is mystery temple should be set as value 1 if the given temple details should be listed under mystery as well.</li>
										  <li>Get the Iconic Category ID from the <a href="temple-iconic-category-listing.php" target="_blank">Iconic Category Master</a></li>
										  <li>Get the God ID from the <a href="god.php" target="_blank">God Master</a></li>
										  <li>Get the Country Code from <a href="city.php" target="_blank">City Master</a></li>
										  <li>Banner and Thumbnail image name should be added to the appropriate field and maintain unique name to avoid overriding</li>
										</ul>
										<div class="d-flex position-relative my-1" align="center">	
											<form action="iconic-temple-upload.php" method="post"  enctype="multipart/form-data">
												
										<div class="row">
											<div class="col-sm-6 mb-3"><strong>Upload CSV File</strong></div>
											<div class="col-sm-6 mb-3">	
											<input type="file" name="upload_file" class="form-control form-control-solid" placeholder="" id="upload_file" required="">
											</div>
										</div>	
										<div class="row">
											<div class="col-sm-12">
														<input type="submit" name="upload" class="btn btn-primary" value="Upload File">
													</div>
												</div>
										</div>
														</form>
														<div style="height:120px"></div>
													</div>
        </div>
    </div>
</div>

<?php
include_once './includes/footer.php';
?>

<script type="text/javascript">
   
$("#bulk_upload_form").submit(function(event){
	event.preventDefault(); 
	var post_url = $(this).attr("action"); 
	var request_method = $(this).attr("method");
	var form_data = $("#bulk_upload_form").serialize();
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