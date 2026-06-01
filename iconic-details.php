<?php

error_reporting(1);

include('./include/header.php');



// Include required classes

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

$id = $xssClean->clean_input($_REQUEST['id']);



// Fetch temple details for the provided id

$select = "SELECT * FROM `iconic_temples` WHERE index_id='$id'";

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



// Check if the query returns a result

if (mysqli_num_rows($SQL_STATEMENT) > 0) {

    $Row = mysqli_fetch_object($SQL_STATEMENT);

    $photo = $Row->upload_image;

    $country = $Row->country;

    $state = $Row->state;

    $city = $Row->city;

    $address = $Row->address;

} else {

    echo "<p>Temple not found.</p>";

    exit;

}



// Create the full address

$fullAddress = urlencode("$address, $city, $state, $country");

?>
<style>
/* ------------------------ */
/*      PRINT STYLE         */
/* ------------------------ */
@media print {

    /* Hide unwanted sections */
    .hidePrint,
    .sidebar,
    .social-media,
    .tab-container,
    .owl-carousel,
    #map,
    iframe,
    .comment-box,
    .review-image,
    .zoom-gallery {
        display: none !important;
    }

    /* Full width layout */
    body, html {
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact !important;
    }

    #printable-area {
        width: 100%!important;
        padding: 20px 40px!important;
        background: #ffffff!important;
        font-size: 16px!important;
        line-height: 1.6!important;
    }

    /* Header Logo */
    .print-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 10px;
        border-bottom: 3px solid #ff8776;
    }
    .print-header img {
        width: 120px;
        height: auto;
        margin-bottom: 10px;
    }
    .print-header h1 {
        font-size:20px;
        font-weight: bold;
        margin: 0;
        text-transform: uppercase;
        color: #333;
    }

    /* Section headings */
    h2 {
        font-size: 22px!important;
        margin-top: 25px!important;
        padding-bottom: 5px!important;
        border-bottom: 2px solid #ff8776!important;
        color: #000!important;
        font-weight: bold!important;
    }

    /* Normal paragraph text */
    p, span {
        font-size: 16px!important;
        color: #000!important;
    }

    /* Cards in print */
    .card {
        box-shadow: none!important;
        border: 1px solid #ddd!important;
        padding: 20px!important;
        margin-bottom: 25px!important;
        background: #fff!important;
    }

    /* Avoid page breaks inside a section */
    .card, h2, p {
        page-break-inside: avoid!important;
    }

    /* Force page break before gallery & comments */
    #gallery,
    #comments {
        page-break-before: always!important;
    }
}
</style>
<style>

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



    .custom-sticky {

        position: sticky;

        top: 0;

        /* Stick to the top of the viewport */

        z-index: 1030;

        /* Ensure it stays above other elements */

       

        /* Background to avoid transparency issues */

        padding: 10px 0;

        /* Add padding for spacing */



    }

</style>

<!-- Start gallery with print icon -->

<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">

    <div class="overflow-hidden position-relative  banner-over-container">

                    <img class="w-100" src="app/uploads/iconic_temple/banner/<?php echo $Row->banner; ?>" class="img-fluid" alt="Temple Image">

        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title; ?></h1>

    </div>

</div>



<div id="printable-content" class="bg-gradient">
    <div id="printable-area">
        <div class="py-5">
            <div class="container">

                <!-- 1) SPECIALITY TITLE SECTION -->
                <div class="row">
                    <div class="col-lg-9 mx-auto">
                        <?php if (!empty($Row->speciality_title)) : 
                            $specialities = $Row->speciality_title;
                            $items = array_map('trim', explode(',', $specialities));

                            if (count($items) > 1) {
                                $last = array_pop($items);
                                $formatted = implode(', ', $items) . ' and ' . $last;
                            } else {
                                $formatted = $items[0];
                            }
                        ?>
                        <div class="mb-5 bg-body p-4 mb-4 sth-text">
                            <h2 class="iconic-head text-center"><?php echo $formatted; ?></h2>
                            <p class="text-dark sth-text"><?php echo $Row->speciality; ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- 2) TOP MENU AND SIDEBAR -->
                <div class="row">

                    <!-- LEFT: TAB MENU (FULL WIDTH ON MOBILE) -->
                    <div class="col-lg-9">
                        <div class="tab-container text-center mb-4 hidePrint custom-sticky">
                            <div class="card rounded-4 border-0 bg-gradient">
                                <div class="row m-3">

                                    <?php if (!empty($Row->sthalam)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('Sthalam')" class="btn btn-primary">Temple Overview</button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($Row->puranam)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('Puranam')" class="btn btn-primary">Origin Story</button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($Row->varnam)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('Varnam')" class="btn btn-primary">Architecture</button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($Row->highlights)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('Highlights')" class="btn btn-primary">Mystical Beliefs</button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($Row->sevas)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('Sevas')" class="btn btn-primary">Festivals & Rituals</button>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($Row->gallery_image)) : ?>
                                        <div class="col-6 col-sm-4 col-md-auto">
                                            <button onclick="scrollToCard('gallery')" class="btn btn-primary">Photos</button>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: SIDEBAR -->
                    <div class="col-lg-3">
                        <div class="card rounded-4 border-0 bg-gradient p-3">

                            <div class="social-media hidePrint">
                                <a class="btn btn-primary d-inline-block mb-2" href="#" id="printBtn">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a class="btn btn-primary d-inline-block mb-2" href="#" onclick="shareToWhatsApp()">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- 3) MAIN CONTENT SECTIONS -->
                <div class="row mt-4">

                    <div class="col-lg-9">

                        <?php if (!empty($Row->sthalam)) : ?>
                            <div id="Sthalam" class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="text-dark caveat-text">Temple Overview</h2>
                                <p><?php echo $Row->sthalam; ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($Row->puranam)) : ?>
                            <div id="Puranam" class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="text-dark caveat-text">Origin Story</h2>
                                <p><?php echo $Row->puranam; ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($Row->varnam)) : ?>
                            <div id="Varnam" class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="text-dark caveat-text">Architecture</h2>
                                <p><?php echo $Row->varnam; ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($Row->highlights)) : ?>
                            <div id="Highlights" class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="text-dark caveat-text">Mystical Beliefs</h2>
                                <p><?php echo $Row->highlights; ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($Row->sevas)) : ?>
                            <div id="Sevas" class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="text-dark caveat-text">Festivals & Daily Rituals</h2>
                                <p><?php echo $Row->sevas; ?></p>
                            </div>
                        <?php endif; ?>

                         <!-- GALLERY RIGHT SIDE FULL WIDTH -->
                    <?php if (!empty($Row->gallery_image)): ?>
                        <div class="col-lg-12">
                            <div id="gallery" class="card shadow mb-4 p-4 bg-body text-dark hidePrint">
                                <h2 class="caveat-text">Photos</h2>
                                <div class="row mt-3 g-2 zoom-gallery">

                                    <?php
                                        $imgs = array_filter(explode(',', $Row->gallery_image));
                                        foreach ($imgs as $image):
                                            $imagePath = "app/uploads/iconic_temple/gallery/" . $image;
                                            if(file_exists($imagePath)):
                                    ?>
                                        <div class="col-lg-3 col-6">
                                            <a href="<?= $imagePath ?>" class="gallery-overlay-hover">
                                                <img src="<?= $imagePath ?>" class="img-fluid rounded-3 object-fit-cover" style="height: 250px; width:100%;">
                                            </a>
                                        </div>
                                    <?php
                                            endif;
                                        endforeach;
                                    ?>

                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                        <?php if (!empty($Row->time)) : ?>
                            <div class="card shadow mb-4 p-4 bg-body text-dark">
                                <h2 class="caveat-text mb-3">Temple Timings</h2>
                                <p><?php echo $Row->time; ?></p>
                            </div>
                        <?php endif; ?>

                        

                        <div class="card shadow mb-4 p-4 bg-body text-dark">
                            <h2 class="caveat-text mb-3">Contact</h2>
                            <p><?php echo nl2br($address); ?></p>

                            <h2 class="mb-2 caveat-text">Wheelchair Access</h2>
                            <p>Available</p>

                            <h2 class="mb-2 caveat-text">Location Information</h2>
                            <div id="locationInfoDiv2" class="hidePrint">
                                <iframe 
    width="100%" 
    height="400" 
    style="border:0"
    loading="lazy"
    allowfullscreen
    referrerpolicy="no-referrer-when-downgrade"
    src="https://www.google.com/maps?q=<?php echo $address; ?>&output=embed">
</iframe>
                            </div>
                        </div>

                    </div>


                   

                </div>

            </div>
        </div>
    </div>
</div>


<!-- <div class="container">

    <h2>Temple Location</h2>

    <p><?php echo htmlspecialchars("$address, $city, $state, $country"); ?></p>

    <div id="map" style="width: 100%; height: 400px;"></div>

</div> -->

<?php
function getTemplePrintContent($Row) {

    // Handle empty fields
    $safe = function($data) {
        return !empty($data) ? $data : "Information not available.";
    };

    // Prepare gallery
    $galleryHTML = "";
    if (!empty($Row->gallery_image)) {
        $galleryHTML .= "<h2 class='sec-head caveat-text' style=' width:fit-content;'>Gallery</h2><div>";
         
        $imgs = array_filter(explode(",", $Row->gallery_image));
        foreach ($imgs as $img) {
            $path = "app/uploads/iconic_temple/gallery/" . trim($img);
            if (file_exists($path)) {
                $galleryHTML .= "<img src='$path' style='width:200px; margin:10px; border-radius:8px; height:220px;'/>";
            }
        }
       $galleryHTML .= "</div>";
    } else {
        $galleryHTML = "<p>No gallery images available.</p>";
    }
    
    // Build full printable HTML
    $html = "
    <style>
        /* Google fonts --------------------------- */
@import url('https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap');
    p{
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
    </style>
        <div style='padding:20px; font-family:Arial;'>

            <h1 class='caveat-text' style='font-weight: 600 !important;'>{$safe($Row->title)}</h1>

            <img src='app/uploads/iconic_temple/banner/{$Row->banner}'
                 style='width:100%; max-height:300px; object-fit:cover; border-radius:8px; margin-bottom:20px;' />

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Speciality</h2>
            <p>{$safe($Row->speciality)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Temple Overview</h2>
            <p>{$safe($Row->sthalam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Origin Story</h2>
            <p>{$safe($Row->puranam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Architecture</h2>
            <p>{$safe($Row->varnam)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Mystical Beliefs</h2>
            <p>{$safe($Row->highlights)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Festivals & Daily Rituals</h2>
            <p>{$safe($Row->sevas)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Timings</h2>
            <p>{$safe($Row->time)}</p>

            <h2 class='sec-head caveat-text' style=' width:fit-content;'>Address</h2>
            <p>{$safe($Row->address)}, {$safe($Row->city)}, {$safe($Row->state)}, {$safe($Row->country)}</p>

             ".$galleryHTML."

        </div>
    ";

    return $html;
}

$printContent = getTemplePrintContent($Row);
?>
<div id="printArea" style="display:none;">
    <?php echo $printContent; ?>
</div>
<?php
// Ensure proper SQL queries are working



// Calculate total pages





$select = "SELECT * FROM `iconic_temples` WHERE index_id='$id'";

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

</script>

<!-- <script>

    // Google Maps initialization function

    function initAutocomplete() {

        // Combine the address details into one full address

        var address = "<?php echo "$address, $city, $state, $country"; ?>";



        // Initialize the Geocoder and map

        const map = new google.maps.Map(document.getElementById("map"), {

            center: {

                lat: -33.8688,

                lng: 151.2195

            }, // Default center, updated upon geocode

            zoom: 13,

            mapTypeId: "roadmap"

        });



        const geocoder = new google.maps.Geocoder();



        // Geocode the address to get coordinates

        geocoder.geocode({

            'address': address

        }, function(results, status) {

            if (status === 'OK') {

                map.setCenter(results[0].geometry.location);

                new google.maps.Marker({

                    map: map,

                    position: results[0].geometry.location

                });

            } else {

                alert('Geocode was not successful for the following reason: ' + status);

            }

        });

    }

</script> -->

<script>

    function initMap() {

        const locationInfoDiv = document.getElementById('location-info');

        const mapElement = document.getElementById('map');

        const [fromAddress, templeName] = <?php echo json_encode([$address, $temple_name]); ?>;



        const geocoder = new google.maps.Geocoder();



        // Show a loading spinner or message while waiting for the map to load

        locationInfoDiv.textContent = 'Loading map...';



        // Geocode the address to get the user's location

        geocoder.geocode({ address: fromAddress }, (results, status) => {

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

    // Ensure the data object contains a valid name before rendering

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

            <a href="https://www.google.com/maps/place/?q=place_id:${results[0].place_id}"  style="color: #007bff; text-decoration: none;">

                View on Google Maps

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

    google.maps.event.addListener(currentInfoWindow, 'domready', function() {

                const closeButton = currentInfoWindow.getContent().querySelector('.info-window-close');

                closeButton.style.display = 'none';



                currentInfoWindow.getDiv().addEventListener('mouseover', function() {

                    closeButton.style.display = 'inline-block';

                });



                currentInfoWindow.getDiv().addEventListener('mouseout', function() {

                    closeButton.style.display = 'none';

                });

            });

    currentInfoWindow.open(map, userMarker); // Open the new info window

});





                // Extract city and area/place from the geocoded results

                const components = results[0].address_components;

                let city = "", area = "";

                components.forEach((component) => {

                    if (component.types.includes("locality")) {

                        city = component.long_name;

                    }

                    if (component.types.includes("sublocality") || component.types.includes("neighborhood")) {

                        area = component.long_name;

                    }

                });



                const formattedLocation = `${area ? area + ", " : ""}${city}`;

                //document.title = `Nearby Temples - ${formattedLocation}`;



                // Search for nearby temples

                const service = new google.maps.places.PlacesService(map);

                const request = {

                    location: userLocation,

                    radius: 50000, // 50 km radius

                    type: ['hindu_temple'], // Specify "Hindu temples"

                };



                service.nearbySearch(request, (results, status) => {

                    if (status === google.maps.places.PlacesServiceStatus.OK) {

                        const infoWindows = []; // To hold open info windows



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



                            placeMarker.addListener('mouseover', () => {

                                // Remove any existing custom card

                                if (customCardElement) {

                                    customCardElement.remove();

                                }



                                // Create the custom card container

                                customCardElement = document.createElement('div');

                                customCardElement.innerHTML = `

                                    <div style="

                                        font-family: Arial, sans-serif; 

                                        font-size: 10px; 

                                        line-height: 1.4; 

                                        width: 250px; /* Set a smaller card width */

                                        display: flex; 

                                        flex-direction: column; 

                                        align-items: center; 

                                        text-align: center; 

                                        box-sizing: border-box; 

                                        padding: 10px; 

                                        margin: 10px auto; 

                                        border: 1px solid #ddd; 

                                        border-radius: 8px; 

                                        background-color: #fff; 

                                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 

                                        position: absolute; 

                                        z-index: 1000;">

                                        

                                        <!-- Card Header -->

                                        <strong style="font-size: 12px; color: #d9534f; text-transform: capitalize;">

                                            ${place.name}

                                        </strong>

                                        <br>

                                        

                                        <!-- Vicinity -->

                                        <span style="color: #555; font-size: 11px;">

                                            ${place.vicinity}

                                        </span>

                                        <br>

                                        

                                        <!-- Link -->

                                        <a href="https://www.google.com/maps/place/?q=place_id:${place.place_id}" 

                                            

                                           style="color: #007bff; text-decoration: none; font-size: 12px; margin-top: 5px;">

                                           View on Google Maps

                                        </a>

                                        

                                        <!-- Close Button -->

                                        <button onclick="this.parentElement.remove(); customCardElement = null;" 

                                                style="

                                                position: absolute; 

                                                top: 5px; 

                                                right: 5px; 

                                                width: 20px; 

                                                height: 20px; 

                                                background: none; 

                                                border: none; 

                                                color: #000; 

                                                font-size: 25px; 

                                                font-weight: 600;

                                                cursor: pointer; 

                                                line-height: 1;">

                                            &times;

                                        </button>

                                    </div>`;



                                // Position the card near the marker

                                const markerPosition = placeMarker.getPosition();

                                const projection = map.getProjection();

                                const markerPoint = projection.fromLatLngToPoint(markerPosition);

                                const container = document.querySelector('#map'); // Ensure your map container has an ID

                                const containerOffset = container.getBoundingClientRect();



                                // Convert map position to pixel position

                                const x = markerPoint.x * Math.pow(2, map.getZoom()) - containerOffset.left;

                                const y = markerPoint.y * Math.pow(2, map.getZoom()) - containerOffset.top;



                                // Set position

                                customCardElement.style.left = `${x}px`;

                                customCardElement.style.top = `${y}px`;



                                // Add to the map container

                                container.appendChild(customCardElement);

                            });



                            // Consistent hover effect for marker icons

                            placeMarker.addListener('mouseover', () => {

                                placeMarker.setIcon({

                                    url: 'https://example.com/images/hover_red_om_symbol.png', // Larger red Om symbol for hover

                                    scaledSize: new google.maps.Size(50, 50), // Larger size for hover

                                });

                            });



                            placeMarker.addListener('mouseout', () => {

                                placeMarker.setIcon({

                                    url: './assets/images/temple-icon.png', // Reset to default red Om symbol

                                    scaledSize: new google.maps.Size(40, 40),

                                });

                            });

                        });

                    } else {

                        locationInfoDiv.textContent += "No nearby temples found.";

                    }

                });

            } else {

                locationInfoDiv.textContent = "Unable to fetch your location. Please check the address.";

                //document.title = "Nearby Temples";

            }

        });

    }



   (function() {
    function initPrint() {
        var printBtn = document.getElementById("printBtn");
        var printArea = document.getElementById("printArea");
        if (!printBtn || !printArea) return;

        printBtn.addEventListener("click", function (e) {
            e.preventDefault();
            var printContents = printArea.innerHTML;
            var printWindow = window.open('', '', 'width=900,height=650');
            if (!printWindow) {
                alert('Please allow popups to print.');
                return;
            }
            printWindow.document.write('<html><head><title>Print Temple</title><' + '/head><body>' + printContents + '<' + '/body><' + '/html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrint);
    } else {
        initPrint();
    }
})();

</script>







<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&libraries=places&callback=initMap"></script>

<?php include('./include/footer.php'); ?>