<?php ob_start();

include_once './includes/header.php';

include_once './class/databaseConn.php';

include_once 'lib/requestHandler.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
error_reporting(1);
$DatabaseCo = new DatabaseConn();

error_reporting(5);



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

  $title = $xssClean->clean_input($_REQUEST['title']);

  $speciality_title = $xssClean->clean_input($_REQUEST['speciality_title']);

  $temple_place = $xssClean->clean_input($_REQUEST['temple_place']);

  $sthalam = $xssClean->clean_input($_REQUEST['sthalam']);

  $puranam = $xssClean->clean_input($_REQUEST['puranam']);

  $varnam = $xssClean->clean_input($_REQUEST['varnam']);

  $highlights = $xssClean->clean_input($_REQUEST['highlights']);

  $sevas = $xssClean->clean_input($_REQUEST['sevas']);

  $open_time = $xssClean->clean_input($_REQUEST['open_time']);

  $close_time = $xssClean->clean_input($_REQUEST['close_time']);

  $country = $xssClean->clean_input($_REQUEST['country']);

  $state = $xssClean->clean_input($_REQUEST['state']);

  $city = $xssClean->clean_input($_REQUEST['city']);

  $address = $xssClean->clean_input($_REQUEST['address']);

  $god_id = $xssClean->clean_input($_REQUEST['god_id']);

  $my_stery = $xssClean->clean_input($_REQUEST['my_stery']);

  $order_by = $xssClean->clean_input($_REQUEST['order_by']);

  $time = $xssClean->clean_input($_REQUEST['time']);

  $speciality = $xssClean->clean_input($_REQUEST['speciality']);

  $status = $xssClean->clean_input($_REQUEST['status']);

  $log_date = date("Y-m-d");

if( $user_role === 'Staff'){

  $status_1='unapproved';

}elseif($user_role === "Admin"){

  $status_1=$status;

}else{



}



  if ($_REQUEST['id'] > 0) {

    $d_id = $_REQUEST['id'];

    $DatabaseCo->dbLink->query("UPDATE `temples` SET `title`='$title',`speciality_title`='$speciality_title',`sthalam`='$sthalam',`puranam`='$puranam',`varnam`='$varnam',`highlights`='$highlights',`sevas`='$sevas',`country`='$country',`state`='$state',`city`='$city',`address`='$address',`log_date`='$log_date',`god_id`='$god_id',`my_stery`='$my_stery',`speciality`='$speciality',`time`='$time',`status`='$status_1'WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));

  } else {

    $DatabaseCo->dbLink->query("INSERT INTO `temples`( `title`,`speciality_title`,`sthalam`, `puranam`,`varnam`,`highlights`,`sevas`,`country`,`state`,`city`,`address`,`log_date`,`god_id`,`my_stery`,`speciality`,`time`,`status`) VALUES ( '$title','$speciality_title','$sthalam', '$puranam','$varnam','$highlights','$sevas','$country','$state','$city','$address','$log_date','$god_id','$my_stery','$speciality','$time','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));

    $d_id = mysqli_insert_id($DatabaseCo->dbLink);

  }



  // Not sure what this is intended for; you can remove if not needed.

  $uploadimage = new ImageUploader($DatabaseCo);



  $upload_image_photos = '';

  $upload_image_banner = '';



  // Check if the photos file is uploaded

  if (is_uploaded_file($_FILES['photos']["tmp_name"])) {

    $upload_image_photos = $uploadimage->upload($_FILES['photos'], "temple");

  }



  // Check if the banner file is uploaded

  if (is_uploaded_file($_FILES['banner']["tmp_name"])) {

    $upload_image_banner = $uploadimage->upload($_FILES['banner'], "temple/banner");

  }



  // Only update the database if there is a new image for photos

  if ($upload_image_photos != '') {

    $DatabaseCo->dbLink->query("UPDATE `temples` SET photos='$upload_image_photos' WHERE index_id='$d_id'");

  }



  // Only update the database if there is a new image for banner

  if ($upload_image_banner != '') {

    $DatabaseCo->dbLink->query("UPDATE `temples` SET banner='$upload_image_banner' WHERE index_id='$d_id'");

  }



  // Upload and compress multiple gallery images

  // Assuming you have a valid PDO connection $pdo

  $imageUploader = new ImageUploaderMultiple($pdo);



  if ($_SERVER['REQUEST_METHOD'] === 'POST') {



    // Step 1: Fetch existing images from the database

    $query = "SELECT gallery_image FROM `temples` WHERE index_id=?";

    $stmt = $DatabaseCo->dbLink->prepare($query);

    $stmt->bind_param("i", $d_id); // Assuming $d_id is set with the current temple ID

    $stmt->execute();

    $stmt->bind_result($existing_images);

    $stmt->fetch();

    $stmt->close();



    // Convert existing images to an array, if they exist

    $existing_images_array = !empty($existing_images) ? explode(',', $existing_images) : [];



    // Create an associative array to track unique images

    $unique_images = array_flip($existing_images_array); // Flips the existing images to track uniqueness



    // Step 2: Handle file uploads

    if (isset($_FILES['gallery_image']) && !empty($_FILES['gallery_image']['name'][0])) {

      // Upload new images

      $uploadedImages = $imageUploader->uploadMultiple($_FILES['gallery_image'], 'Temple_gallery');



      // Check if the upload was successful

      if ($uploadedImages) {

        // Iterate through the uploaded images

        foreach ($uploadedImages as $uploaded_image) {

          // Only add the uploaded image if it's not already in the unique_images array

          if (!isset($unique_images[$uploaded_image])) {

            $unique_images[$uploaded_image] = true; // Mark as existing

          } else {

            echo "Duplicate image not uploaded: " . htmlspecialchars($uploaded_image) . "<br>";

          }

        }

      } else {

        echo "Failed to upload images.";

      }

    } else {

      echo "No files selected for upload.";

    }



    // Step 3: Prepare the final list of unique images for storage

    if (!empty($unique_images)) {

      $gallery_images_str = implode(',', array_keys($unique_images)); // Get only the keys (image names)



      // Prepare and execute the update query

      $query = "UPDATE `temples` SET gallery_image=? WHERE index_id=?";

      $stmt = $DatabaseCo->dbLink->prepare($query);



      if ($stmt) {

        // Bind the parameters

        $stmt->bind_param("si", $gallery_images_str, $d_id);

        $result = $stmt->execute();



        // Check the result of the execution

        if ($result) {

          echo "Gallery images updated successfully.";

        } else {

          echo "Failed to update gallery images: " . $stmt->error;

        }



        // Close the statement

        $stmt->close();

      } else {

        echo "Failed to prepare statement: " . $DatabaseCo->dbLink->error;

      }

    } else {

      echo "No new images uploaded.";

    }

  }













  if ($_REQUEST['edit'] > 0) {

    header("location:temple-listing.php?alt=1");

  } else {

    header("location:add-temple.php");

  }



  header("location:temple-listing.php");

}

















if ($_REQUEST['id'] > 0) {

  // $titl = "Update ";

  $select = "SELECT * FROM temples WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;

  $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

  $Row = mysqli_fetch_object($SQL_STATEMENT);

} else {

  $titl = "";

}





$dbLink = $DatabaseCo->dbLink; // Assign the dbLink to a variable

// Retrieve imageIndex and nameIndex from the POST request

$imageIndex = isset($_POST['imageIndex']) ? intval($_POST['imageIndex']) : null;

$nameIndex = isset($_POST['nameIndex']) ? $_POST['nameIndex'] : null;  // Assuming nameIndex is a string like a filename



// Validate that imageIndex and nameIndex were provided

if ($imageIndex !== null && $nameIndex !== null) {

  // Prepare SQL statement to remove the specific image

  $stmt = $dbLink->prepare("UPDATE temples SET gallery_image = REPLACE(gallery_image, ?, '') WHERE index_id = ?");

  $stmt->bind_param("si", $nameIndex, $imageIndex);  // 's' for string type if nameIndex is a filename



  // Execute the query and check for success

  if ($stmt->execute()) {

    // Close the update statement

    $stmt->close();



    // Fetch the updated images to return to the client

    $stmt = $dbLink->prepare("SELECT gallery_image FROM temples WHERE index_id = ?");

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



  // Close statement

  $stmt->close();

} else {

  // Invalid indices received

  // echo json_encode(['status' => 'error', 'message' => 'Invalid indices received']);

}





?>









<style>

  .gallery {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

    margin-top: 20px;

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



  .select2-selection__clear {

    display: none !important;

  }



  .SumoSelect>.CaptionCont>label>i:before,

  .select2-container--default .select2-selection--single .select2-selection__arrow b:before {

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

    height: 0;

    left: 50%;

    margin-left: -4px;

    margin-top: -2px;

    position: absolute;

    top: 50%;

    font-size: 20px;

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

  <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Temples in India</div>

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

                        <option selected disabled>Select God</option>

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

                      <label>Speciality Title</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <input type="text" class="form-control" name="speciality_title" id="speciality_title" required="" placeholder="Enter Title" value="<?php echo $Row->speciality_title; ?>">

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



                    <!-- <div class="col-sm-4 mb-3">

                      <label>Temple Open Time</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <input type="time" class="form-control" name="open_time" id="open_time" required="" placeholder="Temple Open Time" value="<?php echo date("H:i:s", strtotime($Row->open_time)); ?>">

                        </div>

                      </div>

                    </div> -->

                    <!-- <div class="col-sm-4 mb-3">

                      <label>Temple Close Time</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <input type="time" class="form-control" name="close_time" id="close_time" required="" placeholder="Temple Close Time" value="<?php echo date("H:i:s", strtotime($Row->close_time)); ?>">

                        </div>

                      </div>

                    </div> -->

                    <div class="col-sm-4 mb-3">

                      <label>Featured Image</label>



                      <div class="col-sm-6 mb-3">

                        <div class="field">

                          <!-- <label class="pull-left">image </label> -->

                          <div class="custom-file">

                            <input class="fileUp fileup-sm uploadlink" type="file" name="photos" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {

                                                                                                                                                                                echo 'required=""';

                                                                                                                                                                              } ?>>

                            <div class="form-text"></div>

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



                    <!-- <div class="col-sm-4 mb-3">

                      <label>Order Number</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <input type="number" class="form-control" name="order_by" id="order_by" required="" placeholder="Enter Order Number" value="<?php echo $Row->order_by; ?>">

                        </div>

                      </div>

                    </div> -->

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

                    <div class="col-sm-4 mb-3 ">

                      <!-- <label>Mystery Temples</label> -->

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



                              <select class="form-select mb-3" name="country" id="country">

                                <option selected disabled>Select Country</option>



                                <?php



                                $country_code = 'IN';

                                $Vselect = "SELECT * FROM country WHERE country_code = '$country_code' ORDER BY country_name";



                                // Execute the SQL query and handle errors

                                $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);

                                if (!$VSQL_STATEMENT) {

                                  die("Database query failed: " . mysqli_error($DatabaseCo->dbLink));

                                }



                                // Fetch each row and create an option element for each country

                                while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {

                                  // Check if the current row should be marked as selected

                                  $isSelected = ($VRow->country_code === $Row->country) ? 'selected' : '';

                                  echo "<option value='{$VRow->country_code}' $isSelected>{$VRow->country_name}</option>";

                                }

                                ?>



                              </select>

                            </div>

                            <!-- end /. form group -->

                          </div>

                          <div class="col-sm-6">

                            <!-- start form group -->

                            <div class="">

                              <label class="required fw-medium mb-2">State</label>

                              <select class="form-select mb-3" name="state" id="state">

                                <option selected disabled>Select State</option>

                                <?php

                                // Load states based on the selected country

                                $state = isset($Row->state) ? $Row->state : ''; // Current state code

                                if ($country_code) {

                                  $stateQuery = "SELECT * FROM state WHERE country_code = '$country_code' ORDER BY state_name";

                                  $stateResult = mysqli_query($DatabaseCo->dbLink, $stateQuery);

                                  while ($stateRow = mysqli_fetch_object($stateResult)) {

                                    $selected = ($stateRow->state_code == $state) ? 'selected' : '';

                                    echo "<option value='{$stateRow->state_code}' $selected>{$stateRow->state_name}</option>";

                                  }

                                }

                                ?>

                              </select>







                            </div>

                            <!-- end /. form group -->

                          </div>

                          <div class="col-sm-6">

                            <div>

                              <label class="required fw-medium mb-2">City</label>

                              <select class="form-select mb-3" name="city" id="city">

                                <option selected disabled>Select City</option>

                                <?php

                                // Check if city and state codes are available

                                $currentCity = isset($Row->city) ? $Row->city : ''; // Current city ID

                                $stateCode = isset($Row->state_code) ? $Row->state_code : ''; // Current state code



                                // Only fetch cities if a state code is provided

                                if (!empty($currentCity)) {

                                  $cityQuery = "SELECT * FROM city ORDER BY city_name";

                                  $cityResult = mysqli_query($DatabaseCo->dbLink, $cityQuery);



                                  // Loop through the cities and set the selected option

                                  while ($cityRow = mysqli_fetch_object($cityResult)) {

                                    $selected = ($cityRow->city_id == $currentCity) ? 'selected' : '';

                                    echo "<option value='{$cityRow->city_id}' $selected>{$cityRow->city_name}</option>";

                                  }

                                }

                                ?>

                              </select>

                            </div>

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

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Open Time & End Time</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor6" data-sample-short name="time" rows="5" placeholder="Enter Time"><?php echo $Row->time; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Speciality</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor7" data-sample-short name="speciality" rows="5" placeholder="Enter Speciality"><?php echo $Row->speciality; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Sthalam</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor1" data-sample-short name="sthalam" rows="5" placeholder="Enter Sthalam"><?php echo $Row->sthalam; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Puranam</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor2" name="puranam" rows="5" id="puranam" placeholder="Enter Puranam"><?php echo $Row->puranam; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Varnam</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor3" name="varnam" rows="5" id="varnam" placeholder="Enter Varnam"><?php echo $Row->varnam; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Highlights</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor4" name="highlights" rows="5" id="highlights" placeholder="Enter Highlights"><?php echo $Row->highlights; ?></textarea>

                        </div>

                      </div>

                    </div>

                    <div class="col-sm-6 mb-3">

                      <label>Sevas</label>

                      <div class="field">

                        <div class="control has-icons-left">

                          <textarea class="form-control" id="editor5" name="sevas" rows="5" id="sevas" placeholder="Enter Sevas"><?php echo $Row->sevas; ?></textarea>

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

                        <?php if (empty($Row->gallery_image)) { ?>

                          <div class="gallery">

                            <!-- Images will be dynamically added here -->



                          </div>

                        <?php

                        } else { ?>

                          <?php

                          $existingImages = array_filter(explode(',', $Row->gallery_image)); // Remove empty entries

                          foreach ($existingImages as $image) {

                            $imagePath = "uploads/Temple_gallery/" . htmlspecialchars($image);

                            // Check if the image file exists

                            if (trim($image) !== "" && file_exists($imagePath)) {

                          ?>

                              <div class="image-container" id="image-<?= htmlspecialchars($image); ?>" style="display: inline-block; position: relative; margin: 5px;">

                                <img src="<?= $imagePath; ?>" alt="Existing Image" class="existing-image" style="width: 100px; height: 100px;">

                                <button class="remove" data-index="<?= $Row->index_id; ?>" data-name="<?= htmlspecialchars($image); ?>"

                                  style="position: absolute; top: 0; right: 0; background-color: red; color: white; border: none; cursor: pointer; padding: 3px 5px;">X</button>

                              </div>

                          <?php

                            }

                          }

                          ?>





                        <?php }; ?>

                      </div>

                    </div>



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

<!-- Include jQuery -->





<!-- Include jQuery -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<!-- Include Selectize JS -->







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

        $('#state').prepend('<option selected disabled>Select State</option>'); // Reset with placeholder

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

        $('#city').prepend('<option selected disabled>Select City</option>'); // Reset with placeholder

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

    const gallery = document.querySelector(".gallery");

    gallery.innerHTML = ''; // Clear the gallery before rendering



    selectedFiles.forEach((file, index) => {

      const reader = new FileReader();

      reader.onload = function(e) {

        const imageContainer = document.createElement("div");

        imageContainer.classList.add("image-container");



        const img = document.createElement("img");

        img.src = e.target.result;

        img.alt = file.name;



        const removeBtn = document.createElement("button");

        removeBtn.classList.add("remove-btn");

        removeBtn.textContent = "X";

        removeBtn.onclick = function() {

          removeImage(index); // Call function to remove image by index

        };



        imageContainer.appendChild(img);

        imageContainer.appendChild(removeBtn);

        gallery.appendChild(imageContainer);

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

  $('.remove').click(function() {

    // Get the data attributes to identify the image and gallery row

    var imageIndex = $(this).data('index'); // The row's index_id

    var nameIndex = $(this).data('name'); // The specific image name



    // Send AJAX request to the server to remove the specific image

    $.ajax({

      type: "POST",

      url: "add-temple.php", // Server endpoint to handle image removal

      data: {

        imageIndex: imageIndex,

        nameIndex: nameIndex

      },

      success: function(response) {

        try {

          var result = JSON.parse(response);

          if (result.status === 'success') {

            alert(result.message);

            // Remove only the specific image container from the DOM

            $('div[data-name="' + nameIndex + '"]').remove();

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