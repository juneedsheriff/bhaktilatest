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
$select = "SELECT * FROM `temples` ";
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

?>

<style>
    .sticky {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    z-index: 8 !important;
}
</style>
<div class="border-0 card header rounded-0 sticky">
    <!-- start header search bar  -->
    <div class="border-bottom border-top p-3 p-xl-0 search-bar">
        <div class="row g-3 g-xl-0">
            <!-- search bar title -->
            <div class="col-12 d-xl-none">
                <div class="collapse show" id="CollapseText">
                    <!-- <h2 class="fw-semibold text-center search-bar-title mb-0">Find what<br> you <span class="font-caveat text-primary">want</span></h2> -->
                </div>
            </div>
            <div class="col-md-8 col-lg-12 col-xl-12 text-center">
                <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Nearby Temples</div>
                <!-- Search Select Input Container -->
                <div class="search-select-input has-icon position-relative d-flex align-items-center">

                    <!-- Icon Section -->
                    <div class="icon-container position-absolute top-50 translate-middle-y ">

                    </div>

                    <!-- Text Section -->
                    <div class="text-container flex-grow-1 d-flex justify-content-center text-center d-none">
                        <div class="">
                            <svg
                                class="form-icon-start bi bi-pin-map-fill mt-3"
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z"></path>
                                <path fill-rule="evenodd" d="M4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"></path>
                            </svg>
                        </div>

                        <div>

                            <p class="fw-semibold fs-5  ms-3">Your Current Location :<small id="location-info"></small>
                            </p>
                        </div>

                    </div>

                </div>
            </div>




        </div>
    </div>
    <!-- end /. header search bar  -->
</div>

<div class="map-wrapper">

    <!-- Sidebar Filters -->

    <!-- Map -->
    <div id="map" style="width: 100%; height: 500px;"></div>
</div>

<?php include('./include/footer.php'); ?>

<script>
function initMap() {
    const locationInfoDiv = document.getElementById('location-info');

    // Clear any previous location info and inform the user
    locationInfoDiv.textContent = "Please allow location access to find nearby temples.";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const userLocation = {
                    lat: latitude,
                    lng: longitude
                };

                // Initialize the map centered on user's location
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: userLocation,
                    zoom: 10,
                });

                // Add a marker for the user's location
                const userMarker = new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Location",
                });

                // Fetch nearby temples
                const service = new google.maps.places.PlacesService(map);
                const request = {
                    location: userLocation,
                    radius: 50000, // 50 km radius
                    type: ['hindu_temple'], // Search for Hindu temples
                };

                service.nearbySearch(request, (results, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        let currentInfoWindow = null;

                        results.forEach((place) => {
                            // Create a marker for each temple
                            const placeMarker = new google.maps.Marker({
                                position: place.geometry.location,
                                map: map,
                                title: place.name,
                                icon: {
                                    url: './assets/images/temple-icon.png',
                                    scaledSize: new google.maps.Size(40, 40),
                                },
                            });

                            // Add click event listener to display a custom InfoWindow
                            placeMarker.addListener('click', () => {
                                // Close the currently open InfoWindow (if any)
                                if (currentInfoWindow) {
                                    currentInfoWindow.close();
                                }

                                // Create custom InfoWindow content with a visible close button
                                const content = document.createElement('div');
                                content.style.fontFamily = 'Arial, sans-serif';
                                content.style.fontSize = '12px';
                                content.style.lineHeight = '1.4';
                                content.style.textAlign = 'center';
                                content.style.margin = '0';

                                content.innerHTML = `
                                    <div style="margin: 0;">
                                        <strong style="color: #d9534f; text-transform: capitalize;">${place.name}</strong><br>
                                        <span style="color: #555;">${place.vicinity}</span><br>
                                        <a href="https://www.google.com/maps/place/?q=place_id:${place.place_id}" 
                                            
                                           style="color: #007bff; text-decoration: none; display: block; margin-top: 5px;">
                                           View on Google Maps
                                        </a>
                                    </div>
                                `;

                                const closeButton = document.createElement('button');
                                closeButton.innerText = '×';
                                closeButton.style.position = 'absolute';
                                closeButton.style.top = '5px';
                                closeButton.style.right = '5px';
                                closeButton.style.background = 'none';
                                closeButton.style.border = 'none';
                                closeButton.style.fontSize = '16px';
                                closeButton.style.cursor = 'pointer';
                                closeButton.style.color = '#d9534f';

                                closeButton.addEventListener('click', () => {
                                    if (currentInfoWindow) {
                                        currentInfoWindow.close();
                                    }
                                });

                                // Wrap content and close button in a container
                                const wrapper = document.createElement('div');
                                wrapper.style.position = 'relative';
                                wrapper.style.padding = '10px';
                                wrapper.style.background = '#fff';
                                wrapper.style.border = '1px solid #ddd';
                                wrapper.style.borderRadius = '8px';
                                wrapper.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
                                wrapper.style.minWidth = '200px';
                                wrapper.style.margin = '0';

                                wrapper.appendChild(closeButton);
                                wrapper.appendChild(content);

                                // Create a new InfoWindow
                                currentInfoWindow = new google.maps.InfoWindow({
                                    content: wrapper,
                                });

                                // Open the InfoWindow
                                currentInfoWindow.open(map, placeMarker);
                            });
                        });
                    } else {
                        locationInfoDiv.textContent += "No nearby temples found.";
                    }
                });
            },
            (error) => {
                const errorMessage = {
                    1: "Location access denied. Please enable location access in your browser settings.",
                    2: "Location information is unavailable.",
                    3: "The request to get your location timed out.",
                };
                locationInfoDiv.textContent = `Error: ${errorMessage[error.code] || error.message}`;
            }
        );
    } else {
        locationInfoDiv.textContent = 'Geolocation is not supported by your browser.';
    }
}

</script>
<!-- Include Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&libraries=places&callback=initMap" async defer></script>