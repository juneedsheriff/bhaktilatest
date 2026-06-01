<?php
include_once './class/databaseConn.php';
include_once './includes/header.php';
include_once 'lib/requestHandler.php';
include_once './class/XssClean.php';
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 1);
error_reporting(1);
// Initialize database connection and XSS cleaner
$DatabaseCo = new DatabaseConn(); // Ensure this class properly connects to the database
$xssClean = new xssClean();

// Fetch existing data to prepopulate the form
$query = "SELECT email, phone_number, address, fb_link, instagram,logo,linkedin FROM business_setting WHERE id = 1";
$result = $DatabaseCo->dbLink->query($query);
$data = $result->fetch_assoc();

?>


<div class="body-content">
    <div class="decoration blur-2"></div>
    <div class="decoration blur-3"></div>
    <div class="row" style="margin-top: 0px;">

    <form action="" method="post" name="add_form" id="add_form" enctype="multipart/form-data">
            <div class="row">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <h4 class="header-title">Settings</h4>
                                <p class="card-title-desc">Business Settings</p>
                            </div>
                
                            <div class="container">
                            <?php if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">';
    echo $_SESSION['success_message'];
    echo '</div>';
    
    // Unset the success message so it doesn't show again on refresh
    unset($_SESSION['success_message']);
}?> 
                                <div class="row">
                                    <div class="col-sm-4 mb-3">
                                        <label>Email</label>
                                        <div class="field">
                                            <div class="control has-icons-left">
                                                <input type="email" class="form-control" name="email" id="email" required placeholder="Enter Email" value="<?php echo ($data['email']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Phone Number</label>
                                        <div class="field">
                                            <div class="control has-icons-left">
                                                <input type="tel" class="form-control" name="phone_number" maxlength="11" id="phone_number" required placeholder="Enter Number" value="<?php echo ($data['phone_number']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Address</label>
                                        <div class="field">
                                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter Address"><?php echo ($data['address']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>FB Link</label>
                                        <div class="field">
                                            <div class="control has-icons-left">
                                                <input type="text" class="form-control" name="fb_link" id="fb_link" placeholder="Enter FB Link" value="<?php echo ($data['fb_link']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Instagram Link</label>
                                        <div class="field">
                                            <div class="control has-icons-left">
                                                <input type="text" class="form-control" name="instagram" id="instagram" placeholder="Enter instagram" value="<?php echo ($data['instagram']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 mb-3">
                                        <label>Linkedin Link</label>
                                        <div class="field">
                                            <div class="control has-icons-left">
                                                <input type="text" class="form-control" name="linkedin" id="linkedin" placeholder="Enter linkedin" value="<?php echo ($data['linkedin']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                <div class="col-12 d-flex justify-content-end ">
                <button type="submit" class="btn btn-primary ml-3">Update</button>
                </div>
            </div>
                        </div>

                       

                    </div>
                </div>
              
            </div>
          
        </form>

    </div>

</div>






<?php
include_once './includes/footer.php';

?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#add_form').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Collect form data
            var formData = {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token, if using Laravel
                email: $('#email').val(),
                phone_number: $('#phone_number').val(),
                address: $('#address').val(),
                fb_link: $('#fb_link').val(),
                instagram: $('#instagram').val(),
                linkedin: $('#linkedin').val()
            };

            console.log("Form Data Submitted:", formData);

            // Send AJAX request
            $.ajax({
                url: 'business_process.php', // Path to your PHP script
                method: 'POST',
                data: formData,
                dataType: 'json', // Expecting a JSON response from the server
                success: function(response) {
                    console.log("Server Response:", response);

                    if (response.data) {
                        // Handle success
                        // alert(response.message); // Show success message

                        // Reload the page to reflect changes
                        window.location.reload(); // This will reload the current page
                    } else {
                        // Handle failure

                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    window.location.reload();
                    // console.log("XHR Response:", xhr.responseText);

                }
            });
        });
    });
</script>