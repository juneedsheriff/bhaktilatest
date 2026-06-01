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



                            <form method="post" id="private-ads-form" class="needs-validation" enctype="multipart/form-data">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="message-container" class="mb-3"></div>

        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">Private Ad Details</h6>
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <!-- Title -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="title"><b>Title</b></label>
                                <input type="text" name="title" class="form-control" id="title"
                                    placeholder="Enter Title" required>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="price"><b>Price</b></label>
                                <input type="number" step="0.01" name="price" class="form-control" id="price"
                                    placeholder="Enter Price">
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="location"><b>Location</b></label>
                                <input type="text" name="location" class="form-control" id="location"
                                    placeholder="Enter Location">
                            </div>
                        </div>

                        <!-- Contact Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_email"><b>Contact Email</b></label>
                                <input type="email" name="contact_email" class="form-control" id="contact_email"
                                    placeholder="Enter Contact Email">
                            </div>
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="contact_phone"><b>Contact Phone</b></label>
                                <input type="text" name="contact_phone" class="form-control" id="contact_phone"
                                    placeholder="Enter Contact Phone">
                            </div>
                        </div>

                        <!-- Duration (Days) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="duration_days"><b>Duration (Days)</b></label>
                                <input type="number" name="duration_days" class="form-control" id="duration_days"
                                    placeholder="30">
                            </div>
                        </div>

                        <!-- Duration (Days) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sort_order"><b>Sort Order</b></label>
                                <input type="number" name="sort_order" class="form-control" id="sort_order"
                                    placeholder="1">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="description"><b>Description</b></label>
                                <textarea name="description" class="form-control" id="description" rows="4"
                                    placeholder="Enter Ad Description"></textarea>
                            </div>
                        </div>

                        

                        <!-- Upload Image -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="image_path"><b>Ad Image</b></label>
                                <input type="file" name="image_path" class="form-control-file" id="image_path">
                            </div>
                        </div>

                        <!-- Is Private -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="is_private"><b>Is Private?</b></label>
                                <select name="is_private" id="is_private" class="form-control">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="is_active"><b>Status</b></label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
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
document.getElementById('private-ads-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);

    fetch('insert_private_ads.php', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            form.reset();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        alert("Something went wrong.");
        console.error(error);
    });
});
</script>