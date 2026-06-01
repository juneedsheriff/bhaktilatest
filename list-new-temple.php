<?php
include('./include/header.php');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
error_reporting(1);
?>
<style type="text/css">
	.form-list-temple{
		max-width: 800px;
		margin: 0 auto;
	}
	.list-new-temple{
		padding: 30px 0;
	}
	.select2-container{
		max-width: 100%;
	}
	.card-header {
padding: 1.25rem 1.5rem;
background-color: #fff;
border-bottom: 1px solid #eff2f7;
}
.main-title::after, .card-header::after, .modal-title::after {
    content: "";
    position: absolute;
    height: 32px;
    width: 6px;
    background-color: #ff8776;
    left: 0;
    top: 50%;
    -webkit-transform: translate(0, -50%);
    -ms-transform: translate(0, -50%);
    transform: translate(0, -50%);
    border-top-right-radius: 3px;
    border-bottom-right-radius: 3px;
}
</style>
<section class="list-new-temple">
	<div class="container mt-5">
  <h2 class="text-center mb-4">Add New Temple</h2>
  <form action="/submit-temple" method="POST" id="form-list-temple" class="form-list-temple" enctype="multipart/form-data">
    <div class="row">
      <!-- Temple Name -->
      <div class="col-md-6 mb-3">
        <label for="templeName" class="form-label">Temple Name</label>
        <input type="text" class="form-control" id="templeName" name="templeName" required>
      </div>

      <!-- Deity Name -->
      <div class="col-md-6 mb-3">
        <label for="deityName" class="form-label">Deity Name</label>
        <input type="text" class="form-control" id="deityName" name="deityName" required>
      </div>

     

      <!-- Contact Number -->
      <div class="col-md-6 mb-3">
        <label for="phone" class="form-label">Contact Number</label>
        <input type="tel" class="form-control" id="phone" name="phone">
      </div>

      <!-- Email -->
      <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Contact Email</label>
        <input type="email" class="form-control" id="email" name="email">
      </div>

      <!-- Timings -->
      <div class="col-md-6 mb-3">
        <label for="timings" class="form-label">Temple Timings</label>
        <input type="text" class="form-control" id="timings" name="timings" placeholder="e.g., 6 AM to 8 PM">
      </div>

      <!-- Established Year -->
      <div class="col-md-6 mb-3">
        <label for="year" class="form-label">Established Year</label>
        <input type="number" class="form-control" id="year" name="year" min="1000" max="2099" placeholder="e.g., 1850">
      </div>

      <div class="col-md-6 mb-3 ">
	      <label> </label>
	      <div class="form-check">
	        <div class="form-check mb-2">
	          <!-- Checkbox with ternary operator for checked status -->
	          <input type="hidden" name="my_stery" value="0">
	          <input class="form-check-input" type="checkbox" value="1" name="my_stery" id="Mystery">
	          <label class="form-check-label" for="Mystery">Is Mystery Temple</label>
	        </div>
	      </div>
	  </div>

	  <div class=" mb-4">
                      <div class="card-header position-relative">
                        <h6 class="fs-17 fw-semi-bold mb-0">Location</h6>
                      </div>
                      <div class="card-body">
                        <div class="row g-4">
                          <div class="col-md-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Country</label>
                              
                              <select class="form-select mb-3" name="country" id="country">
                                <option selected="" disabled="">Select Country</option>
                                <?php

                                $country_code = 'IN';
                                $Vselect = "SELECT * FROM country ORDER BY country_name";

                                // Execute the SQL query and handle errors
                                $VSQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $Vselect);
                                if (!$VSQL_STATEMENT) {
                                  die("Database query failed: " . mysqli_error($DatabaseCo->dbLink));
                                }

                                // Fetch each row and create an option element for each country
                                while ($VRow = mysqli_fetch_object($VSQL_STATEMENT)) {
                                  // Check if the current row should be marked as selected
                                  $isSelected = ($VRow->country_code === $Row->country) ? 'selected' : '';
                                  echo "<option value='{$VRow->country_code}' $isSelected>{$VRow->country_name}</option>";
                                }
                                ?>
                              </select>
                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-md-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">State</label>
                              
                              <select class="form-select mb-3" name="state" id="state">
                                <option selected="" disabled="">Select State</option>
                              </select>





                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-md-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">City</label>
                              
                              <select class="form-select mb-3" name="city" id="city">
                                <option selected="" disabled="" data-select2-id="2">Select City</option>
                              </select>
                            </div>
                            <!-- end /. form group -->
                          </div>
                          <div class="col-md-6">
                            <!-- start form group -->
                            <div class="">
                              <label class="required fw-medium mb-2">Address</label>
                              <input type="text" class="form-control" name="address" placeholder="Enter Address" required="" value="">
                            </div>
                            <!-- end /. form group -->
                          </div>
                        </div>

                      </div>
                    </div>

      <!-- Temple Description -->
      <div class="col-md-12 mb-3">
        <label for="description" class="form-label">Temple Description</label>
        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
      </div>

      <!-- Image Upload -->
      <div class="col-md-12 mb-3">
        <label for="photo" class="form-label">Upload Temple Photo</label>
        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
      </div>

      <!-- Submit Button -->
      <div class="col-md-12 text-center">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <button type="submit" class="btn btn-primary">Submit Temple</button>
      </div>
    </div>
  </form>
</div>
</section>

<?php
include('./include/footer.php');
?>
<script>
  // Load states when a country is selected
  $('#country').change(function() {
    let countryCode = $(this).val();
    $.ajax({
      url: 'app/get_states.php',
      type: 'POST',
      data: {
        country_code: countryCode
      },
      success: function(response) {
        $('#state').html(response);
        $('#state').prepend('<option selected disabled>Select State</option>'); // Reset with placeholder
        $('#city').html('<option selected disabled>Select City</option>'); // Reset city dropdown

      }
    });
  });

  // Load cities when a state is selected
  $('#state').change(function() {
    let stateCode = $(this).val();
    $.ajax({
      url: 'app/get_cities.php',
      type: 'POST',
      data: {
        state_code: stateCode
      },
      success: function(response) {
        $('#city').html(response);
        $('#city').prepend('<option selected disabled>Select City</option>'); // Reset with placeholder
      }
    });
  });
</script>

