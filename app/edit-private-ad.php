<?php ob_start();

include_once './includes/header.php';

include_once './class/fileUploader.php';

error_reporting(1);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$Row = null;

if ($id > 0) {
    $stmt = $DatabaseCo->dbLink->prepare("SELECT * FROM private_ads WHERE id = ?");
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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

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



                           <!-- HTML FORM with PHP Pre-filled Fields -->
<form method="post" id="private-ads-form" class="needs-validation" enctype="multipart/form-data" action="update_private_ads.php">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div class="row is-multiline">
            <div class="mb-4">
                <div class="card-header">
                    <h6 class="fs-17 fw-semi-bold mb-0">Edit Private Ad</h6>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- Title -->
                        <div class="col-sm-6">
                            <label><b>Title</b></label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($Row['title']) ?>" required>
                        </div>

                        <!-- Price -->
                        <div class="col-sm-6">
                            <label><b>Price</b></label>
                            <input type="number" step="0.01" name="price" class="form-control" value="<?= $Row['price'] ?>">
                        </div>

                        <!-- Location -->
                        <div class="col-sm-6">
                            <label><b>Location</b></label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($Row['location']) ?>">
                        </div>

                        <!-- Contact Email -->
                        <div class="col-sm-6">
                            <label><b>Contact Email</b></label>
                            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($Row['contact_email']) ?>">
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-sm-6">
                            <label><b>Contact Phone</b></label>
                            <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($Row['contact_phone']) ?>">
                        </div>

                        <!-- Duration -->
                        <div class="col-sm-6">
                            <label><b>Duration (Days)</b></label>
                            <input type="number" name="duration_days" class="form-control" placeholder="30" value="<?= htmlspecialchars($Row['duration_days']) ?>">
                        </div>

                         <!-- Duration (Days) -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="sort_order"><b>Sort Order</b></label>
                                <input type="number" name="sort_order" class="form-control" id="sort_order"
                                    placeholder="1" value="<?= htmlspecialchars($Row['sort_order']) ?>">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-sm-12">
                            <label><b>Description</b></label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($Row['description']) ?></textarea>
                        </div>

                        <!-- Ad Image -->
                        <div class="col-sm-6">
                            <label><b>Ad Image</b></label>
                            <input type="file" name="image_path" class="form-control-file">
                            <?php if ($Row['image_path']): ?>
                                <div class="mt-2">
                                    <img src="<?= $Row['image_path'] ?>" width="100" alt="Current Image">
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Is Private -->
                        <div class="col-sm-3">
                            <label><b>Is Private?</b></label>
                            <select name="is_private" class="form-control">
                                <option value="0" <?= $Row['is_private'] == 0 ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= $Row['is_private'] == 1 ? 'selected' : '' ?>>Yes</option>
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
                    <button type="submit" class="btn btn-primary">Update Ad</button>
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
            //form.reset();
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