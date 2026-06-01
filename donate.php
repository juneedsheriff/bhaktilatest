<?php

include('./include/header.php');

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

// Check if god_id is set to 7 in the URL
error_reporting(0);

?>
<style>
    .donation-form { background: #fff; padding: 20px; max-width: 400px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .donation-form h2 { text-align: center; margin-bottom: 20px; }
    .donation-form label { display: block; margin: 10px 0 5px; }
    .donation-form input, .donation-form select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
    .donation-form button { margin-top: 15px; width: 100%; padding: 10px; background: #4CAF50; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
    .donation-form button:hover { background: #45a049; }
  </style>
<div class="py-3 py-xl-5 bg-gradient">

    <div class="container">
       
<div id="listings-container" class="">
<div class="donation-form">
    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Every Donation is a Step Towards Dharma</div>
    <div id="donationMessage"></div>
  <form action="save_donation.php" id="donationForm" method="post">
    <label for="donor_name">Full Name</label>
    <input type="text" id="donor_name" name="donor_name" required>

    <label for="donor_email">Email</label>
    <input type="email" id="donor_email" name="donor_email">

    <label for="donor_phone">Phone</label>
    <input type="text" id="donor_phone" name="donor_phone">

    <label for="amount">Donation Amount (₹)</label>
    <input type="number" id="amount" name="amount" step="0.01" required>

    <label for="donation_purpose">Purpose</label>
    <select id="donation_purpose" name="donation_purpose">
      <option value="Annadanam">Annadanam</option>
      <option value="Temple Maintenance">Temple Maintenance</option>
      <option value="Special Pooja">Special Pooja</option>
      <option value="Festivals">Festivals</option>
    </select>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <button type="submit">Submit Donation</button>
  </form>
</div>
</div>
</div>
<?php include_once './include/footer.php' ?>

<script>
    $(document).ready(function () {
    $("#donationForm").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "donation_ajax.php", // your PHP handler
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                try {
                    let res = response;
                    if (res.success) {
                        $("#donationMessage").html(
                            "<p style='color:green;'>" + res.message + "</p>"
                        );
                        $("#donationForm")[0].reset();
                    } else {
                        $("#donationMessage").html(
                            "<p style='color:red;'>" + res.message + "</p>"
                        );
                    }
                } catch (e) {
                    $("#donationMessage").html("<p style='color:red;'>Invalid server response</p>");
                }
            },
            error: function () {
                $("#donationMessage").html("<p style='color:red;'>Something went wrong.</p>");
            }
        });
    });
});
</script>