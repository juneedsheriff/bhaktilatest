<?php ob_start();
include_once './includes/header.php';
include_once './lib/mantrasListImport.php';
if (isset($_REQUEST['upload'])) {
    $filename = $_FILES["upload_file"]["tmp_name"];
    $ext = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);

    if (strtolower($ext) === "csv" && is_uploaded_file($filename)) {
        $result = importMantrasListFromCsv($DatabaseCo->dbLink, $filename);
        $j = (int) ($result['imported'] + $result['updated']);
        $skipped = (int) $result['skipped'];
        $errors = count($result['errors']);
        header("location:mantras-upload.php?count=$j&skipped=$skipped&errors=$errors");
        exit;
    }

    header("location:mantras-upload.php?count=0");
    exit;
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
  <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Mantras  - Bulk Upload</div>
  <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bolder">Upload multiple temples details</h2>
            </div>
            <?php if (isset($_REQUEST['count']) && (int) $_REQUEST['count'] > 0) { ?>
            <div class="bg-success text-white m-5 p-3"><?php echo (int) $_REQUEST['count']; ?> mantra records imported or updated.</div>
            <?php } elseif (isset($_REQUEST['count']) && (int) $_REQUEST['count'] === 0) { ?>
            <div class="bg-danger text-white m-5 p-3">No details have been imported. Follow the instructions provided and upload again!</div>
            <?php } ?>
            <?php if (!empty($_REQUEST['skipped'])) { ?>
            <div class="bg-warning text-dark m-5 p-3"><?php echo (int) $_REQUEST['skipped']; ?> rows were skipped.</div>
            <?php } ?>
            <?php if (!empty($_REQUEST['errors'])) { ?>
            <div class="bg-danger text-white m-5 p-3"><?php echo (int) $_REQUEST['errors']; ?> rows had errors. Check god names and CSV format.</div>
            <?php } ?>
        <div class="text-end">
            <a href="data/mantras_list.csv" class="btn btn-primary fw-medium mr-2 pr-2 mx-1" download="mantras_list.csv" type="text/csv"><i class="fa-solid fa-download me-1"></i>Download Sample File</a>
        </div>
            <div class="card-body align-items-center" align="left">
										<h6><strong>Instructions:-</strong></h6>
										<ul>
										  <li>CSV columns: id, title, content, status, date, description, godname, meaning, extra, mp3, mp4</li>
										  <li>God name must match an existing record in <a href="mantras_subcategory.php" target="_blank">God Subcategory Master</a></li>
										  <li>Status should be Approved or unapproved</li>
										  <li>Audio file name should exist in uploads/mantras_audio</li>
										</ul>
										<div class="d-flex position-relative my-1" align="center">	
											<form action="mantras-upload.php" method="post"  enctype="multipart/form-data">
												
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