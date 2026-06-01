<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);

if (isset($_REQUEST['submit'])) {
    $title = $xssClean->clean_input($_REQUEST['title']);

    $description = $xssClean->clean_input($_REQUEST['description']);
    $small_description = $xssClean->clean_input($_REQUEST['small_description']);

    $open_time = $xssClean->clean_input($_REQUEST['open_time']);
    $close_time = $xssClean->clean_input($_REQUEST['close_time']);
    $country = $xssClean->clean_input($_REQUEST['country']);
    $state = $xssClean->clean_input($_REQUEST['state']);
    $city = $xssClean->clean_input($_REQUEST['city']);
    $address = $xssClean->clean_input($_REQUEST['address']);
    $god_id = $xssClean->clean_input($_REQUEST['god_id']);
    $log_date = date("Y-m-d", strtotime($xssClean->clean_input($_REQUEST['log_date'])));


    if ($_REQUEST['id'] > 0) {
        $d_id = $_REQUEST['id'];
        $DatabaseCo->dbLink->query("UPDATE `mystery` SET `title`='$title',`description`='$description',`small_description`='$small_description',`open_time`='$open_time',`close_time`='$close_time',`country`='$country',`state`='$state',`city`='$city',`address`='$address',`log_date`='$log_date',`god_id`='$god_id' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
    } else {
        $DatabaseCo->dbLink->query("INSERT INTO `mystery`( `title`, `description`, `small_description`,`open_time`,`close_time`,`country`,`state`,`city`,`address`,`log_date`,`god_id`) VALUES ( '$title','$description', '$small_description','$open_time','$close_time','$country','$state','$city','$address','$log_date','$god_id')") or die(mysqli_error($DatabaseCo->dbLink));
        $d_id = mysqli_insert_id($DatabaseCo->dbLink);
    }

    // Not sure what this is intended for; you can remove if not needed.
    $uploadimage = new ImageUploader($DatabaseCo);
    $upload_image = is_uploaded_file($_FILES['photos']["tmp_name"]) ? $uploadimage->upload($_FILES['photos'], "mystery") : '';

    if ($upload_image != '') {
        $DatabaseCo->dbLink->query("UPDATE `mystery` SET photos='$upload_image' WHERE index_id='$d_id'");
    }

    if ($_REQUEST['edit'] > 0) {
        header("location:temple-mystery-listing.php?alt=1");
    } else {
        header("location:add-mystery-temple.php");
    }

    header("location:temple-mystery-listing.php");
}

if ($_REQUEST['id'] > 0) {
    // $titl = "Update ";
    $select = "SELECT * FROM mystery WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
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
                <h6 class="fs-17 fw-semi-bold mb-0">Basic Informations</h6>
            </div>
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if ($_REQUEST['img']) {
                                echo ' <p class="card-title-desc">Unable to upload photo as the image size is >2mb.</p>';
                            } ?>
                            <p class="card-title-desc">Please fill the required Mystery temples details.</p>
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
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Future Images</label>

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
                                        <div class="col-sm-12 mb-3">
                                            <label>Short Description</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <textarea class="form-control" id="editor1" data-sample-short name="small_description" rows="5" placeholder="Enter Short Description"><?php echo $Row->small_description; ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 mb-3">
                                            <label>Description</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <textarea class="form-control" id="editor2" data-sample-short name="description" rows="5" placeholder="Enter Description"><?php echo $Row->description; ?></textarea>
                                                </div>
                                            </div>
                                        </div>



                                        <div class=" mb-4">
                                            <div class="card-header position-relative">
                                                <h6 class="fs-17 fw-semi-bold mb-0">Location</h6>
                                            </div>
                                            <?php
                                            // Assume these variables hold the previously saved values from the database.
                                            $selected_country = $Row->country ?? ''; // Saved country code
                                            $selected_state = $Row->state ?? '';     // Saved state code
                                            $selected_city = $Row->city ?? '';       // Saved city code
                                            ?>
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
                                $Vselect = "SELECT * FROM country  ORDER BY country_name";

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
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">City</label>
                              <select class="form-select mb-3" name="city" id="city">
                                <option selected disabled>Select City</option>

                                <?php


                                // Check if city and state codes are available
                                $currentCity = isset($Row->city) ? $Row->city : ''; // Current city ID
                                $stateCode = isset($Row->state_code) ? $Row->state_code : ''; // Current state code

                                // Only fetch cities if a state code is provided
                                if (!empty($currentCity)) {
                                  $cityQuery = "SELECT * FROM city  ORDER BY city_name";
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
    $(document).ready(function() {
        // Retrieve preselected values from PHP variables
        let selectedCountry = "<?php echo $selected_country; ?>";
        let selectedState = "<?php echo $selected_state; ?>";
        let selectedCity = "<?php echo $selected_city; ?>";

        // If editing, load states and cities based on preselected values
        if (selectedCountry) {
            loadStates(selectedCountry, selectedState);
        }

        // Event listener for country change
        $('#country').change(function() {
            let countryCode = $(this).val();
            loadStates(countryCode);
        });

        // Event listener for state change
        $('#state').change(function() {
            let stateCode = $(this).val();
            loadCities(stateCode);
        });

        // Function to load states based on selected country
        function loadStates(countryCode, preselectedState = null) {
            $.ajax({
                url: 'get_states.php',
                type: 'POST',
                data: {
                    country_code: countryCode
                },
                success: function(response) {
                    $('#state').html(response);
                    $('#city').html('<option selected disabled>Select City</option>'); // Reset city dropdown
                    if (preselectedState) {
                        $('#state').val(preselectedState).change(); // Select the preloaded state and trigger change
                    }
                }
            });
        }

        // Function to load cities based on selected state
        function loadCities(stateCode, preselectedCity = null) {
            $.ajax({
                url: 'get_cities.php',
                type: 'POST',
                data: {
                    state_code: stateCode
                },
                success: function(response) {
                    $('#city').html(response);
                    if (preselectedCity) {
                        $('#city').val(preselectedCity); // Select the preloaded city
                    }
                }
            });
        }

        // Load cities if there is a selected state and city
        if (selectedState && selectedCity) {
            loadCities(selectedState, selectedCity);
        }
    });
</script>