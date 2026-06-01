<?php ob_start();
include_once './includes/header.php';
include_once './includes/abroad_import.php';

$importSummary = null;
if (isset($_REQUEST['upload'])) {
    $filename = $_FILES["upload_file"]["tmp_name"];
    $ext = pathinfo($_FILES["upload_file"]["name"], PATHINFO_EXTENSION);
    if (strtolower($ext) === "csv" && is_uploaded_file($filename)) {
        $importSummary = abroad_import_from_file($DatabaseCo->dbLink, $filename);
        $query = http_build_query([
            'count' => (int) ($importSummary['imported'] ?? 0),
            'skipped' => (int) ($importSummary['skipped'] ?? 0),
            'errors' => (int) ($importSummary['errors'] ?? 0),
            'format' => (string) ($importSummary['format'] ?? ''),
        ]);
        header("location:abroad-upload.php?$query");
        exit;
    }
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
  <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Temples in Abroad - Bulk Upload</div>
  <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bolder">Upload multiple temples details</h2>
            </div>
            <?php if (isset($_REQUEST['count']) && (int) $_REQUEST['count'] > 0) { ?>
            <div class="bg-success text-white m-5 p-3">
                <?php echo (int) $_REQUEST['count']; ?> temples imported
                <?php if (!empty($_REQUEST['format'])) { ?>(<?php echo htmlspecialchars($_REQUEST['format'], ENT_QUOTES, 'UTF-8'); ?> format)<?php } ?>.
                <?php if (!empty($_REQUEST['skipped'])) { ?> Skipped: <?php echo (int) $_REQUEST['skipped']; ?>.<?php } ?>
                <?php if (!empty($_REQUEST['errors'])) { ?> Errors: <?php echo (int) $_REQUEST['errors']; ?>.<?php } ?>
            </div>
            <?php } elseif (isset($_REQUEST['count']) && (int) $_REQUEST['count'] === 0) { ?>
            <div class="bg-danger text-white m-5 p-3">No details were imported. Follow the instructions and upload again.</div>
            <?php } ?>
        <div class="text-end">
            <a href="temple-abroad.csv" class="btn btn-primary fw-medium mr-2 pr-2 mx-1" download="temple-abroad.csv" type="text/csv"><i class="fa-solid fa-download me-1"></i>Download Sample File</a>
        </div>
            <div class="card-body align-items-center" align="left">
										<h6><strong>Instructions:-</strong></h6>
										<ul>
										  <li>Fill all the fields provided</li>
										  <li>Is mystery temple should be set as value 1 if the given temple details should be listed under mystery as well.</li>
										  <li>Get the God ID from the <a href="god.php" target="_blank">God Master</a></li>
										  <li>Get the Country Code from <a href="city.php" target="_blank">City Master</a></li>
										  <li>Supports both the sample template and legacy export CSV files (like Results.csv).</li>
										  <li>Legacy CSV rows are imported as approved when status is Approved in the file.</li>
										</ul>
										<div class="d-flex position-relative my-1" align="center">	
											<form action="abroad-upload.php" method="post"  enctype="multipart/form-data">
												
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