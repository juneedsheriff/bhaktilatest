<?php

include('./include/header.php');

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

// Check if god_id is set to 7 in the URL

$id = $xssClean->clean_input($_REQUEST['id']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<p>Invalid Priest ID.</p>";
    exit;
}

// Fetch the priest record
$query = "SELECT * FROM priests WHERE id = $id LIMIT 1";
$result = mysqli_query($DatabaseCo->dbLink, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<p>Priest not found.</p>";
    exit;
}

$priest = mysqli_fetch_assoc($result);

error_reporting(0);

?>
<style>
    .section {
    position: relative;
    padding: 50px 0;
}
.sigma_volunteer-detail .sigma_member-name {
    margin-bottom: 12px;
}
.sigma_volunteer-detail .sigma_volunteer-detail-info {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #efefef;
    margin-bottom: 0;
    padding-left:0px;
}

/*----- volunteers Details----- */

.sigma_volunteer-detail .sigma_member-image{
  position: relative;
  overflow: hidden;
  height: auto;
}

.sigma_volunteer-detail .sigma_member-image img{
  width: 100%;
}

.sigma_volunteer-detail .sigma_volunteer-detail-category{
  color: #7E4555;
  font-size: 16px;
  font-weight: 600;
}

.sigma_volunteer-detail .sigma_member-name{
  margin-bottom: 12px;
}



.sigma_volunteer-detail .sigma_volunteer-detail-info li{
  display: flex;
  align-items: center;
  font-size: 16px;
  margin: 0;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info li i{
  font-size: 20px;
  color: #7E4555;
  margin-right: 30px;
  width: 15px;
}
.sigma_volunteer-detail .sigma_volunteer-detail-info li + li{
  margin-top:5px;
}
.sigma_volunteer-detail .sigma_volunteer-detail-info li .title{
  color: #db4242;
  font-weight: 600;
  padding-right: 12px;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info li a{
  color: #777;
}
.sigma_volunteer-detail .sigma_volunteer-detail-info li a:hover{
  color: #7E4555;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li{
   flex-direction: column;
   align-items: flex-start;
   padding-left: 40px;
   position: relative;
   font-size: 16px;
   padding-bottom: 20px;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li + li{
  margin-top: 0;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li:last-child{
  padding-bottom: 0;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li:before{
  content: "";
  position: absolute;
  left: 2px;
  top: 10px;
  width: 3px;
  height: 100%;
  border-radius: 0;
  background-color: #7E4555;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li:after{
  content: "";
  position: absolute;
  left: 0;
  top: 10px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #7E4555;
}

.sigma_volunteer-detail .sigma_volunteer-detail-info.has-element li:last-child:before{
  content: none;
}


/* 6.13. Pricing */
.sigma_pricing{
  background-image: url(../img/pricing.png);
  background-position: center;
  background-size: cover;
  background-repeat: no-repeat;
  padding: 26px 20px 44px 20px;
  margin-bottom: 30px;
}
.sigma_pricing .sigma_pricing-icon{
  margin-bottom: 18px;
}
.sigma_pricing-title{
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 25px;
}
.sigma_pricing-price{
  position: relative;
  display: inline-block;
  line-height: 1;
}
.sigma_pricing-price .sigma_pricing-currency{
  position: absolute;
  top: 0;
  left: -12px;
  font-size: 20px;
  font-weight: 400;
}
.sigma_pricing-price span{
  font-size: 48px;
  font-weight: 800;
  color: #7E4555;
}
.sigma_pricing .list-style-none{
  margin-top: 20px;
}
.sigma_pricing .sigma_btn-custom{
  margin-top: 35px;
}

.sigma_pricing .list-style-none li i{
  margin-right: 10px;
}

/* Style 2 */
.sigma_pricing.pricing-2{
  background-image: none;
  background-color: #fff;
  padding: 30px;
  overflow: hidden;
  position: relative;
  box-shadow: 0px 10px 50px 0px rgba(53,82,99,0.09);
}
.sigma_pricing.pricing-2::before{
  content: '';
  position: absolute;
  width: 250px;
  height: 250px;
  border-radius: 50%;
  right: -100px;
  top: -90px;
  background-color: #f0fff0;
  border-radius: 50%;
}
.sigma_pricing.pricing-2 > i{
  position: absolute;
  top: 10px;
  right: 40px;
  font-size: 60px;
  color: #7E4555;
}

.sigma_pricing.pricing-2.main > i{
  color: #fff;
}
.sigma_pricing.pricing-2.main::before{
  background-color: #7E4555;
}

.sigma_pricing.pricing-2 .sigma_pricing-price{
  margin-left: 10px;
}

.sigma_pricing.pricing-2 .list-style-none{
  margin: 40px 0 0;
}
.sigma_progress {
    margin-bottom:15px;
}
.sigma_progress h6{
    margin-bottom:0px;
}
</style>
<!-- Post Content Start -->
<div class="section sigma_post-single">
  <div class="container">
    <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?= htmlspecialchars($priest['full_name']) ?></div>
    <div class="row">
      <div class="col-12">
        <div class="entry-content">
          <div class="sigma_volunteer-detail mb-5">
            <div class="row">
              <div class="col-lg-5">
                <div class="sigma_member-image style-1">
                  <img src="app/<?= htmlspecialchars($priest['photo']) ?>" class="mb-0" alt="<?= htmlspecialchars($priest['full_name']) ?>">
                </div>
              </div>
              <div class="col-lg-7">
                <div class="sigma_volunteer-detail-inner mt-5 mt-lg-0 ps-0 ps-lg-4">
                  <h3 class="sigma_member-name"><?= htmlspecialchars($priest['full_name']) ?></h3>
                  <span class="sigma_volunteer-detail-category"><?= htmlspecialchars($priest['specialization']) ?></span>
                  <ul class="sigma_volunteer-detail-info">
                    <li>
                      <i class="fas fa-phone"></i>
                      <span class="title">Phone:</span> <?= htmlspecialchars($priest['contact_number']) ?>
                    </li>
                    <li>
                      <i class="fas fa-envelope"></i>
                      <span class="title">Email:</span> <?= htmlspecialchars($priest['email']) ?>
                    </li>
                    <li>
                      <i class="fas fa-map-marker-alt"></i>
                      <span class="title">Address Info:</span> <?= htmlspecialchars($priest['address']) ?>
                    </li>
                    <li>
                      <i class="fas fa-birthday-cake"></i>
                      <span class="title">Date of Birth:</span> <?= htmlspecialchars($priest['date_of_birth']) ?>
                    </li>
                    <li>
                      <i class="fas fa-venus-mars"></i>
                      <span class="title">Gender:</span> <?= htmlspecialchars($priest['gender']) ?>
                    </li>
                    <li>
                      <i class="fas fa-user-clock"></i>
                      <span class="title">Experience:</span> <?= htmlspecialchars($priest['experience_years']) ?> years
                    </li>
                    <li>
                      <i class="fas fa-check-circle"></i>
                      <span class="title">Availability:</span> <?= $priest['available'] ? 'Available' : 'Not Available' ?>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <h4>About <?= htmlspecialchars($priest['full_name']) ?></h4>
          <p>Our mission is to share the Good of Hinduism, Loving, Faith and Serving. People ask questions related to Hinduism. Temple is a place where Hindu worship our Bhagwan Ram, Shiva, Vishnu, Krishna etc. galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting Some Hindu teachers insist that believing in rebirth is necessary for living an ethical life. Their concern is that if there is no fear of karmic repercussions in future lifetimes.

Temple is a place where Hindu worship our Bhagwan Ram, Shiva, Vishnu, Krishna etc. Proin eget tortor industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type, People ask questions related to Hinduism. specimen book.Some Hindu teachers insist that believing in rebirth is necessary for living an ethical life. Their concern is that if there is no fear of karmic repercussions in future lifetimes,</p>

          <hr>
          <h4>Skills</h4>
          <div class="row">
            <div class="col-lg-6">
              <p>Temple is a place where Hindu worship our Bhagwan Ram, Shiva, Vishnu, Krishna etc. Proin eget tortor industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type, People ask questions related to Hinduism. specimen book.people would likewise be unethical. Our mission is to share the Good of Hinduism, Loving, Faith and Serving. People ask questions related to Hinduism. to make a type specimen book.I find this argument as sad as the argument that without a belief in God people would likewise be unethical.

Some Hindu teachers insist that believing in rebirth is necessary for living an ethical life. Their concern is that if there is no fear of karmic repercussions in future lifetimes</p>
            </div>
            <div class="col-lg-6">
              <div class="mt-4 mt-lg-0">
                <div class="sigma_progress">
                  <h6>Charity</h6>
                  <span class="sigma_progress-count">84%</span>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 84%;"></div>
                  </div>
                </div>
                <div class="sigma_progress with-secondary">
                  <h6>Helped</h6>
                  <span class="sigma_progress-count">70%</span>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 70%;"></div>
                  </div>
                </div>
                <div class="sigma_progress">
                  <h6>Donations</h6>
                  <span class="sigma_progress-count">85%</span>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 85%;"></div>
                  </div>
                </div>
                <div class="sigma_progress">
                  <h6>Food</h6>
                  <span class="sigma_progress-count">92%</span>
                  <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 92%;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- Post Content End -->
<?php include_once './include/footer.php' ?>
