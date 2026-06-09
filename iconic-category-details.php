<?php ob_start();

error_reporting(0);

// Redirect id=86 (Shiridi Sai Temples) to Saikalpa - must run before any output
$id_raw = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
if ($id_raw === '86' || $id_raw === 86) {
    header('Location: https://www.saikalpa.com/');
    exit;
}

include('./include/header.php');



// Include required classes

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';
include_once './include/breadcrumb_helpers.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

$id = $xssClean->clean_input($_REQUEST['id']);

?>

<style>

    .map-img {

        width: 150px;

        height: 125px;

        margin-bottom: 10px;

        margin-left: 90px;

        border-radius: 10px;

    }



    .icons {

        position: fixed;

        right: 10px;

        top: 65%;

        transform: translateY(-50%);

        z-index: 2;

    }



    .btn1 {

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 16px;

        font-weight: bold;

        padding: 10px 15px;

        border: none;

        border-radius: 5px;

        color: white;

        cursor: pointer;

        transition: transform 0.2s ease, background-color 0.2s ease;

    }

    .iconic-category-detail-page .banner-over-container {
        overflow: visible;
        line-height: 0;
    }

    .iconic-category-detail-page .banner-over-container img {
        width: 100%;
        height: auto !important;
        max-height: none;
        object-fit: contain;
        display: block;
    }

    .iconic-category-detail-page .banner-over-title {
        line-height: 1.3;
    }

    .toggle-btn1 {

        background-color: #ff8776;

    }



    .toggle-btn1:hover {

        background-color: #ff6655;

    }

p{
    text-align: left !important;
}

    .btn1 i {

        margin-right: 5px;

    }



    .btn1:active {

        transform: scale(0.95);

    }



    @media (max-width: 768px) {

        .btn1 {

            font-size: 10px;

            padding: 6px;

        }

    }

.scroll {

    position: relative;

    overflow: visible;

    z-index: 0;

}

.comment-item-hidden { display: none !important; }

     

    .iconic-featured-wrap {
        width: 55%;
        margin-left: 14%;
        margin-bottom: 30px;
    }

    .iconic-featured-wrap::after {
        content: '';
        display: table;
        clear: both;
    }

    #printable-content {
        clear: both;
        width: 100%;
    }

    .iconic-main-content {
        width: 55%;
        margin-left: 14%;
        max-width: 100%;
    }

    @media (max-width: 991px) {
        .iconic-featured-wrap,
        .iconic-main-content {
            width: 100%;
            margin-left: 0;
        }
    }

    .iconic-fixed-actions {
        position: fixed;
        top: 50%;
        right: 40px;
        transform: translateY(-50%);
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px;
    }

    .iconic-fixed-actions .btn {
        display: block;
        min-width: 70px;
    }

    .iconic-fixed-actions .voicebtn-play {
        background-color: #0e8733;
        color: #fff;
        border: none;
    }

    @media (max-width: 768px) {
        .iconic-fixed-actions {
            top: auto;
            bottom: 80px;
            transform: none;
        }
    }
</style>

<?php

    // Fetch temple details for the provided id

    $select = "SELECT * FROM `iconic` WHERE index_id='$id'";

    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



    // Check if the query returns a result

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

        $Row = mysqli_fetch_object($SQL_STATEMENT);
        $categoryTitle = htmlspecialchars($Row->title ?? '', ENT_QUOTES, 'UTF-8');
        $bannerSrc = trim((string) ($Row->banner ?? '')) !== ''
            ? 'app/uploads/iconic/banner/' . $Row->banner
            : 'assets/images/default-image.png';

   ?>

<?php
render_breadcrumbs([
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Iconic Temples', 'url' => 'iconic-category.php'],
    ['label' => $Row->title ?? 'Category Details'],
]);
?>
<link href="assets/css/temple-pages-responsive.css" rel="stylesheet">

<div class="container-fluid m-0 p-0 text-center bg-gradient temple-detail-page iconic-category-detail-page">

    <div class="position-relative banner-over-container">

        <img src="<?php echo htmlspecialchars($bannerSrc, ENT_QUOTES, 'UTF-8'); ?>" class="w-100 img-fluid iconic-banner-mob" alt="<?php echo $categoryTitle; ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/images/default-image.png';">

        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary"><?php echo $categoryTitle; ?></h1>

    </div>

</div>

<?php } else {

        header("location:iconic-category.php");

    }

        $select = "SELECT * FROM `iconic_temples` WHERE categories_id='$id' ORDER BY index_id DESC  LIMIT 10";

        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

        $sql3 = mysqli_query($DatabaseCo->dbLink, "SELECT categories_id FROM iconic_temples WHERE categories_id = '$id' LIMIT 1");

        $res3 = mysqli_fetch_object($sql3);

    ?>

    <?php if (mysqli_num_rows($SQL_STATEMENT) > 0) {?>

<div class="position-relative print-disable">

    <div class="iconic-featured-wrap">
        <div id="last" class="listings grid-view listings-yellow-border">
                    <?php
                    $count = 0;
                    while ($templeRow = mysqli_fetch_assoc($SQL_STATEMENT)) {
                        if ($count >= 3) {
                            break;
                        }
                        $count++;

                        $photos = $templeRow['photos'];
                        $title = htmlspecialchars($templeRow['title'] ?? '', ENT_QUOTES, 'UTF-8');
                        $templeId = (int) ($templeRow['index_id'] ?? 0);
                        $photoSrc = trim((string) $photos) !== ''
                            ? 'app/uploads/iconic_temple/' . $photos
                            : 'assets/images/default-image.png';

                        $cityName = '';
                        $stateName = '';
                        if (!empty($templeRow['city'])) {
                            $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . mysqli_real_escape_string($DatabaseCo->dbLink, $templeRow['city']) . "' LIMIT 1");
                            if ($ccc && ($cff = mysqli_fetch_array($ccc))) {
                                $cityName = trim((string) ($cff['city_name'] ?? ''));
                            }
                        }
                        if (!empty($templeRow['state'])) {
                            $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . mysqli_real_escape_string($DatabaseCo->dbLink, $templeRow['state']) . "' AND country_code='" . mysqli_real_escape_string($DatabaseCo->dbLink, $templeRow['country'] ?? '') . "' LIMIT 1");
                            if ($sss && ($fff = mysqli_fetch_array($sss))) {
                                $stateName = trim((string) ($fff['state_name'] ?? ''));
                            }
                        }

                        $placeLine = $cityName;
                        if ($stateName !== '') {
                            $placeLine .= ($placeLine !== '' ? ', ' : '') . $stateName;
                        }
                    ?>
                    <div class="listing">
                        <a href="iconic-details.php?id=<?php echo $templeId; ?>">
                            <img src="<?php echo htmlspecialchars($photoSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $title; ?>" onerror="this.onerror=null;this.src='assets/images/default-image.png';">
                        </a>
                        <div class="listing-details">
                            <a href="iconic-details.php?id=<?php echo $templeId; ?>">
                                <div class="listing-title">
                                    <?php echo $title; ?>
                                    <?php if ($placeLine !== '') : ?>, <?php echo htmlspecialchars($placeLine, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                </div>
                            </a>
                            <div class="listing-rating text-dark"><a href="iconic-details.php?id=<?php echo $templeId; ?>">Read more</a></div>
                        </div>
                    </div>
                <?php } ?>
        </div>
    </div>

</div>

        <?php } ?>

        


           

<!-- Main content area to print, copy, or share -->

<div id="printable-content">

    <div class="py-5">

    <div class=" scroll">

    <div class="row iconic-main-content">

        <?php

        // Fetch temple details for the provided id

        $select = "SELECT * FROM `iconic` WHERE index_id='$id'";

        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



        // Check if the query returns a result

        if (mysqli_num_rows($SQL_STATEMENT) > 0) {

            $Row = mysqli_fetch_object($SQL_STATEMENT);

        } else {

            echo "<p>Temple not found.</p>";

            exit;

        }

        ?>

        <!-- Main Content Section -->

        <div class="col-12 order-first iconic-article" id="bannerTitle">

            <div class="iconic-fixed-actions hidePrint">
                <a class="btn btn-primary voicebtn" href="javascript:void(0)" onclick="printContent()">
                    <i class="fas fa-print"></i> Print
                </a>
                <button id="toggleIcon" class="btn voicebtn voicebtn-play" type="button">
                    <i class="fa fa-play"></i> Play
                </button>
            </div>

            <div class="iconic-head">

                <h1 class="fs-1"><?php echo $Row->speciality; ?></h1>

            </div>

            <?php if (!empty(trim((string) ($Row->small_description ?? '')))) : ?>
            <div class="iconic-subhead">

                <?php echo $Row->small_description; ?>

            </div>
            <?php endif; ?>

            <div class="text-dark icon-desc"><?php echo $Row->description; ?></div>

    </div>

    <div class="col-12 col-lg-3 print-disable filters-col iconic-sidebar ps-lg-4 ps-xl-5 ">

            <!-- Social Media Icons -->

           

 <?php if($Row->map_image != ''){?><div class="print-disable"><img class="img-responsive" src="app/uploads/iconic/map/<?php echo $Row->map_image; ?>" alt=""></div><?php }?>

        <div class="gmap-class">

            <?php

    // Fetch all rows that match the given categories_id

    $select = "SELECT * FROM `iconic_temples` WHERE categories_id = '$id'";

    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



    // Prepare a locations array for JavaScript

    $locations = [];

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

            $locations[] = [

                'id' => $Row['index_id'],

                'name' => $Row['title'],

                'address' => $Row['address'],

                'latitude' => $Row['latitude'] ?? null,

                'longitude' => $Row['longitude'] ?? null,

                'photos' => $Row['photos']

            ];

        }

    }

    ?>

    <?php if($address !=''){?>

            <div class="border p-2 rounded-4 shadow-sm mt-5">

    

    <!-- HTML Map Section -->

    <h4 class="mb-0">Temple <span class="text-primary">Location</span></h4>

    <div id="map" style="width: 100%; height: 500px;"></div>

</div>



    <?php }?>



        </div>

        </div>

            </div> 

</div>

<div class="container icon-footer">

                                                    <div style="padding: 10px; text-align: left">

                                                        <h4>

                                                            More Temples to Know More

                                                        </h4>

                                                        

                                                    </div>

                                                    <div class="row p-2">

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="iconic-category-details.php?id=63">

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/nagafoot.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Nagadevata Temples to releive from Nagadoshas

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="iconic-category-details.php?id=60">

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/tree1.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Nakshatra Trees &amp; Temples to know associated tree...

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="iconic-category-details.php?id=80">

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/swambhu1.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Swayambhu Temples where the Deity appears to...

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="mystery.php">

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/mystry1.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Mystery Temples to know the unsolved mysteries

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="iconic-category-details.php?id=61">

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/nagadosa.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Pariharam Temples to relieve from Graha doshas

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                        <div class="col-sm-4 px-4 py-3">

                                                            <table style="height: 100%; width: 100%">

                                                                <tbody><tr>

                                                                    <td>

                                                                        <div class="pi-pic">

                                                                            <div>

                                                                                <a href="#0" >

                                                                                    <div class=" ">

                                                                                        <img src="assets/images/mukti.jpg" style="width: 100%">

                                                                                    </div>

                                                                                    <div style="text-align: center">

                                                                                        Mukthi Skshetras to attain Moksha

                                                                                    </div>

                                                                                </a>

                                                                            </div>

                                                                        </div>

                                                                    </td>

                                                                </tr>

                                                            </tbody></table>

                                                        </div>

                                                    </div>

                                                </div>

<div class="container print-disable iconic-comments-wrap mt-4 d-none">
    <div class="row">
        <div class="col-12">
            <div class="comment-box mt-3">
                <h4>Leave a Comment</h4>
                <div class="alert alert-success mt-3 d-none" id="success-message">Comment successfully submitted and is pending approval!</div>
                <form action="" method="post" id="submit-comment">
                    <div class="form-group mb-3">
                        <p>Name</p>
                        <input type="text" class="form-control" id="name" placeholder="Your Name" required>
                    </div>
                    <div class="form-group">
                        <p>Comment</p>
                        <textarea class="form-control" id="comment" rows="4" placeholder="Your Comment" required></textarea>
                    </div>
                    <input type="hidden" name="type" id="type" value="icon-cate" />
                    <button class="btn btn-primary mt-4" type="submit">Post Comment</button>
                </form>
            </div>
            <?php
            $query = "SELECT * FROM `comments` WHERE type='icon-cate' AND is_approved=1 ORDER BY index_id DESC";
            $result = mysqli_query($DatabaseCo->dbLink, $query);
            $allComments = [];
            while ($row = mysqli_fetch_object($result)) {
                $allComments[] = $row;
            }
            $totalComments = count($allComments);
            if ($totalComments > 0) {
            ?>
            <h3 class="mt-5">Comments</h3>
            <div id="comments-section" class="comment-section">
                <?php foreach ($allComments as $ci => $Rowc) {
                    $isHidden = $ci >= 3 ? ' comment-item-hidden' : '';
                ?>
                <div class="comment-item<?php echo $isHidden; ?>" data-index="<?php echo $ci; ?>">
                    <p><strong><?php echo htmlspecialchars($Rowc->name); ?></strong> says,<br><?php echo nl2br(htmlspecialchars($Rowc->comment)); ?></p>
                    <hr>
                </div>
                <?php } ?>
            </div>
            <?php if ($totalComments > 3) : ?>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="viewMoreComments" data-show-each="3">View more</button>
            <?php endif; ?>
            <?php } ?>
        </div>
    </div>
</div>




    </div>

</div>



<!-- <div class="col-12">

    <?php

    $select = "SELECT * FROM `iconic_temples` WHERE categories_id = '$id'";

    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



    $locations = [];

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

            $locations[] = [

                'id' => $Row['index_id'],

                'name' => $Row['title'],

                'address' => $Row['address'],

                'latitude' => $Row['latitude'] ?? null,

                'longitude' => $Row['longitude'] ?? null,

                'photos' => $Row['photos']

            ];

        }

    }

    ?>

    <div id="map" style="width: 100%; height: 500px;"></div>

</div> -->

<script>

    // Pass the PHP data to JavaScript

    const locations = <?php echo json_encode($locations); ?>;



    // Google Maps initialization function

    function initAutocomplete() {

        // Initialize the map

        const map = new google.maps.Map(document.getElementById("map"), {

            center: {

                lat: 20.5937,

                lng: 78.9629

            }, // Default center to India

            zoom: 5, // Adjust zoom level

            mapTypeId: "roadmap",

            gestureHandling: "cooperative"

        });



        const geocoder = new google.maps.Geocoder();



        // Iterate through each location and add a marker

        locations.forEach((location) => {

            const fullAddress = location.address;



            // If latitude and longitude are available, use them directly

            if (location.latitude && location.longitude) {

                addMarker(

                    map, {

                        lat: parseFloat(location.latitude),

                        lng: parseFloat(location.longitude)

                    },

                    location.name,

                    location.photos,

                    location.id

                );

            } else {

                // Otherwise, geocode the address to get coordinates

                geocoder.geocode({

                    address: fullAddress

                }, (results, status) => {

                    if (status === "OK") {

                        const position = results[0].geometry.location;

                        addMarker(map, position, location.name, location.photos, location.id);

                    } else {

                        console.error(`Geocoding failed for address: ${fullAddress}, Status: ${status}`);

                    }

                });

            }

        });

    }



    // Function to add a marker to the map

    function addMarker(map, position, name, photos, id) {

        const marker = new google.maps.Marker({

            map: map,

            position: position,

        });



        // Create info window content

        const infowindowContent = `

    <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">

        <h4 class="fs-5 fw-semibold restaurant-text-truncate overflow-hidden mb-0" style="margin: 10px 0;">

            <span>${name}</span>

        </h4>

        <img src="app/uploads/iconic_temple/${photos}" alt="Temple Image" class="map-img" style="width: 100px; height: auto; margin: 10px 0;">

        <a href="iconic-details.php?id=${id}" class="fs-5 fw-semibold restaurant-text-truncate overflow-hidden mb-0"  style="margin-top: 10px;">View Details</a>

    </div>

`;

        const infowindow = new google.maps.InfoWindow({

            content: infowindowContent,

        });



        // Add click event to open the info window

        marker.addListener("click", () => {

            infowindow.open(map, marker);

        });

    }

</script>

<!-- Google Maps API Script -->

<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&libraries=places&callback=initAutocomplete"></script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>

$(document).ready(function(){

   $(".listings-carousel").owlCarousel("destroy").owlCarousel({

    loop: false,

    margin: 30,

    autoplay: true,

    autoplayTimeout: 3000,

    smartSpeed: 800,

    slideBy: 3,

    responsive: {

        0: {  // For mobile screens

            items: 1,

            slideBy: 1

        },

        768: { // For tablets and larger screens

            items: 2,

            slideBy: 2

        },

        1024: { // For desktops

            items: 3,

            slideBy: 3

        }

    }

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



<script>

    const toggleButton = document.getElementById('toggleIcon');

    const bannerTitle = document.getElementById('bannerTitle').textContent;



    // Initialize the SpeechSynthesis API

    const synth = window.speechSynthesis;

    let utterance = new SpeechSynthesisUtterance(bannerTitle);



    // Variable to track play/pause state

    let isPlaying = false;



    // Toggle play/pause functionality

    toggleButton.addEventListener('click', function() {

        if (isPlaying) {

            // Pause the content

            synth.cancel();

            toggleButton.innerHTML = '<i class="fa  fa-play"></i> Play';

        } else {

            // Play the content

            if (!synth.speaking) {

                synth.speak(utterance);

            }

            toggleButton.innerHTML = '<i class="fa fa-stop"></i> Stop';

        }

        isPlaying = !isPlaying;

    });



    // Stop speech synthesis if the page is unloaded

    window.addEventListener('beforeunload', function() {

        if (synth.speaking) {

            synth.cancel();

        }

    });



    // Reset button state when speech ends

    utterance.onend = function() {

        isPlaying = false;

        toggleButton.innerHTML = '<i class="fas fa-volume-up"></i> Play';

    };

</script>

<?php include('./include/footer.php'); ?>