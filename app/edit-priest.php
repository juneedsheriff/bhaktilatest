<?php ob_start();

include_once './includes/header.php';

include_once './class/fileUploader.php';

error_reporting(1);



if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];




$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$Row = null;

if ($id > 0) {
    $stmt = $DatabaseCo->dbLink->prepare("SELECT * FROM priests WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $Row = $result->fetch_assoc();
    $stmt->close();

    if (!$Row) {
        echo "<div style='color: red;'>Ad not found.</div>";
        exit;
    }
} else {
    echo "<div style='color: red;'>Invalid ID.</div>";
    exit;
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
                            
                            
<form method="post" id="priest-update-form" class="needs-validation" enctype="multipart/form-data" action="update_priest.php">

    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header">
                    <h6 class="fs-17 fw-semi-bold mb-0">Edit Priest</h6>
                </div>
                <div id="form-message"></div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-sm-6">
                            <label><b>Full Name</b></label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($Row['full_name']) ?>" required>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-sm-6">
                            <label><b>Contact Number</b></label>
                            <input type="text" name="contact_number" class="form-control" value="<?= htmlspecialchars($Row['contact_number']) ?>">
                        </div>

                        <!-- Email -->
                        <div class="col-sm-6">
                            <label><b>Email</b></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($Row['email']) ?>">
                        </div>

                        <!-- Address -->
                        <div class="col-sm-6">
                            <label><b>Address</b></label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($Row['address']) ?>">
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-sm-6">
                            <label><b>Date of Birth</b></label>
                            <input type="date" name="date_of_birth" class="form-control" value="<?= htmlspecialchars($Row['date_of_birth']) ?>">
                        </div>

                        <!-- Gender -->
                        <div class="col-sm-6">
                            <label><b>Gender</b></label>
                            <select name="gender" class="form-control">
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?= $Row['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $Row['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= $Row['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <!-- Experience -->
                        <div class="col-sm-6">
                            <label><b>Experience (Years)</b></label>
                            <input type="number" name="experience_years" class="form-control" value="<?= htmlspecialchars($Row['experience_years']) ?>">
                        </div>

                        <!-- Specialization -->
                        <div class="col-sm-6">
                            <label><b>Specialization</b></label>
                            <input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($Row['specialization']) ?>">
                        </div>

                        <!-- Upload Photo -->
                        <div class="col-sm-6">
                            <label><b>Priest Photo</b></label>
                            <input type="file" name="photo" class="form-control-file">
                            <?php if (!empty($Row['photo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($Row['photo']) ?>" width="100" alt="Current Photo">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Available -->
                        <div class="col-sm-3">
                            <label><b>Available?</b></label>
                            <select name="available" class="form-control">
                                <option value="1" <?= $Row['available'] == 1 ? 'selected' : '' ?>>Yes</option>
                                <option value="0" <?= $Row['available'] == 0 ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-sm-3">
                            <label><b>Status</b></label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?= $Row['is_active'] == 1 ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= $Row['is_active'] == 0 ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 text-center mt-4">
                    <input type="hidden" name="id" value="<?= $Row['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <button type="submit" class="btn btn-primary" id="save">Update Priest</button>
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
    $('#priest-update-form').on('submit', function (e) {
        e.preventDefault();

        var form = $('#priest-update-form')[0];
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
                $('#save').prop('disabled', false).text('Update');

                if (response.success === true) {
                    $('#form-message').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#priest-form')[0].reset();
                } else {
                    $('#form-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#save').prop('disabled', false).text('Update');
                $('#form-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
});
</script>