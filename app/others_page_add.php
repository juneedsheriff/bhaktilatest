<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);
$page_id = $_REQUEST['page_id'];
if (isset($_REQUEST['submit'])) {
    $title = $xssClean->clean_input($_REQUEST['title']);

    $content = $xssClean->clean_input($_REQUEST['content']);
    $order_by = $xssClean->clean_input($_REQUEST['order_by']);
    $page_id = $xssClean->clean_input($_REQUEST['page_id']);
    $status = $xssClean->clean_input($_REQUEST['status']);
    if( $user_role === 'Staff'){
        $status_1='unapproved';
      }elseif($user_role === "Admin"){
        $status_1=$status;
      }else{
      
      }




    if ($_REQUEST['id'] > 0) {
        $d_id = $_REQUEST['id'];
        $DatabaseCo->dbLink->query("UPDATE `other_page` SET `title`='$title',`content`='$content',`page_id`='$page_id',`order_by`='$order_by',`status`='$status_1' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
    } else {
        $DatabaseCo->dbLink->query("INSERT INTO `other_page`( `title`, `content`,`page_id`,`order_by`,`status`) VALUES ( '$title','$content','$page_id','$order_by','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));
        $d_id = mysqli_insert_id($DatabaseCo->dbLink);
    }

    // Not sure what this is intended for; you can remove if not needed.
    $uploadimage = new ImageUploader($DatabaseCo);
    $upload_image_photos = '';
    $upload_image_banner = '';

    // Check if the photos file is uploaded
    if (is_uploaded_file($_FILES['photos']["tmp_name"])) {
        $upload_image_photos = $uploadimage->upload($_FILES['photos'], "others");
    }

    // Check if the banner file is uploaded
    if (is_uploaded_file($_FILES['banner']["tmp_name"])) {
        $upload_image_banner = $uploadimage->upload($_FILES['banner'], "others/banner");
    }

    // Only update the database if there is a new image for photos
    if ($upload_image_photos != '') {
        $DatabaseCo->dbLink->query("UPDATE `other_page` SET photos='$upload_image_photos' WHERE index_id='$d_id'");
    }

    // Only update the database if there is a new image for banner
    if ($upload_image_banner != '') {
        $DatabaseCo->dbLink->query("UPDATE `other_page` SET banner='$upload_image_banner' WHERE index_id='$d_id'");
    }

    if ($_REQUEST['edit'] > 0) {
        header("location:others_page.php?alt=1?page_id=$page_id");
    } else {
        header("location:other_page_add.php");
    }

    header("location:other_page.php?page_id=$page_id");
}








if ($_REQUEST['id'] > 0) {
    // $titl = "Update ";
    $select = "SELECT * FROM other_page WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    $titl = "";
}
?>
<div class="body-content">
    <div class="decoration blur-2"></div>
    <div class="decoration blur-3"></div>

    <?php
     $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT name FROM  category WHERE index_id='$page_id'");
     $res3 = mysqli_fetch_object($sql3);
    ?>
    <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2"><?php echo $res3->name; ?> </div>
    <div class="row">

        <div class="card mb-4">
            <div class="card-header position-relative">
                <h6 class="fs-17 fw-semi-bold mb-0">Basic Informations</h6>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($_REQUEST['img']) {
                                echo ' <p class="card-title-desc">Unable to upload photo as the image size is >2mb.</p>';
                            } ?>
                            <p class="card-title-desc">Please fill the required category temples details.</p>
                            <form action="" method="post" name="finish-form" enctype="multipart/form-data" class="needs-validation" novalidate="">
                                <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
                                    <div class="row is-multiline">
                                        <div class="col-sm-4 mb-3">
                                            <label>Title</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <input type="text" class="form-control" name="title" id="title" required="" placeholder="Enter Name" value="<?php echo $Row->title; ?>">
                                                </div>
                                            </div>
                                        </div>





                                        <div class="col-sm-4 mb-3">
                                            <label>Featured Images</label>

                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
                                                    <!-- <label class="pull-left">image </label> -->
                                                    <div class="custom-file">
                                                        <input class="fileUp fileup-sm uploadlink" type="file" name="photos" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                                                echo 'required=""';
                                                                                                                                                                                                            } ?>>
                                                         <label class="custom-file-label" for="photos" style="font-size: 13px;">Recommended to 250 x 250 px (png, jpg, jpeg).</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Banner Image </label>

                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
                                                    <!-- <label class="pull-left">image </label> -->
                                                    <div class="custom-file">
                                                        <input class="fileUp fileup-sm uploadlink" type="file" name="banner" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                                                echo 'required=""';
                                                                                                                                                                                                            } ?>>
                                                        <label class="custom-file-label" for="photos"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Order Number</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <input type="number" class="form-control" name="order_by" id="order_by" required="" placeholder="Enter Order Number" value="<?php echo $Row->order_by; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($user_role === 'Admin'): ?>
                    <div class="col-sm-4 mb-3">
                      <!-- start form group -->

                      <label class="required fw-medium ">Status</label>

                      <select class="form-select " name="status" id="">
                        <option selected disabled>Select Status</option>
                        <option value="approved" <?php echo $Row->status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="unapproved" <?php echo $Row->status === 'unapproved' ? 'selected' : ''; ?>>Unapproved</option>
                      </select>

                      <!-- end /. form group -->
                    </div>
                    <?php endif; ?>
                                        <div class="col-sm-12 mb-3">
                                            <label>Content</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <textarea class="form-control" id="editor2" data-sample-short name="content" rows="5" placeholder="Enter content"><?php echo $Row->content; ?></textarea>
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
                                                    <input type="hidden" name="page_id" value="<?php echo $page_id  ?>">
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
<script>
    CKEDITOR.replace('editor1');
    console.log(CKEDITOR.version);
</script>

<script>
    CKEDITOR.replace('editor2');
</script>
<script>
    CKEDITOR.replace('editor3');
</script>