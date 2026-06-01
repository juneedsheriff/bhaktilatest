<?php ob_start();

include('./include/header.php');

error_reporting(1);

// Include required classes

include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

// Check if god_id is set to 7 in the URL

$god_id = isset($_GET['god_id']) ? (int)$_GET['god_id'] : null;



// Define the SQL query based on the presence of god_id=7

if ($god_id === 7) {

    $select_query = "SELECT * FROM iconic WHERE god_id = 7";

} else {

    $select_query = "SELECT * FROM iconic";

}

$id = $xssClean->clean_input($_REQUEST['id']);

$select_query2 = "SELECT * FROM iconic_temples WHERE categories_id = '$id'";

$SQL_STATEMENT2 = mysqli_query($DatabaseCo->dbLink, $select_query2);

$category_info = "SELECT * FROM iconic WHERE index_id = '$id'";

if (mysqli_num_rows($SQL_STATEMENT2) > 0) {

    $select_query3 = "SELECT * FROM iconic WHERE god_id='$id'";

    $SQL_STATEMENT3 = mysqli_query($DatabaseCo->dbLink, $select_query3);

    $Rowico = mysqli_fetch_object($SQL_STATEMENT3);

    $SQLCatInfo = mysqli_query($DatabaseCo->dbLink, $category_info);

    $catinfo = mysqli_fetch_object($SQLCatInfo);

    $count_result = mysqli_query($DatabaseCo->dbLink, "SELECT COUNT(*) AS total FROM iconic_temples WHERE categories_id=" . (int)$id);

    $listings_count = mysqli_fetch_assoc($count_result)['total'];

} else {

    header("location:iconic-category.php");

}

?>



<style>

    .card-img-wrap img {

        width: 100%;

        height: 100%;

        object-fit: cover;

    }

</style>

<div class="py-3 py-xl-5 bg-gradient">

    <div class="container">

        <div class="row">

            <!-- start items content -->

            <div class="col-xl-12 ps-xl-5 sidebar ">

                <div class="d-flex flex-wrap align-items-center text-center mb-3 gap-2">

                    <h2 class="fs-1 font-caveat page-header-title text-center fw-semibold m-2 pb-3  text-primary">Iconic Temples - <?php echo htmlspecialchars($catinfo->title); ?> <span id="listings-count" class="">- <?php echo (int)$listings_count; ?></span></h2>

                    <!-- start button group -->



                    <!-- end /. button group -->

                </div>

                <div id="listings-container" class="row">

                    <?php

                    $select = "SELECT * FROM iconic_temples WHERE categories_id=$id ORDER BY order_by ASC";

                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



                    // Check if records are available

                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                            $photos = $Row['photos'];

                            $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . $Row['city'] . "'");

                            $cff = mysqli_fetch_array($ccc);

                            $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . $Row['state'] . "' AND country_code='" . $Row['country'] . "'");

                            $fff = mysqli_fetch_array($sss);

                    ?>

                            <div class="col-md-4 col-sm-6">

                                <a href="iconic-details.php?id=<?php echo $Row['index_id']; ?>" class="d-block">

                                    <?php
                                    $imgUrl = !empty($photos) ? 'app/uploads/iconic_temple/' . htmlspecialchars($photos) : 'assets/images/default-image.png';
                                    ?>
                                    <div class="listing-img-bg" style="height: 290px; background-image: url('<?php echo $imgUrl; ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>

                                    <div class="listing-details">

                                        <a href="iconic-details.php?id=<?php echo $Row['index_id']; ?>" >

                                            <div class="listing-title"><?php echo $Row['title'];

                                                                        echo $cff['city_name'] != '' ? ', ' : '';

                                                                        echo $cff['city_name'];

                                                                        echo  $fff['state_name'] != '' ? ', ' : '';

                                                                        echo $fff['state_name']; ?></div>

                                        </a>

                                        <!-- <a href="#"><i class="fs-501 fa-solid fa-map-location text-primary address"></i></a> -->



                                        <div class="listing-rating text-dark"><a href="iconic-details.php?id=<?php echo $Row['index_id']; ?>" >Read more</a></div>

                                    </div>

                                </a>

                            </div>

                            <!-- Repeat for additional listings -->

                        <?php



                        }

                    } else {



                        ?>

                    <?php

                        echo "";

                    }



                    ?>

                </div>

            </div>

            <!-- end /. items content -->

        </div>

    </div>

</div>

<?php include_once './include/footer.php' ?>

<script>

    // Load states when a country is selected

    $('#country').change(function() {

        let countryCode = $(this).val();

        $.ajax({

            url: './app/get_states.php',

            type: 'POST',

            data: {

                country_code: countryCode

            },

            success: function(response) {

                $('#state').html(response);

                $('#city').html('<option selected disabled>Select City</option>'); // Reset city dropdown

            }

        });

    });



    // Load cities when a state is selected

    $('#state').change(function() {

        let stateCode = $(this).val();

        $.ajax({

            url: './app/get_cities.php',

            type: 'POST',

            data: {

                state_code: stateCode

            },

            success: function(response) {

                $('#city').html(response);

            }

        });

    });

</script>



<script>

    // Assuming temples is a PHP array passed to JS

    const temples = <?php echo json_encode($temples); ?>; // This passes the array to JS



    // Initialize map

    let map;

    const geocoder = new google.maps.Geocoder();



    function initMap() {

        // Default map options

        const options = {

            center: {

                lat: 20.5937,

                lng: 78.9629

            }, // Centered on India// Set default map center

            zoom: 5,

        };



        map = new google.maps.Map(document.getElementById("map"), options);



        // Check if temples array is correctly passed

        console.log(temples); // Debugging temples array



        // Loop through temple addresses and add markers

        temples.forEach(temple => {

            if (temple.address) { // Ensure address exists

                // Call geocodeAddress function for each temple

                geocodeAddress(temple.address, temple.name, temple.photos, temple.id);



            } else {

                console.log('No address for temple:', temple.name);

            }

        });

        setupPlaceSearch();

    }



    // Function to geocode a single address and place a marker

    function geocodeAddress(address, name, photos, id) {

        console.log("Geocoding address: ", address); // Debugging the address



        // Use the Google Maps Geocoder service

        const geocoder = new google.maps.Geocoder();



        geocoder.geocode({

            address: address

        }, function(results, status) {

            if (status === 'OK') {

                const location = results[0].geometry.location;



                console.log("Coordinates fetched: ", location.lat(), location.lng());



                // Place marker on the map at the fetched coordinates

                const marker = new google.maps.Marker({

                    map: map,

                    position: location

                });



                // Center the map to the location

                map.setCenter(location);



                // Create the info window content with temple name, image, and a link to the details page

                const infowindowContent = `

                <h4 class="fs-5 fw-semibold restaurant-text-truncate overflow-hidden mb-0" style="margin-left:5px;">

                <span style="padding:50px;">${name}</span></h4><br>

                    <img src="app/uploads/iconic_temple/${photos}" alt="Temple Image" class="map-img"><br>

                    <a href="iconic-details.php?id=${id}" align="center" class="fs-5 fw-semibold restaurant-text-truncate overflow-hidden mb-0" >View Details</a><br>

                   

                `;



                // Create an info window with the content

                const infowindow = new google.maps.InfoWindow({

                    content: infowindowContent

                });



                // Add click listener to open the info window when the marker is clicked

                marker.addListener('click', function() {

                    infowindow.open(map, marker);

                });

            } else {

                console.error('Geocode was not successful for the following reason: ' + status);

            }

        });

    }









    // Initialize the map immediately when the page loads

</script>

<script>

    function clearPage() {

        location.reload(iconic.php); // Reloads the current page, clearing any changes

    }

</script>

<script>

    // Initialize Select2 dropdown



    // Function to get geolocation

    function getGeolocation() {

        if (navigator.geolocation) {

            navigator.geolocation.getCurrentPosition(function(position) {

                const lat = position.coords.latitude;

                const lng = position.coords.longitude;

                document.getElementById('location').innerHTML = `Latitude: ${lat}, Longitude: ${lng}`;

                performActionBasedOnLocation(lat, lng);

            }, function(error) {

                alert("Geolocation failed: " + error.message);

            });

        } else {

            alert("Geolocation is not supported by this browser.");

        }

    }



    // Example function to perform action based on geolocation and category

    function performActionBasedOnLocation(lat, lng) {

        const selectedCategory = document.getElementById("place-select").value;



        if (selectedCategory !== 'all') {

            // Example action: You could call an API or filter results based on the category and location

            alert(`Category: ${selectedCategory}, Location: Latitude ${lat}, Longitude ${lng}`);

            // Further logic like fetching nearby places based on the selected category

        } else {

            alert(`Category: All Categories, Location: Latitude ${lat}, Longitude ${lng}`);

        }

    }

</script>

<!-- Google Maps API Script -->

<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&callback=initMap"></script>

<!-- Google Maps API Script -->



<script>

    // Fetch filtered and paginated listings, with all data shown by default on page load

    function fetchListings(page = 1) {

        // Get all checked filters

        let selectedFilters = [];

        document.querySelectorAll('.form-check-input:checked').forEach(checkbox => {

            selectedFilters.push(checkbox.value);

            console.log(selectedFilters);

        });



        // Prepare the request parameters

        const params = new URLSearchParams();

        params.append('page', page);



        // Only add selectedFilters to the request if checkboxes are selected

        if (selectedFilters.length > 0) {

            params.append('selectedFilters_iconic_temple', selectedFilters.join(','));

        }



        // Send selected filters and page number to the server

        const xhr = new XMLHttpRequest();

        xhr.open('POST', 'fetch_listings.php', true);

        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {

            if (xhr.status === 200) {

                // Update listingsContainer with the response data

                const response = JSON.parse(xhr.responseText);

                document.getElementById('listings-container').innerHTML = response.listings;

                const paginationEl = document.getElementById('paginationControls');

                if (paginationEl && response.pagination) paginationEl.innerHTML = response.pagination;

            }

        };

        xhr.send(params.toString());

    }



    // Event listeners for checkboxes to re-fetch listings when any checkbox changes

    document.querySelectorAll('.form-check-input').forEach(checkbox => {

        checkbox.addEventListener('change', () => fetchListings());

    });



    // Initial fetch for page load (shows all data initially)

    fetchListings();

</script>