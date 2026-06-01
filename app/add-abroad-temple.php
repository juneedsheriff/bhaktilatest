<?php ob_start();
include_once './includes/header.php';
include_once '../include/abroad_listing_helpers.php';
include_once './class/fileUploader.php';
error_reporting(0);
function compressAndUpload($file, $folder, $quality = 75)
{
  $image_info = getimagesize($file['tmp_name']);
  $image_type = $image_info[2]; // Get image type

  if ($image_type == IMAGETYPE_JPEG) {
    $image = imagecreatefromjpeg($file['tmp_name']);
  } elseif ($image_type == IMAGETYPE_PNG) {
    $image = imagecreatefrompng($file['tmp_name']);
  } else {
    return false; // Only JPEG and PNG allowed
  }

  $target_file = $folder . '/' . time() . '_' . $file['name'];
  imagejpeg($image, $target_file, $quality); // Compress image

  imagedestroy($image); // Free up memory

  return $target_file;
}
if (isset($_REQUEST['submit'])) {
  $title = $xssClean->clean_input($_REQUEST['title'] ?? '');
  $temple_place = $xssClean->clean_input($_REQUEST['temple_place'] ?? '');
  $sthalam = $xssClean->clean_input($_REQUEST['sthalam'] ?? '');
  $puranam = $xssClean->clean_input($_REQUEST['puranam'] ?? '');
  $varnam = $xssClean->clean_input($_REQUEST['varnam'] ?? '');
  $highlights = $xssClean->clean_input($_REQUEST['highlights'] ?? '');
  $sevas = $xssClean->clean_input($_REQUEST['sevas'] ?? '');
  $open_time = $xssClean->clean_input($_REQUEST['open_time'] ?? '');
  $close_time = $xssClean->clean_input($_REQUEST['close_time'] ?? '');
  $country = $xssClean->clean_input($_REQUEST['country'] ?? '');
  $state = $xssClean->clean_input($_REQUEST['state'] ?? '');
  $city = $xssClean->clean_input($_REQUEST['city'] ?? '');
  $address = $xssClean->clean_input($_REQUEST['address'] ?? '');
  $my_stery = $xssClean->clean_input($_REQUEST['my_stery'] ?? '0');
  $order_by = $xssClean->clean_input($_REQUEST['order_by'] ?? '');
  $time = $xssClean->clean_input($_REQUEST['time'] ?? '');
  $speciality = $xssClean->clean_input($_REQUEST['speciality'] ?? '');
  $status = $xssClean->clean_input($_REQUEST['status'] ?? '');
  $god_id = $xssClean->clean_input($_REQUEST['god_id'] ?? '');
  $mapEmbedInput = $xssClean->clean_input($_REQUEST['map_embed'] ?? '');
  $sanitizedContent = abroad_sanitize_abroad_content_fields($sthalam, $varnam, $mapEmbedInput);
  $sthalam = $sanitizedContent['sthalam'];
  $varnam = $sanitizedContent['varnam'];
  $map_embed = $sanitizedContent['map_embed'];
  $sanitizedContact = abroad_sanitize_abroad_contact_fields($sevas, $speciality);
  $sevas = $sanitizedContact['sevas'];
  $speciality = $sanitizedContact['contact'];
  $log_date = date("Y-m-d");
  $recordId = (int) ($_REQUEST['id'] ?? 0);
  if ($user_role === 'Staff') {
    $status_1 = 'unapproved';
  } elseif ($user_role === 'Admin') {
    $allowed_statuses = ['approved', 'unapproved'];
    $status_1 = in_array($status, $allowed_statuses, true) ? $status : 'unapproved';
    if ($recordId > 0 && $status === '') {
      $existingStatus = mysqli_query($DatabaseCo->dbLink, "SELECT status FROM abroad WHERE index_id='" . (int) $recordId . "' LIMIT 1");
      if ($existingStatus && ($statusRow = mysqli_fetch_assoc($existingStatus))) {
        $status_1 = $statusRow['status'] ?? 'unapproved';
      }
    }
  } else {
    $status_1 = 'unapproved';
  }

  if ($recordId > 0) {
    $d_id = $recordId;
    $DatabaseCo->dbLink->query("UPDATE `abroad` SET `title`='$title', `temple_place`='$temple_place', `sthalam`='$sthalam',`puranam`='$puranam',`varnam`='$varnam',`highlights`='$highlights',`sevas`='$sevas',`open_time`='$open_time',`close_time`='$close_time',`country`='$country',`state`='$state',`city`='$city',`address`='$address',`map_embed`='$map_embed',`log_date`='$log_date',`my_stery`='$my_stery',`god_id`='$god_id',`order_by`='$order_by',`speciality`='$speciality',`time`='$time',`status`='$status_1' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
  } else {
    $DatabaseCo->dbLink->query("INSERT INTO `abroad`( `title`, `temple_place`, `sthalam`, `puranam`,`varnam`,`highlights`,`sevas`,`open_time`,`close_time`,`country`,`state`,`city`,`address`,`map_embed`,`log_date`,`my_stery`,`god_id`,`order_by`,`speciality`,`time`,`status`) VALUES ( '$title', '$temple_place', '$sthalam', '$puranam','$varnam','$highlights','$sevas','$open_time','$close_time','$country','$state','$city','$address','$map_embed','$log_date','$my_stery','$god_id','$order_by','$speciality','$time','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));
    $d_id = mysqli_insert_id($DatabaseCo->dbLink);
  }

  // Not sure what this is intended for; you can remove if not needed.
  $uploadimage = new ImageUploader($DatabaseCo);
  $upload_image_photos = '';
  $upload_image_banner = '';

  // Check if the photos file is uploaded
  if (!empty($_FILES['photos']['tmp_name']) && is_uploaded_file($_FILES['photos']['tmp_name'])) {
    $upload_image_photos = $uploadimage->upload($_FILES['photos'], "abroad");
  }

  // Check if the banner file is uploaded
  if (!empty($_FILES['banner']['tmp_name']) && is_uploaded_file($_FILES['banner']['tmp_name'])) {
    $upload_image_banner = $uploadimage->upload($_FILES['banner'], "abroad/banner");
  }

  // Only update the database if there is a new image for photos
  if ($upload_image_photos != '') {
    $DatabaseCo->dbLink->query("UPDATE `abroad` SET photos='$upload_image_photos' WHERE index_id='$d_id'");
  }

  // Only update the database if there is a new image for banner
  if ($upload_image_banner != '') {
    $DatabaseCo->dbLink->query("UPDATE `abroad` SET banner='$upload_image_banner' WHERE index_id='$d_id'");
  }
  $imageUploader = new ImageUploaderMultiple(null);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 1: Fetch existing images from the database
    $query = "SELECT gallery_image FROM `abroad` WHERE index_id=?";
    $stmt = $DatabaseCo->dbLink->prepare($query);
    $stmt->bind_param("i", $d_id); // Assuming $d_id is set with the current temple ID
    $stmt->execute();
    $stmt->bind_result($existing_images);
    $stmt->fetch();
    $stmt->close();

    // Convert existing images to an array, if they exist
    $existing_images_array = array_values(array_filter(array_map('trim', explode(',', (string) ($existing_images ?? '')))));

    // Create an associative array to track unique images
    $unique_images = $existing_images_array !== [] ? array_flip($existing_images_array) : [];

    // Step 2: Handle file uploads
    if (isset($_FILES['gallery_image']) && !empty($_FILES['gallery_image']['name'][0])) {
      $galleryUploadDir = __DIR__ . '/uploads/abroad/gallery';
      if (!is_dir($galleryUploadDir)) {
        @mkdir($galleryUploadDir, 0755, true);
      }

      // Upload new images
      $uploadedImages = $imageUploader->uploadMultiple($_FILES['gallery_image'], 'abroad/gallery');

      // Check if the upload was successful
      if ($uploadedImages) {
        // Iterate through the uploaded images
        foreach ($uploadedImages as $uploaded_image) {
          if (!isset($unique_images[$uploaded_image])) {
            $unique_images[$uploaded_image] = true;
          }
        }
      }
    }

    if (!empty($unique_images)) {
      $gallery_images_str = implode(',', array_keys($unique_images));
      $query = "UPDATE `abroad` SET gallery_image=? WHERE index_id=?";
      $stmt = $DatabaseCo->dbLink->prepare($query);

      if ($stmt) {
        $stmt->bind_param("si", $gallery_images_str, $d_id);
        $stmt->execute();
        $stmt->close();
      }
    }
  }

  while (ob_get_level() > 0) {
    ob_end_clean();
  }
  header('Location: temple-abroad-listing.php?saved=1&id=' . (int) $d_id);
  exit;
}








if (!empty($_REQUEST['id']) && (int) $_REQUEST['id'] > 0) {
  $titl = "Update ";
  $select = "SELECT * FROM abroad WHERE index_id='" . (int) $_REQUEST['id'] . "'";
  $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
  $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
  $titl = "Add New ";
  $Row = null;
}
if (!$Row) {
  $Row = (object) [
    'index_id' => 0,
    'title' => '',
    'temple_place' => '',
    'god_id' => '',
    'log_date' => date('Y-m-d'),
    'open_time' => '00:00:00',
    'close_time' => '00:00:00',
    'order_by' => '',
    'status' => 'unapproved',
    'my_stery' => '0',
    'country' => '',
    'state' => '',
    'city' => '',
    'address' => '',
    'time' => '',
    'puranam' => '',
    'highlights' => '',
    'sevas' => '',
    'gallery_image' => '',
    'map_embed' => '',
  ];
}
$existingGalleryImages = ($Row && !empty($Row->gallery_image)) ? abroad_admin_gallery_images($Row) : [];
$country_code = $Row->country ?? '';
$dbLink = $DatabaseCo->dbLink; // Assign the dbLink to a variable
// Retrieve imageIndex and nameIndex from the POST request
$imageIndex = isset($_POST['imageIndex']) ? intval($_POST['imageIndex']) : null;
$nameIndex = isset($_POST['nameIndex']) ? $_POST['nameIndex'] : null;  // Assuming nameIndex is a string like a filename

// Validate that imageIndex and nameIndex were provided
if (!isset($_REQUEST['submit']) && $imageIndex !== null && $nameIndex !== null && $nameIndex !== '') {
  // Prepare SQL statement to remove the specific image
  $stmt = $dbLink->prepare("UPDATE abroad SET gallery_image = REPLACE(gallery_image, ?, '') WHERE index_id = ?");
  $stmt->bind_param("si", $nameIndex, $imageIndex);  // 's' for string type if nameIndex is a filename

  // Execute the query and check for success
  if ($stmt->execute()) {
    // Close the update statement
    $stmt->close();

    // Fetch the updated images to return to the client
    $stmt = $dbLink->prepare("SELECT gallery_image FROM abroad WHERE index_id = ?");
    $stmt->bind_param("i", $imageIndex);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Return success response with updated images
    echo json_encode([
      'status' => 'success',
      'message' => 'Image removed successfully',
      'remainingImages' => explode(',', $row['gallery_image'])
    ]);
  } else {
    // Error in query execution
    echo json_encode(['status' => 'error', 'message' => 'Failed to update image']);
  }

  while (ob_get_level() > 0) {
    ob_end_clean();
  }
  exit;
} else {
  // Invalid indices received
  // echo json_encode(['status' => 'error', 'message' => 'Invalid indices received']);
}
$state_code = $_POST['state_code'] ?? '';

if ($state_code) {
  $query = "SELECT * FROM city WHERE state_code = '$state_code' ORDER BY city_name";
  $result = mysqli_query($DatabaseCo->dbLink, $query);

  while ($row = mysqli_fetch_object($result)) {
    echo "<option value='{$row->city_id}'>{$row->city_name}</option>";
  }
}
?>

<style>
  .gallery {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  .gallery-existing,
  .gallery-preview {
    display: contents;
  }

  .image-container .remove {
    position: absolute;
    top: 0;
    right: 0;
    background-color: red;
    color: white;
    border: none;
    cursor: pointer;
    padding: 3px 5px;
    line-height: 1;
  }

  .image-container {
    position: relative;
    width: 100px;
    height: 100px;
    overflow: hidden;
    border: 1px solid #ddd;
    border-radius: 5px;
  }

  .image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: red;
    color: white;
    border: none;
    cursor: pointer;
    padding: 2px 5px;
    border-radius: 3px;
  }
</style>
<style>
  .form-select {
    height: 42px !important;

  }

  /* Make sure the Select2 box blends with the form-select styling */
  .select2-container .select2-selection--single {
    height: 42px;
    /* Same height as Bootstrap's form-select */
    background-color: #f8f4f3 !important;
    border: 1px solid #ced4da;
    /* Border matching Bootstrap */
    border-radius: 0.375rem;
    /* Same border-radius as Bootstrap */
    font-size: 1rem;
    /* Match font size */
  }
.select2-selection__clear{
  display: none !important;
}
.SumoSelect > .CaptionCont > label > i:before, .select2-container--default .select2-selection--single .select2-selection__arrow b:before {
   display: none;
}
  .select2-container .select2-selection__arrow {
      font-size: 50px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
    position: absolute;
    top: 1px;
    right: 1px;
    width: 20px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #888 transparent transparent transparent;
    border-style: solid;
    border-width: 5px 4px 0 4px;
    height: 5px;
    left: 50%;
    margin-left: -4px;
    margin-top: -2px;
    position: absolute;
    top: 50%;
    font-size: 20px !important;
    width: 0;
}
  /* Customize the dropdown options */
  .select2-results__option {
    padding: 10px;
  }

  .select2-results__option--highlighted {
    background-color: #007bff;
    /* Highlight color */
    color: white;
  }

  .select2-results__option--selected {
    background-color: #28a745;
    /* Selected option color */
    color: white;
  }
</style>
<div class="body-content">
  <div class="decoration blur-2"></div>
  <div class="decoration blur-3"></div>
  <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Temple Abroad</div>
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
              <p class="card-title-desc">Please fill the required temples details.</p>
              <form action="" method="post" name="finish-form" enctype="multipart/form-data" class="needs-validation" novalidate="">
                <?php if ((int) ($Row->index_id ?? 0) > 0) : ?>
                  <input type="hidden" name="id" value="<?php echo (int) $Row->index_id; ?>">
                <?php endif; ?>
                <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
                  <div class="row is-multiline">
                    <div class="col-sm-4 mb-3">
                      <label>Temple Name</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input type="text" class="form-control" name="title" id="title" required="" placeholder="Enter Name" value="<?php echo $Row->title; ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                      <!-- start form group -->

                      <label class="required fw-medium ">God</label>

                      <select class="form-select " name="god_id" id="god_id">
                        <option value="" disabled <?php echo empty($Row->god_id) ? 'selected' : ''; ?>>Select God</option>
                        <?php

                        // Make sure to use single quotes around the variable in the SQL query
                        $Vselect = "SELECT * FROM god  ORDER BY god_name";
                        $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                        while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                          $isSelected = ($VRow->index_id == $Row->god_id) ? 'selected' : '';
                          echo "<option value='{$VRow->index_id}' $isSelected>{$VRow->god_name}</option>";
                        }
                        ?>
                      </select>

                      <!-- end /. form group -->
                    </div>
                    <div class="col-sm-4 mb-3">
                      <label>Temple place</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input type="text" class="form-control" name="temple_place" id="temple_place" required="" placeholder="Enter Temple Place" value="<?php echo $Row->temple_place; ?>">
                        </div>
                      </div>
                    </div>
                    <!-- <div class="col-sm-4 mb-3">
                      <label>Temple Date</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input
                            type="date"
                            class="form-control"
                            name="log_date"
                            id="log_date"
                            required
                            placeholder="Date"
                            value="<?php echo date('d-m-y', strtotime($Row->log_date)); ?>">
                        </div>
                      </div>
                    </div> -->
                    <!-- 
                    <div class="col-sm-4 mb-3">
                      <label>Temple Open Time</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input type="time" class="form-control" name="open_time" id="open_time" required="" placeholder="Temple Open Time" value="<?php echo date("H:i:s", strtotime($Row->open_time)); ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                      <label>Temple Close Time</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input type="time" class="form-control" name="close_time" id="close_time" required="" placeholder="Temple Close Time" value="<?php echo date("H:i:s", strtotime($Row->close_time)); ?>">
                        </div>
                      </div>
                    </div> -->
                    <div class="col-sm-4 mb-3">
                      <label>Order Number</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <input type="number" class="form-control" name="order_by" id="order_by" required="" placeholder="Enter Order Number" value="<?php echo $Row->order_by; ?>">
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
                    <?php if ($user_role === 'Admin'): ?>
                    <div class="col-sm-4 mb-3">
                      <!-- start form group -->

                      <label class="required fw-medium ">Status</label>

                      <select class="form-select " name="status" id="">
                        <option value="" disabled <?php echo empty($Row->status) ? 'selected' : ''; ?>>Select Status</option>
                        <option value="approved" <?php echo $Row->status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="unapproved" <?php echo $Row->status === 'unapproved' ? 'selected' : ''; ?>>Unapproved</option>
                      </select>

                      <!-- end /. form group -->
                    </div>
                    <?php endif; ?>

                    <div class="col-sm-7 mb-3 ">
                      <label> </label>
                      <div class="form-check">
                        <div class="form-check mb-2">
                          <!-- Checkbox with ternary operator for checked status -->
                          <input type="hidden" name="my_stery" value="0">
                          <input class="form-check-input"
                            type="checkbox"
                            value="1"
                            name="my_stery"
                            id="Mystery"
                            <?php echo ($Row->my_stery == 1) ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="Mystery">Is Mystery Temple</label>
                        </div>
                      </div>
                    </div>
                    <div class=" mb-4">
                      <div class="card-header position-relative">
                        <h6 class="fs-17 fw-semi-bold mb-0">Location</h6>
                      </div>
                      <div class="card-body">
                        <div class="row g-4">
                          <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Country</label>
                              <?php
                              $options = ''; // Initialize an empty string for options
                              $Vselect = "SELECT * FROM country ORDER BY country_name";

                              // Execute the SQL query and handle errors
                              $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                              if (!$VSQL_STATEMENT) {
                                die("Database query failed: " . mysqli_error($DatabaseCo->dbLink));
                              }

                              // Fetch each row and append to the $options string
                              foreach (mysqli_fetch_all($VSQL_STATEMENT, MYSQLI_ASSOC) as $VRow) {
                                // Check if the current row should be marked as selected
                                $isSelected = ($VRow['country_code'] === $Row->country) ? 'selected' : '';
                                $options .= "<option value='{$VRow['country_code']}' $isSelected>{$VRow['country_name']}</option>";
                              }
                              ?>

                              <select class="form-select mb-3" name="country" id="country">
                                <option value="" disabled <?php echo empty($Row->country) ? 'selected' : ''; ?>>Select Country</option>
                                <?php echo $options; // Output all options 
                                ?>
                              </select>
                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">State</label>
                              <?php
                              $options = ''; // Initialize an empty string for options

                              if (!empty($country_code)) {
                                $stateQuery = "SELECT * FROM state WHERE country_code = '" . mysqli_real_escape_string($DatabaseCo->dbLink, $country_code) . "' ORDER BY state_name";
                                $stateResult = mysqli_query($DatabaseCo->dbLink, $stateQuery);

                                if (!$stateResult) {
                                  die("Database query failed: " . mysqli_error($DatabaseCo->dbLink));
                                }

                                // Fetch all rows and build the options string
                                foreach (mysqli_fetch_all($stateResult, MYSQLI_ASSOC) as $stateRow) {
                                  $selected = ($stateRow['state_code'] == $Row->state) ? 'selected' : '';
                                  $options .= "<option value='{$stateRow['state_code']}' $selected>{$stateRow['state_name']}</option>";
                                }
                              }
                              ?>

                              <select class="form-select mb-3" name="state" id="state">
                                <option value="" disabled <?php echo empty($Row->state) ? 'selected' : ''; ?>>Select State</option>
                                <?php echo $options; // Output all options 
                                ?>
                              </select>





                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">City</label>
                              <?php
                              $options = ''; // Initialize an empty string for options

                              // Check if city and state codes are available
                              $currentCity = isset($Row->city) ? $Row->city : '';
                              $stateCode = isset($Row->state) ? $Row->state : '';

                              if (!empty($stateCode)) {
                                $cityQuery = "SELECT * FROM city WHERE state_code = '" . mysqli_real_escape_string($DatabaseCo->dbLink, $stateCode) . "' ORDER BY city_name";
                                $cityResult = mysqli_query($DatabaseCo->dbLink, $cityQuery);

                                if (!$cityResult) {
                                  die("Database query failed: " . mysqli_error($DatabaseCo->dbLink));
                                }

                                // Fetch all rows and build the options string
                                foreach (mysqli_fetch_all($cityResult, MYSQLI_ASSOC) as $cityRow) {
                                  $selected = ($cityRow['city_id'] == $currentCity) ? 'selected' : '';
                                  $options .= "<option value='{$cityRow['city_id']}' $selected>{$cityRow['city_name']}</option>";
                                }
                              }
                              ?>

                              <select class="form-select mb-3" name="city" id="city">
                                <option value="" disabled <?php echo empty($Row->city) ? 'selected' : ''; ?>>Select City</option>
                                <?php echo $options; // Output all options 
                                ?>
                              </select>


                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Address</label>
                              <input type="text" class="form-control" name="address" placeholder="Enter Address" required="" value="<?php echo $Row->address; ?>">
                            </div>
                            <!-- end /. form group -->
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col-12">
                            <label class="fw-medium mb-2">Map Embed URL</label>
                            <input type="url" class="form-control" name="map_embed" placeholder="https://www.google.com/maps/embed?pb=..." value="<?php
                              $displayMapEmbed = '';
                              if (isset($Row) && $Row) {
                                  $displayMapEmbed = trim((string) ($Row->map_embed ?? ''));
                                  if ($displayMapEmbed === '') {
                                      $displayMapEmbed = abroad_detail_map_embed_src($Row);
                                  }
                              }
                              echo htmlspecialchars($displayMapEmbed, ENT_QUOTES, 'UTF-8');
                            ?>">
                            <small class="text-muted">Google Maps embed URL for the Location section. Map iframes pasted into Sthalam or Varnam are moved here automatically on save.</small>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Open Time & End Time</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor6" data-sample-short name="time" rows="5" placeholder="Enter Time"><?php echo $Row->time; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Contact</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor7" data-sample-short name="speciality" rows="5" placeholder="Enter Contact (phone, email, website)"><?php echo isset($Row) && $Row ? abroad_detail_contact_field_value($Row) : ''; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Sthalam</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor1" name="sthalam" rows="5" placeholder="Enter Sthalam"><?php echo isset($Row) && $Row ? abroad_detail_sthalam_text($Row) : ''; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Puranam</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor2" name="puranam" rows="5" id="puranam" placeholder="Enter Puranam"><?php echo $Row->puranam; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Varnam</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor3" name="varnam" rows="5" id="varnam" placeholder="Enter Varnam"><?php echo isset($Row) && $Row ? abroad_detail_varnam_text($Row) : ''; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Highlights</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor4" name="highlights" rows="5" id="highlights" placeholder="Enter Highlights"><?php echo $Row->highlights; ?></textarea>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-12 mb-3">
                      <label>Sevas</label>
                      <div class="field">
                        <div class="control has-icons-left">
                          <textarea class="form-control" id="editor5" name="sevas" rows="5" id="sevas" placeholder="Enter Sevas"><?php echo isset($Row) && $Row ? abroad_detail_sevas_text($Row) : ''; ?></textarea>
                        </div>
                      </div>
                    </div>




                    <div class=" mb-4">
                      <div class="card-header position-relative">
                        <h6 class="fs-17 fw-semi-bold mb-0">Gallery</h6>
                      </div>
                      <div class="card-body">

                        <div class="">
                          <label class="required fw-medium mb-2">Gallery</label>
                          <a href="#" class="bg-secondary uploadlink"></a>
                          <input class=" upload fileUp fileup-sm uploadlink" type="file" name="gallery_image[]" id="fileInput" accept=".jpg, .png, image/jpeg, image/png" multiple value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                              echo 'required=""';
                                                                                                                                                                                            } ?>>
                          <div class="form-text">Recommended to 350 x 350 px (png, jpg, jpeg).</div>
                        </div>
                        <div class="gallery">
                          <div class="gallery-existing" id="galleryExisting">
                            <?php if ($Row && !empty($existingGalleryImages)) : ?>
                              <?php foreach ($existingGalleryImages as $image) :
                                $imagePath = abroad_admin_gallery_url($image);
                                if ($imagePath === '') {
                                  continue;
                                }
                                $imageId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $image);
                              ?>
                                <div class="image-container existing-image-item" id="image-<?php echo htmlspecialchars($imageId, ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>">
                                  <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Gallery Image" class="existing-image">
                                  <button type="button" class="remove" data-index="<?php echo (int) $Row->index_id; ?>" data-name="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>">X</button>
                                </div>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </div>
                          <div class="gallery-preview" id="galleryPreview"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- <div class=" mb-4">
                      <div class="card-header position-relative">
                        <h6 class="fs-17 fw-semi-bold mb-0">Image</h6>
                      </div>
                      <div class="card-body">
                     
                        <div class="">
                          <label class="required fw-medium mb-2">Image</label>
                          <a href="#" class="bg-secondary uploadlink"></a>
                          <input class="fileUp fileup-sm uploadlink" type="file" name="photos" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
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
  $(document).ready(function() {
    $('#city').select2({
      placeholder: 'Select a City', // Optional placeholder text
      allowClear: true // Allows clearing the selection
    });
  });
</script>

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
  CKEDITOR.replace('editor6');
</script>
<script>
  CKEDITOR.replace('editor7');
</script>
<script>
  document.querySelector('form[name="finish-form"]').addEventListener('submit', function () {
    if (typeof CKEDITOR !== 'undefined') {
      for (var instance in CKEDITOR.instances) {
        if (CKEDITOR.instances.hasOwnProperty(instance)) {
          CKEDITOR.instances[instance].updateElement();
        }
      }
    }
  });
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
<script>
  let selectedFiles = []; // Array to track selected files

  // Attach the event listener to the file input
  document.getElementById("fileInput").addEventListener("change", handleFileSelection);

  function handleFileSelection(event) {
    const input = event.target;
    // Reset the selectedFiles array and repopulate it from the input
    selectedFiles = Array.from(input.files);

    renderGallery(); // Render the images in the gallery
    uploadFiles(); // Automatically upload files after selection if needed
  }

  function renderGallery() {
    const galleryPreview = document.getElementById("galleryPreview");
    if (!galleryPreview) {
      return;
    }

    galleryPreview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e) {
        const imageContainer = document.createElement("div");
        imageContainer.classList.add("image-container", "preview-image-item");

        const img = document.createElement("img");
        img.src = e.target.result;
        img.alt = file.name;

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.classList.add("remove-btn");
        removeBtn.textContent = "X";
        removeBtn.onclick = function() {
          removeImage(index);
        };

        imageContainer.appendChild(img);
        imageContainer.appendChild(removeBtn);
        galleryPreview.appendChild(imageContainer);
      };
      reader.readAsDataURL(file);
    });
  }

  function removeImage(index) {
    selectedFiles.splice(index, 1); // Remove the file from the selectedFiles array

    // Update the file input's files using DataTransfer
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById("fileInput").files = dataTransfer.files;

    renderGallery(); // Re-render the gallery to update the view
  }

  function uploadFiles() {
    // Placeholder function for file upload logic
    console.log("Uploading files:", selectedFiles);
  }
</script>


<script>
  $(document).on('click', '.existing-image-item .remove', function() {
    var imageIndex = $(this).data('index');
    var nameIndex = $(this).data('name');
    var $item = $(this).closest('.existing-image-item');

    $.ajax({
      type: "POST",
      url: "add-abroad-temple.php",
      data: {
        imageIndex: imageIndex,
        nameIndex: nameIndex
      },
      success: function(response) {
        try {
          var result = JSON.parse(response);
          if (result.status === 'success') {
            $item.remove();
          } else {
            alert(result.message);
          }
        } catch (e) {
          console.log("Parsing Error:", e);
        }
      },
      error: function(error) {
        console.log("Error removing image:", error);
      }
    });
  });
</script>
