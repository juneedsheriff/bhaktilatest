<?php ob_start();
include_once './includes/header.php';
include_once './class/fileUploader.php';
error_reporting(1);



if ($_REQUEST['index_id'] > 0) {
    $titl = "Update ";
    $select = "SELECT * FROM gallery WHERE index_id ='" . $_REQUEST['index_id'] . "'"; //echo $select;
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
                            <p class="card-title-desc">Please fill the required gallery details.</p>
                            <div class="alert" id="validationSummary" style="display:none;"></div>

                            <form method="post" id="gallery-form" class="needs-validation" enctype="multipart/form-data">
    <div id="basic-layout" class="pt-30 pl-30 pr-30 pb-30">
        <div id="message-container" class="mb-3"></div> <!-- Alert messages -->

        <div class="row">
            <div class="mb-4">
                <div class="card-header position-relative">
                    <h6 class="fs-17 fw-semi-bold mb-0">Gallery</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="required fw-medium mb-2">Gallery</label>
                                <input class="upload fileUp fileup-sm uploadlink" type="file" name="photos[]" id="photos" accept=".jpg, .png, image/jpeg, image/png" multiple >
                                <div class="form-text">Recommended size: 350 x 350 px (png, jpg, jpeg).</div>
                            </div>
                            <?php if(empty($Row->photos)){?>
                        <div class="gallery">
        <!-- Images will be dynamically added here -->
      
    </div>
    <?php
  }else { ?>
     <?php 
$existingImages = array_filter(explode(',', $Row->photos)); // Remove empty entries
foreach ($existingImages as $image) {
    $imagePath = "uploads/gallery/" . htmlspecialchars($image);
    // Check if the image file exists
    if (trim($image) !== "" && file_exists($imagePath)) { 
?>
    <div class="image-container" id="image-<?= htmlspecialchars($image); ?>" style="display: inline-block; position: relative; margin: 5px;">
        <img src="<?= $imagePath; ?>" alt="Existing Image" class="existing-image" style="width: 100px; height: 100px;">
        <button class="remove" data-index="<?= $Row->index_id; ?>" data-name="<?= htmlspecialchars($image); ?>" 
                style="position: absolute; top: 0; right: 0; background-color: red; color: white; border: none; cursor: pointer; padding: 3px 5px;">X</button>
    </div>
<?php 
    }
}
?>

    <?php };?>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="title"><b>Title</b></label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title" value="<?php echo $Row->title ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 text-center">
                    <input type="hidden" name="index_id" id="index_id" value="<?php echo $Row->index_id ?? ''; ?>">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#gallery-form').on('submit', function (e) {
        e.preventDefault(); // Prevent page reload on form submission

        // Determine action based on whether `index_id` has a value
        let action = $('#index_id').val() ? 'update' : 'insert';

        // Create FormData object to handle file upload and form data
        let formData = new FormData(this);
        formData.append('action', action);

        $.ajax({
            url: 'gallery_process.php',
            type: 'POST',
            data: formData,
            contentType: false, // Needed to handle file upload
            processData: false, // Needed to handle file upload
            success: function (response) {
                try {
                    let res = JSON.parse(response);
                    if (res.success) {
                        showMessage('success', res.message); // Show success message

                        // Redirect to another page after 2 seconds (adjust URL as needed)
                        setTimeout(() => {
                            window.location.href = 'gallery_list.php'; // Change to your target page
                        }, 2000);
                    } else {
                        showMessage('danger', res.message); // Show error message
                    }
                } catch (e) {
                    // showMessage('danger', 'Invalid response from server.');
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
<script>
$('.remove').click(function() {
  // Get the data attributes to identify the image and gallery row
  var imageIndex = $(this).data('index'); // The row's index_id
  var nameIndex = $(this).data('name'); // The specific image name

  // Send AJAX request to the server to remove the specific image using POST
  $.ajax({
    type: "POST",
    url: "remove_gallery.php", // Server endpoint to handle image removal
    data: {
      imageIndex: imageIndex,
      nameIndex: nameIndex
    },
    success: function(response) {
      try {
        var result = JSON.parse(response);
        if (result.status === 'success') {
        //   alert(result.message);  // Display success message

          // Reload the current page to reflect changes
          window.location.reload();
        } else {
          alert(result.message);  // Display failure message
        }
      } catch (e) {
        console.log("Parsing Error:", e);
        window.location.reload();
      }
    },
    error: function(error) {
      console.log("Error removing image:", error);
      window.location.reload();
    }
  });
});

;
</script>