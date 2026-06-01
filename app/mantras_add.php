<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);

if (isset($_REQUEST['submit'])) {
  $title = $xssClean->clean_input($_REQUEST['title']);
  $content = $xssClean->clean_input($_REQUEST['content']);
  $sub_category = $xssClean->clean_input($_REQUEST['sub_category']);
  $categories_id = $xssClean->clean_input($_REQUEST['categories_id']);
  $mantras_title = $xssClean->clean_input($_REQUEST['mantras_title']);
  $status = $xssClean->clean_input($_REQUEST['status']);
  if( $user_role === 'Staff'){
      $status_1='unapproved';
    }elseif($user_role === "Admin"){
      $status_1=$status;
    }else{
    
    }
  // Initialize variable for audio file upload
  $upload_audio = '';

  // Handle file upload
  if (isset($_FILES['audio']) && is_uploaded_file($_FILES['audio']["tmp_name"])) {
    // Assuming the ImageUploader class is set up correctly for audio uploads
    $uploadimage = new AudioUploader($DatabaseCo);
    $upload_audio = $uploadimage->upload($_FILES['audio'], "mantras_audio");
  }

  if ($_REQUEST['id'] > 0) {
    // Update existing record
    $d_id = $_REQUEST['id'];
    $query = "UPDATE `mantras_stotras` SET `title`='$title', `content`='$content', `sub_category`='$sub_category',`categories_id`='$categories_id',`mantras_title`='$mantras_title',`status`='$status_1'";

    // Only include audio update if a new file was uploaded
    if ($upload_audio != '') {
      $query .= ", `audio`='$upload_audio'";
    }

    $query .= " WHERE `index_id`='$d_id'";
    $DatabaseCo->dbLink->query($query) or die(mysqli_error($DatabaseCo->dbLink));
  } else {
    // Insert new record
    $DatabaseCo->dbLink->query("INSERT INTO `mantras_stotras`(`title`, `content`, `sub_category`, `audio`,`categories_id`,`mantras_title`,status) VALUES ('$title', '$content', '$sub_category', '$upload_audio','$categories_id','$mantras_title','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));
  }

  // Redirect after operation
  header("location:mantras_list.php");
}








if ($_REQUEST['id'] > 0) {
  // $titl = "Update ";
  $select = "SELECT * FROM mantras_stotras WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
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
        <h6 class="fs-17 fw-semi-bold mb-0">Mantras Stotras</h6>
      </div>
      <div class="card-body">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <?php if ($_REQUEST['img']) {
                echo ' <p class="card-title-desc">Unable to upload photo as the image size is >2mb.</p>';
              } ?>
              <p class="card-title-desc">Please fill the required Mantras Stotras temples details.</p>
              <form action="" method="post" name="finish-form" enctype="multipart/form-data" class="needs-validation">
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
                      <label for="categories_id" class="fw-semi-bold">Mantras Category</label>
                      <select class="form-select" id="categories_id" name="categories_id">
                        <option value="">Select Category</option>
                        <?php
                        // Fetch categories from the database
                        $Vselect = "SELECT * FROM mantras_category ORDER BY title";
                        $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                        while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                          // Check if a category is pre-selected (for edit functionality)
                          $isSelected = ($VRow->index_id == $Row->categories_id) ? 'selected' : '';
                          echo "<option value='{$VRow->index_id}' $isSelected>{$VRow->title}</option>";
                        }
                        ?>
                      </select>
                    </div>

                    <div class="col-sm-4 mb-3">
                      <label class="required fw-medium">God</label>
                      <select class="form-select" name="sub_category" id="sub_category" required>
                        <option selected disabled>Select a God</option>
                        <?php
                        // Pre-fill the `god_id` dropdown if editing
                        if (isset($Row->categories_id)) {
                          $query = "SELECT index_id, title FROM mantras_subcategory WHERE categories_id = ? ORDER BY title";
                          if ($stmt = $DatabaseCo->dbLink->prepare($query)) {
                            $stmt->bind_param('i', $Row->categories_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                              $isSelected = ($row['index_id'] == $Row->sub_category) ? 'selected' : '';
                              echo "<option value='{$row['index_id']}' $isSelected>{$row['title']}</option>";
                            }
                            $stmt->close();
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-sm-4 mb-3">
                      <label class="required fw-medium">Mantras Title</label>
                      <select class="form-select" id="mantras_title" name="mantras_title">
                        <option value="">Select Mantras</option>
                        <?php
                        // Fetch categories from the database
                        $Vselect = "SELECT * FROM mantras_title ORDER BY title";
                        $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                        while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                          // Check if a category is pre-selected (for edit functionality)
                          $isSelected = ($VRow->index_id == $Row->mantras_title) ? 'selected' : '';
                          echo "<option value='{$VRow->index_id}' $isSelected>{$VRow->title}</option>";
                        }
                        ?>
                      </select>
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


                    <div class="col-sm-4 mb-3">
                      <label> Audio</label>
                      <div class="field">
                        <div class="custom-file">
                          <input class="fileUp fileup-sm uploadlink" type="file" name="audio" id="audio" accept=".mp3, .wav, .ogg" <?php if ($titl == "Add New ") {
                                                                                                                                      echo 'required';
                                                                                                                                    } ?>>
                          <!-- <label class="custom-file-label" for="audio">Choose audio file</label> -->
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-12 mb-3">
                      <label>Content</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor1" data-sample-short name="content" rows="5" placeholder="Enter content"><?php echo $Row->content; ?></textarea>
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
<script>
  CKEDITOR.replace('editor4');
</script>
<script>
  CKEDITOR.replace('editor5');
</script>
<script>
  // Load states when a country is selected
  $('#country').change(function() {
    let countryCode = $(this).val();
    $.ajax({
      url: 'get_states.php',
      type: 'POST',
      data: {
        country_code: countryCode
      },
      success: function(response) {
        $('#state').html(response);
        $('#city').html('<option selected disabled>Select City</option>'); // Reset city dropdown
      }
    });
  });

  // Load cities when a state is selected
  $('#state').change(function() {
    let stateCode = $(this).val();
    $.ajax({
      url: 'get_cities.php',
      type: 'POST',
      data: {
        state_code: stateCode
      },
      success: function(response) {
        $('#city').html(response);
      }
    });
  });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#categories_id').change(function() {
      let categoryId = $(this).val();

      // Check if a valid category ID is selected
      if (categoryId) {
        $.ajax({
          url: 'fetch_items.php', // The PHP script for fetching God options
          type: 'POST',
          data: {
            category_id: categoryId
          },
          success: function(response) {
            $('#sub_category').html(response); // Populate the God dropdown
          },
          error: function() {
            alert('Failed to fetch data. Please try again.');
          },
        });
      } else {
        // Reset the God dropdown if no category is selected
        $('#sub_category').html('<option selected disabled>Select a God</option>');
      }
    });
  });
</script>