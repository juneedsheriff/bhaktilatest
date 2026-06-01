<?php ob_start();

include_once './includes/header.php';

include_once './class/fileUploader.php';

error_reporting(1);



if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];




if ($_REQUEST['index_id'] > 0) {

    $titl = "Update ";

    $select = "SELECT * FROM mantras_title WHERE index_id ='" . $_REQUEST['index_id'] . "'"; //echo $select;

    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    $Row = mysqli_fetch_object($SQL_STATEMENT);

  } else {

    $titl = "";

  }

?>

<div class="body-content">

    <div class="decoration blur-2"></div>

    <div class="decoration blur-3"></div>

    <!-- <div class="font-caveat fs-4 fw-bold fw-medium section-header__subtitle text-capitalize text-center text-primary text-xl-start mb-2">Temple </div> -->

    <div class="row">



        <div class=" mb-4">

      

            <div class="card-body">

                <div class="col-12">

                    <div class="card">

                        <div class="card-body">

                         

                            <p class="card-title-desc">Please fill the required Mantras Group details.</p>

                            <div class="alert" id="validationSummary" style="display:none;"></div>
                            
                            

                            <form method="post" id="priest-form" class="needs-validation" enctype="multipart/form-data">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="form-message" class="mb-3"></div>

        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">Add New Priest</h6>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <!-- Full Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="full_name"><b>Full Name</b></label>
                                <input type="text" name="full_name" class="form-control" id="full_name" placeholder="Enter Full Name" required>
                            </div>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_number"><b>Contact Number</b></label>
                                <input type="text" name="contact_number" class="form-control" id="contact_number" placeholder="Enter Phone Number">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="email"><b>Email</b></label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email">
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="address"><b>Address</b></label>
                                <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address">
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_of_birth"><b>Date of Birth</b></label>
                                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth">
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="gender"><b>Gender</b></label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">-- Select Gender --</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="experience_years"><b>Experience (Years)</b></label>
                                <input type="number" name="experience_years" class="form-control" id="experience_years" placeholder="e.g. 10">
                            </div>
                        </div>

                        <!-- Specialization -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="specialization"><b>Specialization</b></label>
                                <input type="text" name="specialization" class="form-control" id="specialization" placeholder="e.g. Vedic Rituals, Astrology">
                            </div>
                        </div>

                        <!-- Upload Photo -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="photo"><b>Priest Photo</b></label>
                                <input type="file" name="photo" class="form-control-file" id="photo">
                            </div>
                        </div>

                        <!-- Available -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="available"><b>Available?</b></label>
                                <select name="available" id="available" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                    </div> <!-- row -->
                </div> <!-- card-body -->

                <div class="col-sm-12 text-center mt-4">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
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
    $('#priest-form').on('submit', function (e) {
        e.preventDefault();

        var form = $('#priest-form')[0];
        var formData = new FormData(form);

        $('#save').prop('disabled', true).text('Submitting...');

        $.ajax({
            url: 'add-priest-ajax.php', // PHP file to handle insert
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                $('#save').prop('disabled', false).text('Submit');

                if (response.success === true) {
                    $('#form-message').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#priest-form')[0].reset();
                } else {
                    $('#form-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#save').prop('disabled', false).text('Submit');
                $('#form-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
});
</script>