<?php



error_reporting(1);



include('./include/header.php');







// Include required classes



include_once './app/class/XssClean.php';



include_once './app/class/databaseConn.php';



include_once './app/lib/requestHandler.php';

include_once './include/temple_detail_helpers.php';
include_once './include/breadcrumb_helpers.php';







$DatabaseCo = new DatabaseConn();



$xssClean = new xssClean();



$id = $xssClean->clean_input($_REQUEST['id']);







// Fetch temple details for the provided id



$select = "SELECT * FROM `temples` WHERE index_id='$id'";



$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



$templeVideos = [];



// Check if the query returns a result



if (mysqli_num_rows($SQL_STATEMENT) > 0) {



    $Row = mysqli_fetch_object($SQL_STATEMENT);



    $vidRes = @mysqli_query($DatabaseCo->dbLink, "SELECT * FROM temple_videos WHERE temple_id='" . intval($id) . "' ORDER BY index_id");



    if ($vidRes) {
        while ($v = mysqli_fetch_object($vidRes)) { $templeVideos[] = $v; }
    }
    // Fallback: legacy video_url only when it is a video link (maps URLs use map iframe)
    if (empty($templeVideos) && isset($Row->video_url) && !empty(trim($Row->video_url ?? ''))) {
        $legacyVideoUrl = trim($Row->video_url);
        if (temple_detail_is_video_url($legacyVideoUrl)) {
            $templeVideos[] = (object)['video_url' => $legacyVideoUrl, 'video_thumbnail' => $Row->video_thumbnail ?? null];
        }
    }

    $photo = $Row->upload_image;
    $country = $Row->country;
    $state = $Row->state;
    $city = $Row->city;
    $address = $Row->address;

    extract(temple_detail_build_view($DatabaseCo->dbLink, $Row));
    $mainRenderOrder = ['About', 'History', 'Deity', 'Mystical', 'Seva', 'Contact'];

} else {



    echo "<p>Temple not found.</p>";



    exit;



}





// Create the full address



$fullAddress = urlencode("$address, $city, $state, $country");


$liveStreams = temple_detail_live_streams($DatabaseCo->dbLink, $id);



?>



<style>
.mt-10{
    margin-top:15px;
}
    .stream {
      display: flex;
      flex-direction: column;
      background: #fcfcff;
      border-radius: 12px;
      margin-bottom: 30px;
      margin-top:10px;
      border: 1px solid #e3eaff;
      transition: 0.3s ease;
      padding: 10px;
    }

    .stream:hover {
      box-shadow: 0 5px 20px rgba(34, 69, 160, 0.1);
    }

    .stream.live {
      border: 2px solid #d49c00;
      background: #fff9e6;
    }

    .stream h2 {
      font-size: 16px;
      margin: 10px 0;
      color: #19378a;
    }

    .live-badge {
      background: #e63946;
      color: white;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 0.8rem;
      font-weight: 600;
      margin-left: 10px;
      vertical-align: middle;
      box-shadow: 0 2px 6px rgba(230,57,70,0.3);
    }

    iframe {
      width: 100%;
      height: 320px;
      border-radius: 10px;
      border: none;
      margin-top: 10px;
    }

    .schedule {
      color: #555;
      font-size: 0.95rem;
      margin-top: 8px;
    }

    footer {
      text-align: center;
      font-size: 14px;
      margin-top: 40px;
      color: #777;
    }

    footer p {
      border-top: 1px solid #eee;
      padding-top: 15px;
      margin-top: 30px;
    }

    @media (max-width: 600px) {
      .stream iframe {
        height: 220px;
      }
    }

    h2.underline {



        display: inline-block; /* Makes the border width adapt to the text */



        border-bottom: 2px solid #000; /* Creates a bottom border */



        padding-bottom: 4px; /* Optional: Adds some spacing between text and border */



        margin-bottom: 10px; /* Adjusts spacing below the h2 if needed */



    }



    .custom-btn {



        font-size: 18px;



        font-weight: 600;



        border: 3px solid #ff8776;



        /* Primary border color */



        color: black;



        /* Primary text color */



        background-color: transparent;



        transition: all 0.3s ease;



    }







    .custom-btn:hover {



        border: 3px solid black;



        background-color: #ff8776 !important;



        /* Primary background on hover */



        color: black;



        /* White text on hover */



    }







    /* Sticky tab-container */



    .tab-container {
        position: sticky;
        top: 0;
        z-index: 56;
        padding: 10px 0;
        text-align: center;
    }

    .temple-section-nav {
        overflow: hidden;
    }

    .temple-section-nav .btn-cus {
        min-height: 2.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1.25;
        white-space: normal;
        word-break: break-word;
    }

    .sth-text img,
    #pageReadContent img {
        max-width: 100%;
        height: auto;
    }

.theiaStickySidebar .btn-cus {
    padding: 8px 10px;
}

    @media (max-width: 576px) {



    .custom-btn {



        width: 100%; /* Full-width buttons on small screens */



        margin-bottom: 0.5rem;



    }



}







@media (max-width: 767px) {



    .custom-btn {



        font-size: 1rem; /* Optional: Adjust button text size */



        padding: 0.5rem 1rem;



    }



}



/* Custom button style */



.custom-btn {



    line-height: 1.5; /* Adjust line height for better text alignment */



    padding: 10px 20px; /* Adjust padding for better spacing */



    font-size: 1rem; /* Control font size for better readability */



    margin-bottom: 1rem; /* Space between buttons when stacked */



}







/* Adjust button spacing on mobile */



@media (max-width: 767px) {



    .custom-btn {



        font-size: 1.1rem; /* Slightly larger font on mobile */



        padding: 12px 24px; /* Increase button padding on mobile */



    }



}







/* Adjust button spacing on tablet screens */



@media (min-width: 768px) and (max-width: 991px) {



    .custom-btn {



        font-size: 1.2rem; /* Larger font on tablets */



    }



}







/* Adjust button spacing on desktop */



@media (min-width: 992px) {



    .custom-btn {



        font-size: 1.3rem; /* Larger font on desktops */



    }



}


.timing-section {
  background: #f8faff;
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 3px 10px rgba(35, 95, 200, 0.1);
  border-left: 4px solid #2c4da5;
  font-family: 'Poppins', sans-serif;
  color: #1f2c47;
  max-width: 420px;
  margin: 20px auto;
}

.timing-section h4 {
  color: #19378a;
  font-weight: 600;
  margin-bottom: 15px;
  text-align: center;
}

.timing-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.timing-list li {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #e4ebff;
  font-size: 15px;
}

.timing-list li:last-child {
  border-bottom: none;
}

.day-name {
  font-weight: 600;
  color: #000000ff;
  width:120px;
}

.time-range {
  font-weight: 500;
  color: #333;
}




</style>





<style>

@media print {

  /* Reduce padding and margins globally */

  body, html {

    margin: 0;

    padding: 0;

  }



  * {

    margin: 0 !important;

    padding: 0 !important;

    line-height: 1.2 !important;

  }



  /* Optional: Adjust specific elements */

  table {

    border-spacing: 0;

    border-collapse: collapse;

  }



  td, th {

    padding: 2px 4px !important;

  }



  .print-section {

    margin: 0 !important;

    padding: 0 !important;

  }

}

</style>

<!-- Start gallery with print icon -->

<?php

    $year = $Row->temple_age; // No year found

    $templeBreadcrumbParent = (isset($Row->country) && strtoupper((string) $Row->country) !== 'IN')
        ? ['label' => 'Temples Abroad', 'url' => 'abroad.php']
        : ['label' => 'Temples in India', 'url' => 'temples-in-india.php'];

    render_breadcrumbs([
        ['label' => 'Home', 'url' => 'index.php'],
        $templeBreadcrumbParent,
        ['label' => $Row->title ?? 'Temple Details'],
    ]);

?>
<link href="assets/css/temple-pages-responsive.css" rel="stylesheet">

<div class="container-fluid m-0 p-0 text-center bg-gradient text-center temple-detail-page">



    <div class="overflow-hidden position-relative  banner-over-container">



        <img <?php echo temple_detail_photo_attrs($Row->photos ?? ''); ?>>



        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title;?></h1>



    </div>



</div>



<!-- End gallery -->



<!-- Video player container -->
<div class="modal fade" id="videoModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Video Story</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-0">
        <div id="videoContainer" style="width:100%; height:400px;">
            <!-- Video iframe will appear here -->
        </div>
      </div>

    </div>
  </div>
</div>
<style>
    .owl-carousel .owl-nav button.owl-next, .owl-carousel .owl-nav button.owl-prev{
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgb(255 135 118);
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: 0.3s;
    }

    .owl-carousel .owl-nav button.owl-prev {
    left: 10px;   /* adjust as needed */
}

.owl-carousel .owl-nav button.owl-next {
    right: 10px;  /* adjust as needed */
}
    .stories-wrapper {
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px 0;
}

.stories-carousel {
    display: flex;
    gap: 15px;
}

.story-item {
    position: relative;
    width: 100%;
    height: 160px;
    border-radius: 15px;
    overflow: hidden;
    cursor: pointer;
}

.story-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.play-btn {
      position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 35px;
    color: white;
    text-shadow: 0 0 10px black;
    border: 2px solid #fff;
    border-radius: 100%;
    width: 60px;
    height: 60px;
    text-align: center;
    padding: 0px;
    padding-left: 5px;
    background: rgba(0, 0, 0, .4);
}
#story-player {
    margin-top: 20px;
    width: 100%;
    max-width: 400px;
}
#story-player iframe {
    width: 100%;
    height: 300px;
    border-radius: 15px;
}

</style>

<div id="printable-content" class="bg-gradient temple-detail-page">







    <!-- Start printable-area -->



    <div id="printable-area">



        <div class="py-3 py-lg-5">



            <div class="container px-2 px-md-3">



                <div class="col-12">



                    <?php if ($specialityTitle !== '' || $specialityText !== '' || $godName !== '') : ?>
                        <div class="card shadow mb-5 bg-body rounded text-dark p-4 mb-4 sth-text">
                            <?php if ($specialityTitle !== '') : ?>
                                <h2 class="text-dark text-center"><?php echo htmlspecialchars($specialityTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <?php endif; ?>
                            <?php if ($godName !== '') : ?>
                             <?php endif; ?>
                            <?php if ($specialityText !== '') : ?>
                                <div class="text-dark sth-text"><?php echo $specialityText; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>







                </div>



                <div class="row">







                    <!-- Main Content Section -->



                    <div id="pageReadContent" class="col-12 col-lg-8 ps-lg-3 ps-xxl-5 content">



                        <?php if (!empty($navSections)) : ?>
                      <div class="tab-container text-center mb-4 hidePrint custom-sticky">
                          <div class="card rounded-4 border-0 bg-gradient temple-section-nav">
                              <div class="card-body p-2 p-sm-3">
                                  <div class="row g-2 g-sm-3 justify-content-center temple-section-nav__grid">
                                      <?php foreach ($navSections as $section) : ?>
                                          <div class="col-6 col-sm-4 col-lg">
                                              <button type="button" onclick="scrollToCard('<?php echo temple_detail_section_id($section); ?>')" class="btn btn-primary btn-cus w-100">
                                                  <?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?>
                                              </button>
                                          </div>
                                      <?php endforeach; ?>
                                  </div>
                              </div>
                          </div>
                      </div>
                        <?php endif; ?>

<div id="bannerTitle">
    <?php foreach ($mainRenderOrder as $anchor) : ?>
        <?php if ($anchor === 'Contact') : ?>
            <?php if (!$contactSection) { continue; } ?>
            <div id="Contact" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">
                <h2 class="text-dark font-caveat">Contact</h2>
                <div><?php echo $contactSection['content']; ?></div>
            </div>
        <?php else : ?>
            <?php
            if (!isset($sectionByAnchor[$anchor]) || !temple_detail_section_visible($sectionByAnchor[$anchor])) {
                continue;
            }
            $section = $sectionByAnchor[$anchor];
            $sectionId = temple_detail_section_id($section);
            ?>
            <div id="<?php echo htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8'); ?>" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">
                <h2 class="text-dark font-caveat"><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <div><?php echo $section['content']; ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>



 <!-- Comment Form -->
 <div class="row print-disable">
    <div class="comment-box mt-3 ">
        <h4>Leave a Comment</h4>
        <div class="alert alert-success mt-3 d-none" id="success-message">
            Comment successfully submitted and is pending approval!
        </div>
        <form action="" method="post" id="submit-comment">
        <div class="form-group mb-3">
            <p>Name</p>
            <input type="text" class="form-control" id="name" placeholder="Your Name" required>
        </div>
        <div class="form-group">
            <p>Comment</p>
            <textarea class="form-control " id="comment" rows="4" placeholder="Your Comment" required></textarea>
        </div>
        <input type="hidden" name="type" id="type" value="india" />
        <button class="btn btn-primary mt-10" type="submit">Post Comment</button>
        </form>
    </div>
    <?php $query = "SELECT * FROM `comments` WHERE type='india' AND is_approved=1 ORDER BY index_id DESC";
    $result = mysqli_query($DatabaseCo->dbLink,$query);
    $allComments = [];
    while ($row = mysqli_fetch_object($result)) { $allComments[] = $row; }
    $totalComments = count($allComments);
    if($totalComments > 0){?>
    <h3 class="mt-5">Comments</h3>
    <div id="comments-section" class="comment-section">
    <?php foreach ($allComments as $ci => $Rowc) { $isHidden = $ci >= 3 ? ' comment-item-hidden' : ''; ?>
        <div class="comment-item<?php echo $isHidden; ?>" data-index="<?php echo $ci; ?>">
        <p><strong><?php echo htmlspecialchars($Rowc->name);?></strong> says,<br><?php echo nl2br(htmlspecialchars($Rowc->comment));?></p>
        <hr>
        </div>
    <?php }?>
    </div>
    <?php if ($totalComments > 3): ?>
    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="viewMoreComments" data-show-each="3">View more</button>
    <?php endif; ?>
    <?php }?>
</div>
                    

    </div>










                    <!-- Sidebar Section -->



                    <div class="col-12 col-lg-4 sidebar mt-4 mt-lg-0">



                        <div class="social-media hidePrint">



                            <!-- Print Icon -->



                            <a class="btn btn-primary d-inline-block" href="#" data-lang="<?php echo $_GET['lang'] ?? 'en'; ?>" id="printBtn">



                                <i class="fas fa-print"></i>



                            </a>







                            <!-- WhatsApp Icon -->



                            <a class="btn btn-primary d-inline-block" href="#" onclick="shareToWhatsApp()">



                                <i class="fab fa-whatsapp"></i>



                            </a>



                            <!-- PDF Download Icon -->



                            <!--<a class="btn btn-primary d-inline-block" href="#" onclick="downloadPDF()">-->



                            <!--    <i class="fas fa-file-pdf"></i>-->



                            <!--</a>-->



                            <!-- Copy Link Icon



                            <a class="btn btn-primary d-inline-block" href="#" onclick="copyContent()">



                                <i class="fas fa-copy"></i>



                            </a> -->



                            <button id="toggleIcon" class="btn btn-primary d-inline-block">
                                <i class="fa fa-play"></i> Listen to Page
                            </button>







                        </div>







                        <?php if (!empty($liveStreams)) : ?>
                            <div class="border p-4 rounded-4 shadow-sm" style="margin-top: 30px;">
                                <h4 class="mb-3">Live Darshan / <span class="text-primary">Aarti Schedule</span></h4>
                                <?php foreach ($liveStreams as $stream) : ?>
                                    <?php
                                    date_default_timezone_set($stream['timezone']);
                                    $now = date('H:i');
                                    $isLive = ($stream['status'] === 'Live')
                                        && $stream['start_time'] !== ''
                                        && $stream['end_time'] !== ''
                                        && ($now >= $stream['start_time'] && $now <= $stream['end_time']);
                                    $streamTitle = $stream['god_name'] !== '' ? $stream['god_name'] : $stream['temple_name'];
                                    ?>
                                    <div class="stream <?= $isLive ? 'live' : '' ?>">
                                        <h2><?= htmlspecialchars($streamTitle, ENT_QUOTES, 'UTF-8') ?>
                                            <?php if ($isLive) : ?>
                                                <span class="live-badge">🔴 Live Now</span>
                                            <?php endif; ?>
                                        </h2>
                                        <?php if ($isLive) : ?>
                                            <iframe src="<?= htmlspecialchars($stream['youtube_url'], ENT_QUOTES, 'UTF-8') ?>" allowfullscreen></iframe>
                                        <?php else : ?>
                                            <div class="schedule">
                                                Next Live Darshan: <strong><?= htmlspecialchars($stream['start_time'], ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($stream['end_time'], ENT_QUOTES, 'UTF-8') ?> (IST)</strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($timingsContent !== ''): ?>



                            <div class="border p-4 rounded-4 shadow-sm" style="margin-top: 30px;">

                                <div class="d-flex align-items-center justify-content-between mb-3">



                                    <h4 class="mb-0">Temple  <span class="text-primary">Timings</span></h4>



                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">



                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />



                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />



                                    </svg>



                                </div>



                                <div class="d-flex justify-content-between timing-list flex-column">



                                   <?php echo $timingsContent; ?>


                                </div>



                            </div>



                        <?php endif; ?>

                        <?php if ($hasGallery) : ?>
                            <div id="Photo" class="border p-4 rounded-4 shadow-sm mt-5 temple-sidebar-photos hidePrint">
                                <h4 class="mb-3 text-primary">Photos</h4>
                                <div class="row g-2 review-image zoom-gallery">
                                    <?php foreach ($galleryImages as $image) :
                                        $imagePath = temple_detail_image_url($image);
                                        if ($imagePath === '') {
                                            continue;
                                        }
                                    ?>
                                        <div class="col-4">
                                            <a href="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="gallery-overlay-hover dark-overlay position-relative d-block overflow-hidden rounded-3 gallery-image-link">
                                                <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Gallery Image" class="rounded-3 gallery-thumb w-100" onerror="this.closest('.col-4').style.display='none';">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>



                        <?php if (!empty($templeVideos)): ?>



                            <div class="border p-4 rounded-4 shadow-sm mt-5">



                                <div class="col-12">



                                    <h4 class="mb-0  text-primary">Video</h4>



                                    <div class="row mt-3 g-2 review-image">



                                        <?php foreach ($templeVideos as $tv):
                                        $vidUrl = trim($tv->video_url);
                                        if (!temple_detail_is_video_url($vidUrl)) { continue; }
                                        $vidId = preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $vidUrl, $m) ? trim($m[1]) : '';
                                        $thumbSrc = (isset($tv->video_thumbnail) && !empty($tv->video_thumbnail)) ? 'app/uploads/temple/video_thumb/' . htmlspecialchars($tv->video_thumbnail) : ($vidId ? 'https://img.youtube.com/vi/' . $vidId . '/hqdefault.jpg' : '');
                                        if ($vidId && $thumbSrc):
                                        ?>



                                                <div class="col-6">



                                                    <div class="gallery-overlay-hover dark-overlay position-relative d-block overflow-hidden rounded-3 gallery-image-link story-item" data-video="<?= htmlspecialchars($vidUrl) ?>" style="cursor:pointer;aspect-ratio:4/3;">



                                                        <img src="<?= htmlspecialchars($thumbSrc) ?>" alt="Video" class="rounded-3 gallery-thumb">



                                                        <span class="position-absolute top-50 start-50 translate-middle rounded-circle bg-dark bg-opacity-50 text-white d-flex align-items-center justify-content-center" style="width:50px;height:50px;pointer-events:none;"><i class="fa fa-play ms-1"></i></span>



                                                    </div>



                                                </div>



                                        <?php endif; endforeach; ?>



                                    </div>



                                </div>



                            </div>



                        <?php endif; ?>







                        <?php if ($addressSection) : ?>
                            <div class="border p-4 rounded-4 shadow-sm mt-5">
                                <h5 class="mb-3">Temple <span class="text-primary">Address</span></h5>
                                <div class="timing-list"><?php echo $addressSection['content']; ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasLocation) : ?>
                        <div id="Location" class="border p-4 rounded-4 shadow-sm mt-5 print-disable">
                            <div class="container">
                                <h5 class="mb-3">Temple <span class="text-primary">Location</span></h5>
                                <div id="location-info" class="d-none"></div>
                                <?php if ($mapEmbed !== '') : ?>
                                    <iframe src="<?php echo htmlspecialchars($mapEmbed, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="300" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <?php else : ?>
                                    <div id="map" style="width: 100%; height: 400px;"></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>



                    </div>



                </div>



            </div>



            <!-- <div id="location-info">Fetching your location...</div> -->







        </div>



    </div>



</div>



<!-- <div id="map" style="width: 100%; height: 400px;"></div> -->



</div>




<?php include('include/priest-reviews.php');?>





<!-- <div class="container">



    <h2>Temple Location</h2>



    <p><?php echo htmlspecialchars("$address, $city, $state, $country"); ?></p>



    <div id="map" style="width: 100%; height: 400px;"></div>



</div> -->



<?php



$select = "SELECT * FROM `temples` WHERE index_id='$id'";



$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);







// Check if the query returns a result



if (mysqli_num_rows($SQL_STATEMENT) > 0) {



    $Row = mysqli_fetch_object($SQL_STATEMENT);



    $photo = $Row->upload_image;



    $country = $Row->country;



    $temple_name = $Row->title;



    $state = $Row->state;



    $city = $Row->city;



    $address = $Row->address;



} else {



    echo "<p>Temple not found.</p>";



    exit;



}



?>







<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&libraries=places&callback=initMap"></script>



<script>



    function scrollToCard(cardId) {



        const card = document.getElementById(cardId);







        // Scroll to the card



        card.scrollIntoView({



            behavior: 'smooth'



        });







        // Add the highlight animation



        card.classList.add('highlight');







        // Remove the highlight animation after 1 second



        setTimeout(() => {



            card.classList.remove('highlight');



        }, 1000);



    }



    



    function initMap() {



    const locationInfoDiv = document.getElementById('location-info');



    const mapElement = document.getElementById('map');



    const [fromAddress, templeName] = <?php echo json_encode([$address, $temple_name]); ?>;







    const geocoder = new google.maps.Geocoder();







    // Show a loading spinner or message while waiting for the map to load



    locationInfoDiv.textContent = 'Loading map...';







    // Geocode the address to get the user's location



    geocoder.geocode({



        address: fromAddress



    }, (results, status) => {



        if (status === google.maps.GeocoderStatus.OK && results[0]) {



            const userLocation = results[0].geometry.location;



            console.log("Coordinates fetched: ", userLocation.lat(), userLocation.lng());







            // Initialize the map centered on the user's location



            const map = new google.maps.Map(mapElement, {



                center: userLocation,



                zoom: 11,



            });







            // Add a marker for the user's location with the highest z-index



            const userMarker = new google.maps.Marker({



                position: userLocation,



                map: map,



                title: results[0].formatted_address,



                zIndex: 9999, // Highest z-index for userMarker



                icon: {



                    url: "https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png",



                    scaledSize: new google.maps.Size(40, 40),



                },



            });







            // Create an info window for the user's location



            let currentInfoWindow = null; // Track the currently open info window







            // Function to set the content of the InfoWindow



            function setInfoWindowContent(marker, data) {



                const name = data.Name ? data.Name : 'Name not available';



                const content = `



                    <div style="text-align: center;">



                        <h4 class="fs-5 fw-semibold restaurant-text-truncate overflow-hidden mb-0">



                            <span style="padding:50px;">${templeName}</span>



                        </h4>



                        <br>



                        <span style="color: #555;">${data.address}</span>



                        <br>



                        <br>



                        <a href="https://www.google.com/maps/place/?q=place_id:${results[0].place_id}" target="_blank" style="color: #007bff; text-decoration: none;">



                            Click to get Direction



                        </a>



                    </div>



                `;



                return content;



            }







            // Attach the info window to the user marker



            userMarker.addListener('click', function() {



                if (currentInfoWindow) {



                    currentInfoWindow.close(); // Close the previous info window



                }







                currentInfoWindow = new google.maps.InfoWindow({



                    content: setInfoWindowContent(userMarker, {



                        address: results[0].formatted_address, // Example data



                        placeId: results[0].place_id, // Example data



                    }),



                });







                currentInfoWindow.open(map, userMarker); // Open the new info window



            });







            // Extract city and area/place from the geocoded results



            const components = results[0].address_components;



            let city = "",



                area = "";



            components.forEach((component) => {



                if (component.types.includes("locality")) {



                    city = component.long_name;



                }



                if (component.types.includes("sublocality") || component.types.includes("neighborhood")) {



                    area = component.long_name;



                }



            });







            const formattedLocation = `${area ? area + ", " : ""}${city}`;







            // Search for nearby temples



            const service = new google.maps.places.PlacesService(map);



            const request = {



                location: userLocation,



                radius: 50000, // 50 km radius



                type: ['hindu_temple'], // Specify "Hindu temples"



            };







            service.nearbySearch(request, (results, status) => {



                if (status === google.maps.places.PlacesServiceStatus.OK) {



                    results.forEach((place) => {



                        // Create a custom marker for temples



                        const placeMarker = new google.maps.Marker({



                            position: place.geometry.location,



                            map: map,



                            title: place.name,



                            icon: {



                                url: './assets/images/temple-icon.png', // Red Om symbol



                                scaledSize: new google.maps.Size(40, 40),



                            },



                        });







                        // Variable to track the currently displayed card



                        let customCardElement = null;







                        placeMarker.addListener('click', () => {



                            // Close the previously opened info window (if any)



                            if (currentInfoWindow) {



                                currentInfoWindow.close();



                            }







                            // Create custom content for the InfoWindow when a temple marker is clicked



                            const content = `



                                <div style="



                                    font-family: Arial, sans-serif; 



                                    font-size: 12px; 



                                    line-height: 1.4; 



                                    width: 100%; 



                                    max-width: 100%; /* Ensure it doesn't overflow */



                                    text-align: center; 



                                    padding: 10px; 



                                    border: 1px solid #ddd; 



                                    border-radius: 8px; 



                                    background-color: #fff; 



                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 



                                    position: relative; 



                                    overflow: hidden;



                                ">



                                    <!-- Close button -->



                                    <button class="close-button" style="



                                        position: absolute; 



                                        top: 10px; 



                                        right: 10px; 



                                        background: none; 



                                        border: none; 



                                        font-size: 16px; 



                                        cursor: pointer; 



                                        color: #d9534f;



                                    ">



                                        ×



                                    </button>



                                    <strong style="color: #d9534f; text-transform: capitalize;">



                                        ${place.name}



                                    </strong>



                                    <br><br>



                                    <span style="color: #555; font-size: 11px;">${place.vicinity}</span>



                                    <br><br>



                                    <a href="https://www.google.com/maps/place/?q=place_id:${place.place_id}" 



                                       target="_blank" 



                                       style="color: #007bff; text-decoration: none; font-size: 12px;">



                                        View on Google Maps



                                    </a>



                                </div>



                            `;







                            // Create a new InfoWindow for the temple



                            currentInfoWindow = new google.maps.InfoWindow({



                                content: content,



                            });







                            // Open the InfoWindow for the clicked marker



                            currentInfoWindow.open(map, placeMarker);







                            // Wait for the InfoWindow to open, then attach the close button event listener



                            google.maps.event.addListener(currentInfoWindow, 'domready', () => {



                                const closeButton = document.querySelector('.close-button');



                                closeButton.addEventListener('click', () => {



                                    currentInfoWindow.close();



                                });



                            });



                        });



                    });



                } else {



                    locationInfoDiv.textContent += "No nearby temples found.";



                }



            });



        } else {



            locationInfoDiv.textContent = "Unable to fetch your location. Please check the address.";



        }



    });



}







    window.onload = initMap;



</script>



<script>



    // JavaScript function to scroll to specific content section



    function scrollToCard(cardId) {



        var element = document.getElementById(cardId);



        if (element) {



            element.scrollIntoView({



                behavior: "smooth"



            });



        }



    }



</script>
<script>
(function() {
  var toggleButton = document.getElementById('toggleIcon');
  if (!toggleButton || !window.speechSynthesis) return;

  var synth = window.speechSynthesis;
  var utterance = null;
  var isPlaying = false;
  var selectedVoice = null;

  // Get text to read: use Sthalam+bannerTitle if present (e.g. temple page), else index page content
  function getTextToSpeak() {
    var sthalamEl = document.getElementById('Sthalam');
    var bannerTitleEl = document.getElementById('bannerTitle');
    if (sthalamEl && bannerTitleEl) {
      return (sthalamEl.textContent || '').trim() + ' ' + (bannerTitleEl.textContent || '').trim();
    }
    var parts = [];
    var heroTitle = document.querySelector('.hero-header_title');
    var heroLead = document.querySelector('.overlay-content .lead');
    var mainContent = document.getElementById('pageReadContent');
    if (heroTitle) parts.push((heroTitle.textContent || '').trim());
    if (heroLead) parts.push((heroLead.textContent || '').trim());
    if (mainContent) parts.push((mainContent.textContent || '').trim());
    var text = parts.filter(Boolean).join('. ');
    return text.replace(/\s+/g, ' ').trim();
  }

  function setupVoice() {
    var voices = synth.getVoices();
    selectedVoice = voices.find(function(v) {
      return v.lang === 'hi-IN' || v.name.toLowerCase().indexOf('hindi') !== -1 || v.lang === 'en-IN';
    });
  }
  if (synth.getVoices().length) setupVoice();
  synth.onvoiceschanged = setupVoice;

  function playSpeech() {
    if (synth.speaking) synth.cancel();
    var text = getTextToSpeak();
    if (!text) {
      if (typeof toastr !== 'undefined') toastr.info('No content to read.');
      return;
    }
    utterance = new SpeechSynthesisUtterance(text);
    if (selectedVoice) utterance.voice = selectedVoice;
    utterance.rate = 0.95;
    utterance.pitch = 1;
    utterance.volume = 1;
    utterance.onend = function() {
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-play"></i> Listen to Page';
    };
    utterance.onerror = function() {
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-play"></i> Listen to Page';
    };
    synth.speak(utterance);
    isPlaying = true;
    toggleButton.innerHTML = '<i class="fa fa-stop"></i> Stop';
  }

  toggleButton.addEventListener('click', function() {
    if (isPlaying) {
      synth.cancel();
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-play"></i> Listen to Page';
    } else {
      playSpeech();
    }
  });

  window.addEventListener('beforeunload', function() {
    if (synth.speaking) synth.cancel();
  });

  var voiceSelect = document.getElementById('voiceSelect');
  if (voiceSelect) {
    function populateVoiceList() {
      var voices = synth.getVoices();
      voiceSelect.innerHTML = '';
      voices.forEach(function(voice, index) {
        var option = document.createElement('option');
        option.textContent = voice.name + ' (' + voice.lang + ')';
        option.value = index;
        voiceSelect.appendChild(option);
      });
    }
    if (synth.getVoices().length) populateVoiceList();
    synth.onvoiceschanged = populateVoiceList;
  }
})();
</script>
<?php
function getTemplePrintContent($Row) {

    // Handle empty fields
    $safe = function($data) {
        return !empty($data) ? $data : "Information not available.";
    };

    // Prepare gallery
    $galleryHTML = "";
    if (!empty($Row->gallery_image)) {
        $imgs = array_filter(explode(",", $Row->gallery_image));
        $galleryHTML .= "<h2 class='sec-head font-caveat' style='border-bottom:3px solid #ff8776; width:fit-content;'>Gallery</h2>
            <div>";
        foreach ($imgs as $img) {
            $path = "app/uploads/Temple_gallery/" . trim($img);
            if (file_exists($path)) {
                $galleryHTML .= "<img src='$path' style='width:300px; margin:10px; border-radius:8px; height:220px;'/>";
            }
        }
        $galleryHTML .= "</div>";
    }
    
    // Build full printable HTML
    $html = "
    <link href='assets/css/css.css' rel='stylesheet'>
    <style>
    /* Google fonts --------------------------- */
@import url('https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap');
    .para{
      font-family: 'georgia';
    font-size: 18px !important;
    text-align: justify;
    word-spacing: -0.5px;
}
    .sec-head{
        font-family: 'georgia';
    }
     .caveat-text {
        font-family: 'Caveat', cursive !important;
    }

.timing-section {
  background: #f8faff;
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 3px 10px rgba(35, 95, 200, 0.1);
  border-left: 4px solid #2c4da5;
  font-family: 'Poppins', sans-serif;
  color: #1f2c47;
  max-width: 420px;
  margin: 20px auto;
}

.timing-section h4 {
  color: #19378a;
  font-weight: 600;
  margin-bottom: 15px;
  text-align: center;
}

.timing-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.timing-list li {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #e4ebff;
  font-size: 15px;
}

.timing-list li:last-child {
  border-bottom: none;
}

.day-name {
  font-weight: 600;
  color: #000000ff;
  width:120px;
}

.time-range {
  font-weight: 500;
  color: #333;
}
    </style>
    
        <div style='padding:20px; font-family:Arial;'>

            <h1 class='caveat-text font-caveat' style='font-weight: 600 !important;'>{$safe($Row->title)}</h1>

            <img src='app/uploads/temple/{$Row->photos}'
                 style='width:100%; max-height:300px; object-fit:cover; border-radius:8px; margin-bottom:20px;' />

            <h2 class='sec-head font-caveat' style=' width:fit-content;'>Speciality</h2>
            <p class='para'>{$safe($Row->speciality)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Sthalam</h2>
            <p class='para'>{$safe($Row->sthalam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Era</h2>
            <p class='para'>{$safe($Row->temple_age)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Puranam</h2>
            <p class='para'>{$safe($Row->puranam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Varnam</h2>
            <p class='para'>{$safe($Row->varnam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Highlights</h2>
            <p class='para'>{$safe($Row->highlights)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Sevas</h2>
            <p class='para'>{$safe($Row->sevas)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Timings</h2>
            <p class='para'>{$safe($Row->time)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Address</h2>
            <p class='para'>{$safe($Row->address)}, {$safe($Row->city)}, {$safe($Row->state)}, {$safe($Row->country)}</p>

            $galleryHTML

        </div>
    ";

    return $html;
}

$printContent = getTemplePrintContent($Row);
?>
<div id="printArea" style="display:none;">
    <?php echo $printContent; ?>
</div>

<script>
(function () {
    function getPrintBaseHref() {
        var pathParts = window.location.pathname.split('/');
        pathParts.pop();
        return window.location.origin + pathParts.join('/') + '/';
    }

    function initPrint() {
        var printBtn = document.getElementById('printBtn');
        var printArea = document.getElementById('printArea');
        if (!printBtn || !printArea) {
            return;
        }

        printBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var printContents = printArea.innerHTML;
            var printWindow = window.open('', '_blank', 'width=900,height=650');
            if (!printWindow) {
                alert('Please allow popups to print.');
                return;
            }

            var baseHref = getPrintBaseHref();
            var watermarkStyle = '<style type="text/css">' +
                '.print-watermark{position:fixed;top:0;left:0;width:100%;height:100%;' +
                'display:flex;align-items:center;justify-content:center;pointer-events:none;' +
                'z-index:1;opacity:0.12;font-size:120px;font-weight:bold;color:#000;' +
                'font-family:Arial,sans-serif;transform:rotate(-25deg);-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
                '.print-content-wrap{position:relative;z-index:2;}' +
                '</style>';
            var watermarkHtml = '<div class="print-watermark" aria-hidden="true">Bhaktikalpa</div><div class="print-content-wrap">' + printContents + '</div>';

            printWindow.document.open();
            printWindow.document.write('<html><head><title>Print Temple</title><base href="' + baseHref + '">' + watermarkStyle + '</head><body>');
            printWindow.document.write(watermarkHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.onafterprint = function () {
                printWindow.close();
            };

            var didPrint = false;
            function triggerPrint() {
                if (didPrint || printWindow.closed) {
                    return;
                }
                didPrint = true;
                printWindow.focus();
                printWindow.print();
            }

            printWindow.onload = triggerPrint;
            setTimeout(triggerPrint, 400);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrint);
    } else {
        initPrint();
    }
})();
</script>



<?php include('./include/footer.php'); ?>

<script>
$(document).ready(function() {

    // Initialize Owl Carousel
    $('.stories-carousel').owlCarousel({
          loop:true,
        margin:25,
        nav:true,
        dots:false,
        autoplay:false,
        smartSpeed:600,
        responsive:{
          0:{ items:2 },
          768:{ items:4 },
          1200:{ items:6 }
        }
    });

   // Extract YouTube ID from URL
    function getYouTubeID(url) {
        const match = url.match(/(?:v=|youtu\.be\/)([^&]+)/);
        return match ? match[1] : null;
    }

    // When a story is clicked → open modal with video
    $(".story-item").click(function () {
        let videoURL = $(this).data("video");
        let videoID = getYouTubeID(videoURL);

        if (!videoID) return alert("Invalid YouTube link");

        let iframeHTML = `
            <iframe width="100%" height="400" 
                src="https://www.youtube.com/embed/${videoID}?autoplay=1" 
                frameborder="0" 
                allow="autoplay; encrypted-media" 
                allowfullscreen>
            </iframe>
        `;

        $("#videoContainer").html(iframeHTML);

        let modal = new bootstrap.Modal(document.getElementById('videoModal'));
        modal.show();
    });

    // Stop video when modal is closed
    $('#videoModal').on('hidden.bs.modal', function () {
        $("#videoContainer").html(""); // removes the iframe
    });

    // Comments "View more" - show 3 more on each click
    $('#viewMoreComments').on('click', function() {
        var hidden = $('#comments-section .comment-item-hidden');
        var showCount = parseInt($(this).data('show-each') || 3, 10);
        hidden.slice(0, showCount).removeClass('comment-item-hidden');
        if ($('#comments-section .comment-item-hidden').length === 0) {
            $(this).hide();
        }
    });
});
</script>
<style>
.comment-item-hidden { display: none !important; }
</style>