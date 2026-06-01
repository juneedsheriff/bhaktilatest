<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);

if (isset($_REQUEST['submit'])) {
    $title = $xssClean->clean_input($_REQUEST['title']);
    $order_by = $xssClean->clean_input($_REQUEST['order_by']);
    $categories_id = $xssClean->clean_input($_REQUEST['categories_id']);

    $description = $xssClean->clean_input($_REQUEST['description']);

    $status = $xssClean->clean_input($_REQUEST['status']);
    if( $user_role === 'Staff'){
        $status_1='unapproved';
      }elseif($user_role === "Admin"){
        $status_1=$status;
      }else{
      
      }





    if ($_REQUEST['id'] > 0) {
        $d_id = $_REQUEST['id'];
        $DatabaseCo->dbLink->query("UPDATE `mantras_subcategory` SET `title`='$title',`description`='$description',`order_by`='$order_by',`categories_id`='$categories_id',`status`='$status_1' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
    } else {
        $DatabaseCo->dbLink->query("INSERT INTO `mantras_subcategory`( `title`, `description`,`order_by`,`categories_id`,`status`) VALUES ( '$title','$description','$order_by','$categories_id','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));
        $d_id = mysqli_insert_id($DatabaseCo->dbLink);
    }

    // Not sure what this is intended for; you can remove if not needed.
    // Not sure what this is intended for; you can remove if not needed.
    $uploadimage = new ImageUploader($DatabaseCo);
    $upload_image_photos = '';
    $upload_image_banner = '';

    // Check if the photos file is uploaded (retain existing if no new file)
    if (!empty($_FILES['photos']['tmp_name']) && is_uploaded_file($_FILES['photos']['tmp_name'])) {
        $upload_image_photos = $uploadimage->upload($_FILES['photos'], "gods");
    }

    // Check if the banner file is uploaded (retain existing if no new file)
    if (!empty($_FILES['banner']['tmp_name']) && is_uploaded_file($_FILES['banner']['tmp_name'])) {
        $upload_image_banner = $uploadimage->upload($_FILES['banner'], "gods/banner");
    }

    // Only update the database if there is a new image for photos
    if ($upload_image_photos != '') {
        $DatabaseCo->dbLink->query("UPDATE `mantras_subcategory` SET photos='$upload_image_photos' WHERE index_id='$d_id'");
    }

    // Only update the database if there is a new image for banner
    if ($upload_image_banner != '') {
        $DatabaseCo->dbLink->query("UPDATE `mantras_subcategory` SET banner='$upload_image_banner' WHERE index_id='$d_id'");
    }

    if ($_REQUEST['edit'] > 0) {
        header("location:mantras_subcategory.php?alt=1");
    } else {
        header("location:add_mantras_subcategory.php");
    }

    header("location:mantras_subcategory.php");
}








if ($_REQUEST['id'] > 0) {
    // $titl = "Update ";
    $select = "SELECT * FROM mantras_subcategory WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
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
                <h6 class="fs-17 fw-semi-bold mb-0">God Details</h6>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($_REQUEST['img']) {
                                echo ' <p class="card-title-desc">Unable to upload photo as the image size is >2mb.</p>';
                            } ?>
                            <p class="card-title-desc">Please fill the required God details.</p>
                            <form action="" method="post" name="finish-form" enctype="multipart/form-data" class="needs-validation" novalidate="">
                                <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
                                    <div class="row is-multiline">
                                        <div class="col-sm-4 mb-3">
                                            <label>God Name</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <input type="text" class="form-control" name="title" id="title" required="" placeholder="Enter Name" value="<?php echo $Row->title; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label for="categories_id" class="fw-semi-bold">Mantras Category</label>
                                            <select class="form-select" id="categories_id" name="categories_id">
                                                <option value="">Select Category</option>

                                                <?php

                                                // Make sure to use single quotes around the variable in the SQL query
                                                $Vselect = "SELECT * FROM mantras_category ORDER BY title";
                                                $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                                                while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                                                    $isSelected = ($VRow->index_id == $Row->categories_id) ? 'selected' : '';
                                                    echo "<option value='{$VRow->index_id}' $isSelected>{$VRow->title}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Featured Image</label>
                                            <?php if (!empty($_REQUEST['id']) && isset($Row) && !empty($Row->photos)): ?>
                                            <div class="mb-2">
                                                <span class="d-block small text-muted mb-1">Current image:</span>
                                                <a href="./uploads/gods/<?php echo htmlspecialchars($Row->photos); ?>" class="d-inline-block" target="_blank">
                                                    <img src="./uploads/gods/<?php echo htmlspecialchars($Row->photos); ?>" alt="Featured" class="img-thumbnail" style="max-width:120px;max-height:120px;object-fit:contain;">
                                                </a>
                                                <p class="small text-muted mb-0 mt-1">Choose a new file below to replace.</p>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
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
                                            <label>Banner Image</label>
                                            <?php if (!empty($_REQUEST['id']) && isset($Row) && !empty($Row->banner)): ?>
                                            <div class="mb-2">
                                                <span class="d-block small text-muted mb-1">Current image:</span>
                                                <a href="./uploads/gods/banner/<?php echo htmlspecialchars($Row->banner); ?>" class="d-inline-block" target="_blank">
                                                    <img src="./uploads/gods/banner/<?php echo htmlspecialchars($Row->banner); ?>" alt="Banner" class="img-thumbnail" style="max-width:120px;max-height:120px;object-fit:contain;">
                                                </a>
                                                <p class="small text-muted mb-0 mt-1">Choose a new file below to replace.</p>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
                                                    <div class="custom-file">
                                                        <input class="fileUp fileup-sm uploadlink" type="file" name="banner" id="banner" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                                                echo 'required=""';
                                                                                                                                                                                                            } ?>>
                                                        <label class="custom-file-label" for="banner"></label>
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
                                            <label>Description</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <textarea class="form-control" id="editor2" data-sample-short name="description" rows="5" placeholder="Enter Description"><?php echo $Row->description; ?></textarea>
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