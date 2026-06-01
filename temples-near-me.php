<?php
include('./include/header.php');
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
?>
<?php 

// $conn = new mysqli("localhost", "root", "", "bhakti");
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

$userLat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
$userLng = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;
$min_distance = isset($_GET['min_distance']) ? intval($_GET['min_distance']) : 25; // default: 100 km
$max_distance = isset($_GET['max_distance']) ? intval($_GET['max_distance']) : 100;

 $listings = false;
// Check if latitude and longitude are available
if ($userLat != null || $userLng != null || $userLat != 0 || $userLng != 0) {
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
    HAVING distance BETWEEN $min_distance AND $max_distance
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

}

// SQL with Haversine formula



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
.link{
    text-decoration: underline;
    font-size: 14px;
    margin-left: 10px;
    color: #ff8777;
}
</style>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
  #slider-range { margin: 20px 0; }
  .tooltip {
    position: absolute;
    background: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    transform: translate(-50%, -120%);
    white-space: nowrap;
  }
</style>


<div class="py-3 py-xl-5 bg-gradient">
    <div class="container">
        <div class="row">
            <!-- start sidebar filters -->
            <aside class="col-xl-3 filters-col content pe-lg-4 pe-xl-5 shadow-end ">
                <div class="js-sidebar-filters-mobile">
                   <div class="geolocation-form">
                       <form id="locationForm" method="GET" action="temples-near-me.php">
                        <div>
                            <label for="radius">Select Radius:</label>
                              <div id="slider-range"></div>
<div>
  <span id="amount"></span>
</div>

                            <!-- <select name="radius" id="radius" class="form-control">
                                <option value="" disabled <?= $radiusKm === '' ? 'selected' : '' ?>>Select radius</option>
                                <option value="5" <?= $radiusKm == '5' ? 'selected' : '' ?>>5 km</option>
                                <option value="10" <?= $radiusKm == '10' ? 'selected' : '' ?>>10 km</option>
                                <option value="25" <?= $radiusKm == '25' ? 'selected' : '' ?> >25 km</option>
                                <option value="50" <?= $radiusKm == '50' ? 'selected' : '' ?>>50 km</option>
                                <option value="100" <?= $radiusKm == '100' ? 'selected' : '' ?>>100 km</option>
                                <option value="500" <?= $radiusKm == '500' ? 'selected' : '' ?>>500 km</option>
                            </select> -->
                        </div>
                            
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="min_distance" id="min_distance">
                            <input type="hidden" name="max_distance" id="max_distance">
                            <button type="submit" class="btn-search-near-me">Search Near Me</button>
                        </form>
                   </div>
                </div>
            </aside>
            <!-- end /. sidebar filters -->
            <!-- start items content -->
            <div class="col-xl-9 ps-lg-4 ps-xl-5 sidebar">
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <div class="fs-1 font-caveat page-header-title fw-bold m-2 pb-3  text-primary">Temples Near Me</div>
                    <!-- start button group -->

                    <!-- end /. button group -->
                </div>
                <div id="viewmore" class="listings grid-view">
                    <?php
                    if ($userLat != null || $userLng != null || $userLat != 0 || $userLng != 0) {
                        if($listings){
                            foreach($listings as $temple_list => $temple){
                               
                                $photos = $temple['photos'];
                                if($temple['city']!=""){
                                    $ccc = $DatabaseCo->dbLink->query("SELECT city_name FROM `city` WHERE city_id='" . $temple['city'] . "'");
                                    $cff = mysqli_fetch_array($ccc);
                                }else{
                                    $cff = false;
                                }
                                if($temple['state']!="" and $temple['country'] !=""){
                                    $sss = $DatabaseCo->dbLink->query("SELECT state_name FROM `state` WHERE state_code='" . $temple['state'] . "' AND country_code='" . $temple['country'] . "'");
                                    $fff = mysqli_fetch_array($sss);
                                }else{
                                    $fff = false;
                                }

                              
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
                                            <p>Distance : <?php echo number_format($temple['distance'], 2) . ' km';?> <a href="<?= $mapLink ?>"  class="link">Get Directions <i class="fa fa-arrow-right"></i></a></p>
                                            <a href="temple-details.php?id=<?php echo $temple['index_id']; ?>" >
                                                <div class="listing-title"><?php echo $temple['title'];
                                                                            if($cff){
                                                                            echo $cff['city_name'] != '' ? ', ' : '';
                                                                             echo $cff['city_name'];
                                                                            }
                                                                           
                                                                           if($fff){
                                                                            echo  $fff['state_name'] != '' ? ', ' : '';
                                                                            echo $fff['state_name']; 
                                                                           }
                                                                        ?>
                                                                        </div>
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
                    }else{
                            echo '<p>Sorry, we couldn\'t find any temples within '.$radiusKm.' KM of your location.</p>';
                        }
                            
                    ?>
                            
                    
                </div>
               

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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
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

<script>
$(function () {
  const minVal = 0;
  const maxVal = 500;
  const minDistance = 50;

  $("#slider-range").slider({
    range: true,
    min: minVal,
    max: maxVal,
    values: [<?php echo $min_distance;?>, <?php echo $max_distance;?>],
    slide: function (event, ui) {
      if (ui.values[1] - ui.values[0] < minDistance) {
        return false;
      }

      $("#amount").text(ui.values[0] + "KM - " + ui.values[1]+ " KM");
      updateTooltip(ui.values[0], ui.values[1]);
    },
    create: function (event, ui) {
      $("#amount").text(
        $(this).slider("values", 0) + "KM - " + $(this).slider("values", 1)+" KM"
      );

      // Create tooltips
      let handles = $("#slider-range .ui-slider-handle");
      handles.each(function () {
        $(this).append('<div class="tooltip"></div>');
      });
      updateTooltip($(this).slider("values", 0), $(this).slider("values", 1));
    }
  });

  function updateTooltip(val1, val2) {
    let handles = $("#slider-range .ui-slider-handle");
    $(handles[0]).find(".tooltip").text( val1+" KM");
    $(handles[1]).find(".tooltip").text( val2+" KM");
    $("#min_distance").val(val1);
    $("#max_distance").val(val2);
  }
});
</script>