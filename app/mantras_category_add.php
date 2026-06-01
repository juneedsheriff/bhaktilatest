<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);

if (isset($_REQUEST['submit'])) {
    $title = $xssClean->clean_input($_REQUEST['title']);
   






    if ($_REQUEST['id'] > 0) {
        $d_id = $_REQUEST['id'];
        $DatabaseCo->dbLink->query("UPDATE `mantras_category` SET `title`='$title' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
    } else {
        $DatabaseCo->dbLink->query("INSERT INTO `mantras_category`( `title`) VALUES ( '$title')") or die(mysqli_error($DatabaseCo->dbLink));
        $d_id = mysqli_insert_id($DatabaseCo->dbLink);
    }

 

    // Check if the photos file is uploaded
   

    if ($_REQUEST['edit'] > 0) {
        header("location:mantras_category.php?alt=1");
    } else {
        header("location:mantras_category_add.php");
    }

    header("location:mantras_category.php");
}








if ($_REQUEST['id'] > 0) {
    // $titl = "Update ";
    $select = "SELECT * FROM mantras_category WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    $titl = "";
}
?>
<div class="body-content">
    <div class="decoration blur-2"></div>
    <div class="decoration blur-3"></div>
    <div class="row">

        <div class="card mb-4">
            <div class="card-header position-relative">
                <h6 class="fs-17 fw-semi-bold mb-0">Mantras Category </h6>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($_REQUEST['img']) {
                                echo ' <p class="card-title-desc">Unable to upload photo as the image size is >2mb.</p>';
                            } ?>
                            <p class="card-title-desc">Please fill the required Mantras Category details.</p>
                            <form action="" method="post" name="finish-form" enctype="multipart/form-data" class="needs-validation" novalidate="">
                                <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
                                    <div class="row is-multiline">
                                        <div class="col-sm-6 mb-3">
                                            <label>Mantras Category Name</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <input type="text" class="form-control" name="title" id="title" required="" placeholder="Enter Name" value="<?php echo $Row->title; ?>">
                                                </div>
                                            </div>
                                        </div>
                                       
                                      
                                       





                                        <!-- <div class=" mb-4">
                      <div class="card-header position-relative">
                        <h6 class="fs-17 fw-semi-bold mb-0">Gallery</h6>
                      </div>
                      <div class="card-body">
                   
                        <div class="">
                          <label class="required fw-medium mb-2">Gallery</label>
                          <a href="#" class="bg-secondary uploadlink"></a>
                          <input class="fileUp fileup-sm uploadlink" type="file" name="gallery_image" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                        echo 'required=""';
                                                                                                                                                                                    } ?>>
                          <div class="form-text">Recommended to 350 x 350 px (png, jpg, jpeg).</div>
                        </div>
                      
                      </div>
                    </div> -->

                                        <!-- <div class="col-sm-3 mt-3 mb-3">
                <label></label>
                <div class="field">
                  <div class="control has-icons-left mt-30">
                    <a href="#" class="bg-secondary uploadlink"> <i class="fa fa-upload"></i> Upload Photo<input type="file" name="photos" id="photos" required="" class="upload " ></a><br>
                    <sub>maximum size 2mb only</sub>
                    <div class="mt-10" id="templesname"></div>
                  </div>
                </div>
              </div> -->


                                        <div class="col-sm-12">
                                            <div class="field" align="center">
                                                <div class="has-text-centered mt-30">
                                                    <input name="submit" type="submit" class="btn btn-primary" value="<?php echo $titl; ?> Submit" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>

</div>


<script src="https://cdn.ckeditor.com/4.25.0/standard/ckeditor.js"></script>


<?php
include_once './includes/footer.php';

?>
