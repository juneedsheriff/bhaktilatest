<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);



if ($_REQUEST['index_id'] > 0) {
    $titl = "Update ";
    $select = "SELECT * FROM god WHERE index_id ='" . $_REQUEST['index_id'] . "'"; //echo $select;
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
                            <p class="card-title-desc">Please fill the required God details.</p>
                            <div class="alert" id="validationSummary" style="display:none;"></div>

                            <form method="post" id="country-form" class="needs-validation">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="message-container" class="mb-3"></div> <!-- Alert messages -->

        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">God </h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country_name"><b>God Name</b></label>
                                <input type="text" name="god_name" class="form-control" id="god_name"
                                    placeholder="Enter God name" value="<?php echo $Row->god_name; ?>" required>
                            </div>
                        </div>

                       

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="country_code"><b>Sub Name</b></label>
                                <input type="text" name="sub_name" class="form-control" id="sub_name"
                                    placeholder="Enter Sub Name" value="<?php echo $Row->sub_name; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 text-center">
                    <input type="hidden" name="index_id" id="index_id" value="<?php echo $Row->index_id  ?? ''; ?>">
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
    $('#country-form').on('submit', function (e) {
        e.preventDefault(); // Prevent page reload on form submission

        let action = $('#index_id').val() ? 'update' : 'insert'; // Determine action based on country_id

        $.ajax({
            url: 'god_process.php',
            type: 'POST',
            data: {
                action: action,
                index_id: $('#index_id').val(), // Optional for 'update' only
                god_name: $('#god_name').val(),
                sub_name: $('#sub_name').val(),
     
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.success) {
                    showMessage('success', res.message); // Show success message

                    // Redirect to another page after 2 seconds (adjust URL as needed)
                    setTimeout(() => {
                        window.location.href = 'god.php'; // Change to your target page
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