<?php ob_start();
include_once './includes/header.php';
include_once './includes/city_import.php';

$cityImportSummary = null;
if (isset($_POST['import_cities_csv']) && !empty($_FILES['city_csv']['tmp_name'])) {
    $ext = strtolower(pathinfo($_FILES['city_csv']['name'] ?? '', PATHINFO_EXTENSION));
    if ($ext === 'csv' && is_uploaded_file($_FILES['city_csv']['tmp_name'])) {
        $cityImportSummary = city_import_from_file($DatabaseCo->dbLink, $_FILES['city_csv']['tmp_name'], false);
    } else {
        $cityImportSummary = ['errors' => 1, 'messages' => ['Please upload a valid CSV file.']];
    }
}

if (!empty($_REQUEST['state_code'])) {
    $state = $DatabaseCo->dbLink->real_escape_string($_REQUEST['state_code']);
} else {
    $state = '';
}
error_reporting(1);
if (isset($_POST['delete_now']) && !empty($_POST['del_c'])) {
    $del_id = intval($_POST['del_c']); // Sanitize input to avoid SQL injection

    $query = "DELETE FROM `city` WHERE `city_id` = ?";
    $stmt = $DatabaseCo->dbLink->prepare($query);
    $stmt->bind_param("i", $del_id);

    if ($stmt->execute()) {
        echo "<script>
              
                window.location.href = 'city.php';
              </script>";
    } else {
        echo "<script>alert('Error deleting record: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $DatabaseCo->dbLink->close();
}
// Check if delete ID is available in the request

?>



<style>
    .form-select{
        width: 60% !important;
    }
</style>

<!-- Page-Title -->
<div class="card-header position-relative">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="fs-17 fw-semi-bold my-1">Cites</h6>
            <!-- <p class="mb-0">Countrys Listing.</p> -->

        </div>
       <!-- Dropdown Menu -->
    <div class="dropdown">
       
       <div class="form-group">
   <label for="countryDropdown" class="fw-semi-bold">Select State</label>
   <select class="form-select" id="countryDropdown">
   <option value="">Select state</option>
   <?php
   $select = "SELECT * FROM `state` ORDER BY state_name ASC";
   $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
   if (mysqli_num_rows($SQL_STATEMENT) != 0) {
       while ($Row = mysqli_fetch_object($SQL_STATEMENT)) { ?>
           <option value="<?php echo htmlspecialchars($Row->state_code); ?>"<?php echo $Row->state_code==$state?'selected':'';?>>
               <?php echo htmlspecialchars($Row->state_name); ?>
           </option>
       <?php }
   } ?>
</select>
</div>
   </div>
        <div class="text-end d-flex flex-wrap gap-2 justify-content-end">
            <a href="city_add.php" class="btn btn-primary fw-medium"><i class="fa-solid fa-plus me-1"></i>Add New City</a>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<?php if ($cityImportSummary !== null) {
    $ok = (int) ($cityImportSummary['errors'] ?? 0) === 0;
    $cls = $ok ? 'alert-success' : 'alert-danger';
    ?>
<div class="alert <?php echo $cls; ?> mb-3">
    <?php if ($ok) { ?>
        City import finished (<?php echo htmlspecialchars((string) ($cityImportSummary['format'] ?? 'csv'), ENT_QUOTES, 'UTF-8'); ?> format).
        CSV rows: <?php echo (int) ($cityImportSummary['csv_rows'] ?? 0); ?>.
        New: <?php echo (int) ($cityImportSummary['imported'] ?? 0); ?>,
        updated: <?php echo (int) ($cityImportSummary['updated'] ?? 0); ?>,
        skipped: <?php echo (int) ($cityImportSummary['skipped'] ?? 0); ?><?php if (!empty($cityImportSummary['removed'])) { ?>,
        removed: <?php echo (int) $cityImportSummary['removed']; ?><?php } ?>.
        Total in database: <?php echo number_format((int) ($cityImportSummary['db_total'] ?? 0)); ?>.
    <?php } else { ?>
        City import failed.
        <?php foreach ($cityImportSummary['messages'] ?? [] as $msg) {
            echo '<div>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
        } ?>
    <?php } ?>
</div>
<?php } ?>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semi-bold mb-2">Import cities from CSV</h6>
        <p class="text-muted small mb-2">
            Global: <code>city_id,city_name</code> (e.g. <code>cityResults.csv</code>).<br>
            India legacy: <code>city_id,city_name,NULL,legacy_state_id</code> (e.g. <code>indiacityResults.csv</code>).<br>
            Temple export: <code>indiacitys.csv</code> — columns: id, <strong>state</strong>, god, <strong>city</strong>, title, … (duplicate city+state rows are merged; existing cities are replaced).
        </p>
        <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
            <div class="col-md-8">
                <input type="file" name="city_csv" class="form-control" accept=".csv" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="import_cities_csv" value="1" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-upload me-1"></i>Import CSV
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive wrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th class="w-5">Sno</th>
                            <th class="w-5">City Id</th>
                            <th class="w-15">State Code</th>
                            <th class="w-5">Country ID</th>
                            <th class="w-5">Country Code</th>
                            <th class="w-25">City Name</th>
                            <th class="w-20">Status</th>
                            <th class="w-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  // Example: State ID to filter cities, change as needed

                        $select = "SELECT * FROM `city` 
           WHERE city_id != '0' " . ($state !== '' ? "AND state_code = '$state'" : "") . " 
           ORDER BY city_id DESC";

                        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);
                        $num_rows = mysqli_num_rows($SQL_STATEMENT);
                        if ($num_rows != 0) {
                            $i = 1;
                            while ($Row = mysqli_fetch_object($SQL_STATEMENT)) {
                                $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT state_name FROM `state` WHERE state_code='" . $DatabaseCo->dbLink->real_escape_string($Row->state_code) . "'");
                                $res3 = mysqli_fetch_object($sql3);
                        ?>
                                <tr>
                                    <td><?php echo $i;
                                        $i++; ?></td>

                                    <td><?php echo $Row->city_id; ?></td>
                                    <td><?php echo htmlspecialchars($Row->state_code); ?></td>
                                    <td><?php echo (int)($Row->country_id ?? 0); ?></td>
                                    <td><?php echo htmlspecialchars($Row->country_code ?? ''); ?></td>
                                    <td><?php echo $Row->city_name; ?></td>
                                    <td><?php echo $Row->status; ?></td>

                                    <td>
                                        <a class="btn btn-sm p-2 btn-primary text-white  edit-board alert-box-trigger waves-effect waves-light kill-drop" href="city_add.php?city_id=<?php echo $Row->city_id; ?>"><i class="fas fa-pencil-alt"></i></a> &nbsp; &nbsp;
                                        <a class="btn btn-sm p-2 btn-danger delete-board waves-effect waves-light text-white"
                                            data-toggle="modal"
                                            data-target="#delete-board-alert"
                                            data-id="<?php echo $Row->city_id; ?>"
                                            id="delete-board<?php echo $Row->city_id; ?>">
                                            <i class="fa fa-trash text-white"></i>
                                        </a>

                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="9">
                                    <div align="center"><strong>No Records!</strong></div>
                                </td>
                            </tr>
                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- end row -->
<div id="delete-board-alert" class="modal fade alert-box">
    <form method="POST" id="delete_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="header-title">Delete City</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h5 class="text-center">Delete City Details?</h5>
                    <p>Are you sure you want to delete this City? All related data will be lost.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="del_c" id="delid" value="" />
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button name="delete_now" type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </form>
</div>



<?php
include_once './includes/footer.php';
?>

<script type="text/javascript">
    // $("#add_form").submit(function(event) {
    //     event.preventDefault();
    //     var post_url = $(this).attr("action");
    //     var request_method = $(this).attr("method");
    //     var form_data = $("#add_form").serialize();
    //     //alert(form_data);
    //     $.ajax({
    //         url: post_url,
    //         type: request_method,
    //         dataType: "text",
    //         data: form_data
    //     }).done(function(response) {
    //         console.log(response);
    //         //window.location.reload();
    //     });
    // });
    $("#add_form").submit(function(event) {
        event.preventDefault();
        var post_url = $(this).attr("action");
        var request_method = $(this).attr("method");
        var form = $('#add_form')[0];
        var data = new FormData(form);
        //alert(data);
        $.ajax({
            type: "POST",
            url: "packages-process.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(data) {
                var newPatient = $.trim(data);
                //console.log(newPatient);
                // $("#new_patient_id").val(newPatient);
                // $("#hideDate").hide();             
                // $("#hideTrouble").hide();              
                // $('#patient_details').modal('hide');
                // $('#appointment_add').modal('show');
                //   window.location.href="billing.php?type=1";
                window.location.reload();
            },
            error: function(event) {
                console.log("ERROR : ", event);
                window.location.reload();
            }
        });
    })
    $('.drop-edit-board').click(function() {
        var id = $(this).data('id');
        $("#pget_id").val(id);
        $("#vcategory").hide();
        var dataString = 'TourAddedit=' + id;
        $("#hidden_id").val(id);
        $.ajax({
            url: "packages-process.php",
            type: "POST",
            dataType: "text",
            data: dataString
        }).done(function(html) { //alert(html);
            var arr = html.split("|");
            $("#package_name").val(arr[0]);
            $("#package_price").val(arr[1]);
            $("#number_of_nights").val(arr[2]);
            $("#others_details").val(arr[3]);
            $("#number_of_days").val(arr[4]);

        });

    });
</script>

<script>
    $(document).on('click', '.delete-board', function() {
        var countryId = $(this).data('id'); // Get the data-id from the button
        $('#delid').val(countryId); // Assign it to the hidden input field
    });
</script>
<script>
  $("#countryDropdown").on('change', function () {
    const selectedValue = this.value;
    console.log(selectedValue);
    if (selectedValue) {
      // Use correct string interpolation for the URL
      window.location.href = `city.php?state_code=${encodeURIComponent(selectedValue)}`;
    }
  });
</script>