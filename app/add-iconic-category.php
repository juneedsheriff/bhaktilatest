<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(0);

if (isset($_REQUEST['submit'])) {
    $title = $xssClean->clean_input($_REQUEST['title']);
    $description = $xssClean->clean_input($_REQUEST['description']);
    $small_description = $xssClean->clean_input($_REQUEST['small_description']);
    $country = $xssClean->clean_input($_REQUEST['country']);
    $state = $xssClean->clean_input($_REQUEST['state']);
    $city = $xssClean->clean_input($_REQUEST['city']);
    $address = $xssClean->clean_input($_REQUEST['address']);
    $god_id = $xssClean->clean_input($_REQUEST['god_id']);
    $my_stery = $xssClean->clean_input($_REQUEST['my_stery']);
    $order_by = $xssClean->clean_input($_REQUEST['order_by']);
    $time = $xssClean->clean_input($_REQUEST['time']);
    $speciality = $xssClean->clean_input($_REQUEST['speciality']);
    $log_date = date("Y-m-d");
    $status = $xssClean->clean_input($_REQUEST['status']);
    if( $user_role === 'Staff'){
        $status_1='unapproved';
      }elseif($user_role === "Admin"){
        $status_1=$status;
      }else{
      }

    if ($_REQUEST['id'] > 0) {
        $d_id = $_REQUEST['id'];
        $DatabaseCo->dbLink->query("UPDATE `iconic` SET `title`='$title',`description`='$description',`small_description`='$small_description',`country`='$country',`state`='$state',`city`='$city',`address`='$address',`log_date`='$log_date',`god_id`='$god_id',`my_stery`='$my_stery',`order_by`='$order_by',`speciality`='$speciality',`time`='$time',`status`='$status_1' WHERE `index_id`='$d_id'") or die(mysqli_error($DatabaseCo->dbLink));
    } else {
        $DatabaseCo->dbLink->query("INSERT INTO `iconic`( `title`, `description`, `small_description`,`country`,`state`,`city`,`address`,`log_date`,`god_id`,`my_stery`,`order_by`,`speciality`,`time`,`status`) VALUES ( '$title','$description', '$small_description','$country','$state','$city','$address','$log_date','$god_id','$my_stery','$order_by','$speciality','$time','$status_1')") or die(mysqli_error($DatabaseCo->dbLink));
        $d_id = mysqli_insert_id($DatabaseCo->dbLink);
    }
    
    $uploadimage = new ImageUploader($DatabaseCo);
    $upload_image_photos = '';
    $upload_image_banner = '';
    
    if (is_uploaded_file($_FILES['photos']["tmp_name"])) {
        $upload_image_photos = $uploadimage->upload($_FILES['photos'], "iconic");
    }
    
    if (is_uploaded_file($_FILES['banner']["tmp_name"])) {
        $upload_image_banner = $uploadimage->upload($_FILES['banner'], "iconic/banner");
    }
    
    if (is_uploaded_file($_FILES['mapImage']["tmp_name"])) {
        $upload_image_map = $uploadimage->upload($_FILES['mapImage'], "iconic/map");
    }
    if ($upload_image_photos != '') {
        $DatabaseCo->dbLink->query("UPDATE `iconic` SET photos='$upload_image_photos' WHERE index_id='$d_id'");
    }
    if ($upload_image_banner != '') {
        $DatabaseCo->dbLink->query("UPDATE `iconic` SET banner='$upload_image_banner' WHERE index_id='$d_id'");
    }
    if ($upload_image_map != '') {
        $DatabaseCo->dbLink->query("UPDATE `iconic` SET map_image='$upload_image_map' WHERE index_id='$d_id'");
    }
    if ($_REQUEST['edit'] > 0) {
        header("location:iconic_category_list.php?alt=1");
    } else {
        header("location:add-iconic-temple.php");
    }

    header("location:temple-iconic-category-listing.php");
}

if ($_REQUEST['id'] > 0) {
    // $titl = "Update ";
    $select = "SELECT * FROM iconic WHERE index_id='" . $_REQUEST['id'] . "'"; //echo $select;
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    $titl = "";
}
?>
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
    <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Iconic Homepage </div>
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
                                            <label>Order Number</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <input type="number" class="form-control" name="order_by" id="order_by" required="" placeholder="Enter Order Number" value="<?php echo $Row->order_by; ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Featured Image</label>
                                            <?php if (!empty($_REQUEST['id']) && isset($Row) && !empty($Row->photos)): ?>
                                            <div class="mb-2">
                                                <span class="d-block small text-muted mb-1">Current image:</span>
                                                <a href="./uploads/iconic/<?php echo htmlspecialchars($Row->photos); ?>"  class="d-inline-block">
                                                    <img src="./uploads/iconic/<?php echo htmlspecialchars($Row->photos); ?>" alt="Featured" class="img-thumbnail" style="max-width:120px;max-height:120px;object-fit:contain;">
                                                </a>
                                                <p class="small text-muted mb-0 mt-1">Choose a new file below to replace.</p>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
                                                    <div class="custom-file">
                                                        <input class="fileUp fileup-sm uploadlink" type="file" name="photos" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") { echo 'required=""';} ?>>
                                                        <label class="custom-file-label" for="photos" style="font-size: 13px;">Recommended to 250 x 250 px (png, jpg, jpeg).</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 mb-3">
                                            <label>Banner Image </label>
                                            <?php if (!empty($_REQUEST['id']) && isset($Row) && !empty($Row->banner)): ?>
                                            <div class="mb-2">
                                                <span class="d-block small text-muted mb-1">Current image:</span>
                                                <a href="./uploads/iconic/banner/<?php echo htmlspecialchars($Row->banner); ?>"  class="d-inline-block">
                                                    <img src="./uploads/iconic/banner/<?php echo htmlspecialchars($Row->banner); ?>" alt="Banner" class="img-thumbnail" style="max-width:120px;max-height:120px;object-fit:contain;">
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
                                                    <label class="form-check-label" for="Mystery"> Is Mystery Temple</label>
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
                                                    <div class="col-sm-4">
                                                        <!-- start form group -->
                                                        <div class="">
                                                            <label class="required fw-medium mb-2">Country</label>

                                                            <select class="form-select mb-3" name="country" id="country">
                                                                <option selected disabled>Select Country</option>

                                                                <?php
                                                                // Use saved country when editing, else default to IN
                                                                $country_code = (isset($Row) && isset($Row->country) && $Row->country !== '') ? $Row->country : 'IN';
                                                                $Vselect = "SELECT * FROM country ORDER BY country_name";

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
                                                    <div class="col-sm-4">
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
                                                    <div class="col-sm-4">
                                                        <!-- start form group -->
                                                        <div class="">
                                                            <label class="required fw-medium mb-2">City</label>
                                                            <select class="form-select mb-3" name="city" id="city">
                                                                <option selected disabled>Select City</option>

                                                                <?php


                                                                // Check if city and state are available (iconic table uses state, not state_code)
                                                                $currentCity = (isset($Row) && isset($Row->city)) ? $Row->city : ''; // Current city ID
                                                                $stateForCity = (isset($Row) && isset($Row->state)) ? $Row->state : ''; // State code for filtering cities

                                                                // When editing, load cities (filter by state if we have it so list is relevant)
                                                                if ((!empty($currentCity) || !empty($stateForCity)) && isset($Row)) {
                                                                    $cityWhere = !empty($stateForCity) ? " WHERE state_code = '" . $DatabaseCo->dbLink->real_escape_string($stateForCity) . "'" : '';
                                                                    $cityQuery = "SELECT * FROM city" . $cityWhere . " ORDER BY city_name";
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
<div class="col-sm-6">
                                            <label>Upload Location Map </label>

                                            <div class="col-sm-6 mb-3">
                                                <div class="field">
                                                    <!-- <label class="pull-left">image </label> -->
                                                    <div class="custom-file">
                                                        <input class="fileUp fileup-sm uploadlink" type="file" name="mapImage" id="mapImage" accept=".jpg, .png, image/jpeg, image/png" multiple="" value="" <?php if ($titl == "Add New ") {
                                                                                                                                                                                                                echo 'required=""';
                                                                                                                                                                                                            } ?>>
                                                        <label class="custom-file-label" for="mapImage"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                            <?php
                                            $selected_country = (isset($Row) && $Row) ? ($Row->country ?? '') : '';
                                            $selected_state  = (isset($Row) && $Row) ? ($Row->state ?? '') : '';
                                            $selected_city   = (isset($Row) && $Row) ? ($Row->city ?? '') : '';
                                            ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-header position-relative">
                                                <h6 class="fs-17 fw-semi-bold mb-0">Temple Details</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                        <div class="col-sm-12 mb-3">
                                            <label>Temple Subheading</label>
                                            <div class="field">
                                                <div class="control has-icons-left">
                                                    <textarea class="form-control" id="editor7" data-sample-short name="speciality" rows="5" placeholder="Enter Speciality"><?php echo $Row->speciality; ?></textarea>
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
                                            </div>
                                        </div>
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
    $(document).ready(function() {
        // Retrieve preselected values from PHP variables
        let selectedCountry = "<?php echo $selected_country; ?>";
        let selectedState = "<?php echo $selected_state; ?>";
        let selectedCity = "<?php echo $selected_city; ?>";

        // If editing, load states and cities based on preselected values (pass city so it is retained)
        if (selectedCountry) {
            loadStates(selectedCountry, selectedState, selectedCity);
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

        // Function to load states based on selected country; optional preselected state/city for edit
        function loadStates(countryCode, preselectedState = null, preselectedCity = null) {
            $.ajax({
                url: 'get_states.php',
                type: 'POST',
                data: {
                    country_code: countryCode
                },
                success: function(response) {
                    $('#state').html(response);
                    if (preselectedState) {
                        $('#state').val(preselectedState);
                        // Load cities for this state and restore selected city (do not reset city first)
                        loadCities(preselectedState, preselectedCity);
                    } else {
                        $('#city').html('<option selected disabled>Select City</option>');
                    }
                }
            });
        }

        // Function to load cities based on selected state; optional preselected city for edit
        function loadCities(stateCode, preselectedCity = null) {
            if (!stateCode) {
                $('#city').html('<option value="">Select City</option>');
                return;
            }
            $.ajax({
                url: 'get_cities.php',
                type: 'POST',
                data: {
                    state_code: stateCode
                },
                success: function(response) {
                    $('#city').html(response);
                    if (preselectedCity) {
                        $('#city').val(preselectedCity).trigger('change');
                    }
                }
            });
        }
    });
</script>
<script>
    CKEDITOR.replace('editor6');
</script>
<script>
    CKEDITOR.replace('editor7');
</script>