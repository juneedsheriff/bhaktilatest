<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);



if (!empty($_REQUEST['city_id']) && intval($_REQUEST['city_id']) > 0) {
    $titl = "Update ";
    $select = "SELECT * FROM `city` WHERE city_id ='" . $DatabaseCo->dbLink->real_escape_string($_REQUEST['city_id']) . "'";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    $titl = "";
    $Row = (object)['city_id' => '', 'city_name' => '', 'country_id' => '', 'country_code' => '', 'state_code' => '', 'status' => 'UNAPPROVED'];
}
?>
<div class="body-content">
    <div class="decoration blur-2"></div>
    <div class="decoration blur-3"></div>
    <div class="row">

        <div class=" mb-4">
            <!-- <div class="card-header position-relative">
                <h6 class="fs-17 fw-semi-bold mb-0">Basic Informations</h6>
            </div> -->
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_REQUEST['img'])): ?>
                                <p class="card-title-desc">Unable to upload photo as the image size is >2MB.</p>
                            <?php endif; ?>
                            <p class="card-title-desc">Please fill the required City  details.</p>
                            <div class="alert" id="validationSummary" style="display:none;"></div>

                            <form method="post" id="state-form" class="needs-validation">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="message-container" class="mb-3"></div> <!-- Alert messages -->

        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">City</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                    <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Country</label>

                              <select class="form-select mb-3" name="country_id" id="country_id" required>
                    <option value="">Select Country</option>
                    <?php
                    $Vselect = "SELECT * FROM country ORDER BY country_name";
                    $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                    while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                        $displayText = htmlspecialchars($VRow->country_name);
                        $selected = (isset($Row->country_id) && $VRow->country_id == $Row->country_id) ? 'selected' : '';
                        echo "<option value='" . (int)$VRow->country_id . "' $selected>$displayText</option>";
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

                              <select class="form-select mb-3" name="state_code" id="state_code" required>
                    <option value="">Select State</option>
                    <?php
                    $Vselect = "SELECT * FROM `state` ORDER BY state_name";
                    $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                    while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                        $displayText = $VRow->state_name;
                        $selected = (isset($Row->state_code) && $VRow->state_code == $Row->state_code) ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($VRow->state_code) . "' $selected>$displayText</option>";
                    }
                    ?>
                </select>
                            </div>
                            <!-- end /. form group -->
                </div>
             
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country_name"><b>City Name</b></label>
                                <input type="text" name="city_name" class="form-control" id="city_name"
                                    placeholder="Enter State name" value="<?php echo htmlspecialchars($Row->city_name ?? ''); ?>" required>
                            </div>
                        </div>
                     
                        <div class="col-sm-6">
                            <label><b>Status</b></label>
                            <div class="radio">
                                <input id="optionsRadios1" class="status" type="radio" value="APPROVED" name="status"
                                    <?php echo (isset($Row->status) && $Row->status === 'APPROVED') ? 'checked' : ''; ?>>
                                <label for="optionsRadios1"><b>Active</b></label>

                                <input id="optionsRadios2" class="country_status" type="radio" value="UNAPPROVED" name="status"
                                    <?php echo (isset($Row->status) && $Row->status === 'UNAPPROVED') ? 'checked' : ''; ?>>
                                <label for="optionsRadios2"><b>Inactive</b></label>
                            </div>
                        </div>

                    
                    </div>
                </div>

                <div class="col-sm-12 text-center">
                    <input type="hidden" name="city_id" id="city_id" value="<?php echo $Row->city_id  ?? ''; ?>">
                    <button type="submit" class="btn btn-primary" id="save">Submit</button>
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
    <div class="md-overlay"></div>
</div>


<script src="https://cdn.ckeditor.com/4.25.0/standard/ckeditor.js"></script>


<?php
include_once './includes/footer.php';

?>
<script>
$(document).ready(function () {
    $('#state-form').on('submit', function (e) {
        e.preventDefault(); // Prevent page reload

        let action = $('#city_id').val() ? 'update' : 'insert'; // Determine action

        // Prepare data for AJAX call
        let formData = {
            action: action,
            city_name: $('#city_name').val(),
            country_id: $('#country_id').val(),
            state_code: $('#state_code').val(),
            status: $('input[name="status"]:checked').val()
        };

        // Include city_id only for 'update'
        if (action === 'update') {
            formData.city_id = $('#city_id').val();
        }

        $.ajax({
            url: 'city_process.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    showMessage('success', res.message); // Show success message

                    // Redirect to another page after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'city.php'; // Adjust your URL
                    }, 2000);
                } else {
                    showMessage('danger', res.message); // Show error message
                }
            },
            error: function (xhr, status, error) {
                showMessage('danger', 'An error occurred: ' + error);
            }
        });
    });

    // Function to show alert messages
    function showMessage(type, message) {
        let alertBox = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        $('#message-container').html(alertBox);

        // Auto-hide alert after 5 seconds
        setTimeout(() => $('.alert').alert('close'), 5000);
    }
});



</script>