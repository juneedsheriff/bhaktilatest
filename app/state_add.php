<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);



if ($_REQUEST['state_id'] > 0) {
    $titl = "Update ";
    $select = "SELECT * FROM `state` WHERE state_id ='" . $_REQUEST['state_id'] . "'"; //echo $select;
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

        <div class=" mb-4">
        
            <div class="card-body">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_REQUEST['img'])): ?>
                                <p class="card-title-desc">Unable to upload photo as the image size is >2MB.</p>
                            <?php endif; ?>
                            <p class="card-title-desc">Please fill the required state  details.</p>
                            <div class="alert" id="validationSummary" style="display:none;"></div>

                            <form method="post" id="state-form" class="needs-validation">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="message-container" class="mb-3"></div> <!-- Alert messages -->

        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">State</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                    <div class="col-sm-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Country</label>

                              <select class="form-select mb-3" name="country_code" id="country_code" required>
                    <option disabled>Select Country</option>
                    <?php
                    // Fetch countries from the database
                    $Vselect = "SELECT * FROM country ORDER BY country_name";
                    $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);

                    // Loop through each country and mark the selected one
                    while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                        // Combine the country code with the name (e.g., "US - United States")
                        $displayText = "{$VRow->country_name}";

                        // Check if this is the selected country during edit
                        $selected = ($VRow->country_code == $Row->country_code) ? 'selected' : '';
                        echo "<option value='{$VRow->country_code}' $selected>$displayText</option>";
                    }
                    ?>
                </select>
                            </div>
                            <!-- end /. form group -->
                          </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country_name"><b>State Name</b></label>
                                <input type="text" name="state_name" class="form-control" id="state_name"
                                    placeholder="Enter State name" value="<?php echo $Row->state_name; ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="state_code"><b>State Code</b></label>
                                <input type="number" name="state_code" class="form-control" id="state_code"
                                    placeholder="Enter State code" value="<?php echo $Row->state_code; ?>" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label><b>Status</b></label>
                            <div class="radio">
                                <input id="optionsRadios1" class="status" type="radio" value="APPROVED" name="status"
                                    <?php echo ($Row->status === 'APPROVED') ? 'checked' : ''; ?>>
                                <label for="optionsRadios1"><b>Active</b></label>

                                <input id="optionsRadios2" class="country_status" type="radio" value="UNAPPROVED" name="status"
                                    <?php echo ($Row->status === 'UNAPPROVED') ? 'checked' : ''; ?>>
                                <label for="optionsRadios2"><b>Inactive</b></label>
                            </div>
                        </div>

                    
                    </div>
                </div>

                <div class="col-sm-12 text-center">
                    <input type="hidden" name="state_id" id="state_id" value="<?php echo $Row->state_id  ?? ''; ?>">
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
        e.preventDefault(); // Prevent page reload on form submission

        let action = $('#state_id').val() ? 'update' : 'insert'; // Determine action based on country_id

        $.ajax({
            url: 'state_process.php',
            type: 'POST',
            data: {
                action: action,
                country_code: $('#country_code').val(), // Optional for 'update' only
                state_id: $('#state_id').val(), // Optional for 'update' only
                state_name: $('#state_name').val(),
                state_code: $('#state_code').val(),
                status: $('input[name="status"]:checked').val()
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    showMessage('success', res.message); // Show success message

                    // Redirect to another page after 2 seconds (adjust URL as needed)
                    setTimeout(() => {
                        window.location.href = 'state.php'; // Change to your target page
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

    // Function to show messages (success or error)
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