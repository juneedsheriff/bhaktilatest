<?php
include('./include/header.php');
error_reporting(0);
?>
<?php 

$conn = new mysqli("localhost", "root", "", "bhakti");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$userLng = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;
$radiusKm = isset($_GET['radius']) ? intval($_GET['radius']) : 25; // default: 100 km

// Check if latitude and longitude are available
if ($userLat === null || $userLng === null || $userLat == 0 || $userLng == 0) {
   
}

// SQL with Haversine formula
$sql = "
    SELECT *, (
        6371 * acos(
            cos(radians($userLat)) *
            cos(radians(lat)) *
            cos(radians(lang) - radians($userLng)) +
            sin(radians($userLat)) *
            sin(radians(lat))
        )
    ) AS distance
    FROM temples
    HAVING distance <= $radiusKm
    ORDER BY distance ASC
    LIMIT 50
";

// Prepare and execute query
$result = mysqli_query($DatabaseCo->dbLink, $sql);

// Output results
$listings = [];
while ($row = $result->fetch_assoc()) {
    $listings[] = $row;
}


?>
<style>
    .btn-search-near-me{
    border: 1px solid #8bc34a;
    background: #8BC34A;
    color: #fff;
    border-radius: 6px;
    padding: 10px 15px;
    display: block;
    width: 100%;
    margin-top: 10px;
}
</style>
<div class="py-3 py-xl-5 bg-gradient">
    <div class="container">
        <div class="row">
            <!-- start sidebar filters -->
            <aside class="col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end ">
                <div class="js-sidebar-filters-mobile">
                   <div class="geolocation-form">
                       <form id="locationForm" method="GET" action="sample-listings.php">
                        <div>
                            <label for="radius">Select Radius:</label>
                            <select name="radius" id="radius" class="form-control">
                                <option value="" disabled <?= $radiusKm === '' ? 'selected' : '' ?>>Select radius</option>
                                <option value="5" <?= $radiusKm == '5' ? 'selected' : '' ?>>5 km</option>
                                <option value="10" <?= $radiusKm == '10' ? 'selected' : '' ?>>10 km</option>
                                <option value="25" <?= $radiusKm == '25' ? 'selected' : '' ?> >25 km</option>
                                <option value="50" <?= $radiusKm == '50' ? 'selected' : '' ?>>50 km</option>
                                <option value="100" <?= $radiusKm == '100' ? 'selected' : '' ?>>100 km</option>
                            </select>
                        </div>
                            
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <button type="submit" class="btn-search-near-me">Search Near Me</button>
                        </form>
                   </div>
                </div>
            </aside>
            <!-- end /. sidebar filters -->
            <!-- start items content -->
            <div class="col-xl-9 ps-lg-4 ps-xl-5 sidebar">
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <div class="fs-1 font-caveat page-header-title fw-bold m-2 pb-3  text-primary">Temples in India</div>
                    <!-- start button group -->

                    <!-- end /. button group -->
                </div>
                <div id="viewmore" class="listings grid-view">
                    <?php

                        if($listings){
                            foreach($listings as $temple_list => $temple){

                                $photos = $Row['photos'];
                                $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . $temple['city'] . "'");
                                $cff = mysqli_fetch_array($ccc);
                                $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . $temple['state'] . "' AND country_code='" . $temple['country'] . "'");
                                $fff = mysqli_fetch_array($sss);

                              
                                ?>

                                <?php
                               
                                $toLat = $temple['lat'];   // Destination latitude
                                $toLng = $temple['lang'];   // Destination longitude

                                $mapLink = "https://www.google.com/maps/dir/?api=1&origin={$userLat},{$userLng}&destination={$toLat},{$toLng}";
                                ?>

                                
                                <div class="listing">
                                    <a href="temple-details.php?id=<?php echo $temple['index_id']; ?> " >
                                        <a href="temple-details.php?id=<?php echo $temple['index_id']; ?>" >
                                            <img src="app/uploads/temple/<?php echo $photos; ?>" alt="">
                                        </a>
                                        <div class="listing-details">
                                            <p>Distance : <?php echo number_format($temple['distance'], 2) . ' km';?> <a href="<?= $mapLink ?>"  class="link">Get Directions</a></p>
                                            <a href="temple-details.php?id=<?php echo $temple['index_id']; ?>" >
                                                <div class="listing-title"><?php echo $temple['title'];
                                                                            echo $cff['city_name'] != '' ? ', ' : '';
                                                                            echo $cff['city_name'];
                                                                            echo  $fff['state_name'] != '' ? ', ' : '';
                                                                            echo $fff['state_name']; ?></div>
                                            </a>
                                            <!-- <a href="#"><i class="fs-501 fa-solid fa-map-location text-primary address"></i></a> -->

                                            <div class="listing-rating text-dark"><a href="temple-details.php?id=<?php echo $temple['index_id']; ?>" >Read more</a></div>
                                        </div>
                                </div>
                                <!-- Repeat for additional listings -->
                                <?php 
                            }
                        }else{
                            echo '<p>Sorry, we couldn\'t find any temples within '.$radiusKm.' KM of your location.</p>';
                        }
                            
                    ?>
                            
                        <?php

                       

                    ?>
                </div>
                <?php if($total_records>9){?>
                <div class="show_more_main m-3" id="show_more_main1" align="center">
        <span id="getID" data-id="1" data-category="<?php echo "india";?>" class="show_more btn btn-primary btn-lg" title="Load more Images">Load More</span>
        <span class="loding btn btn-info btn-lg text-white" style="display: none;"><span class="loding_txt">Loading...</span></span>
    </div><?php }?>
                <div class="row d-none">
                    <div class="col-lg-12 mt--60">
                        <nav class="custom-pagination mt-5" aria-label="Page navigation">
                            <!-- Previous Button -->
                            <?php if ($page > 1) { ?>
                                <a class="prev page-numbers" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-arrow-left-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z" />
                                    </svg>
                                    Previous
                                </a>
                            <?php } ?>

                            <!-- Page Numbers -->
                            <div class="page-links">
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <a href="?page=<?php echo $i; ?>"
                                        class="page-numbers <?php echo ($i == $page) ? 'current' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php } ?>
                            </div>

                            <!-- Next Button -->
                            <?php if ($page < $total_pages) { ?>
                                <a class="next page-numbers" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                    Next
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8z" />
                                    </svg>
                                </a>
                            <?php } ?>
                        </nav>


                    </div>
                </div>
            </div>
            <!-- end /. items content -->
        </div>
    </div>
</div>
<?php 
include('./include/footer.php');
?>
<script>
    // Get user's current location
    window.onload = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                document.getElementById("latitude").value = position.coords.latitude;
                document.getElementById("longitude").value = position.coords.longitude;
                if (!window.location.search) {
                    document.getElementById('locationForm').submit();
                }
            }, function(error) {
                alert("Location access denied. Please allow location.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    };
</script>