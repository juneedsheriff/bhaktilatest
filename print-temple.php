<?php
error_reporting(1);
include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

$id = $xssClean->clean_input($_REQUEST['id']);

// Fetch temple details for the provided id

$select = "SELECT * FROM `temples` WHERE index_id='$id'";

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



// Check if the query returns a result

if (mysqli_num_rows($SQL_STATEMENT) > 0) {

    $Row = mysqli_fetch_object($SQL_STATEMENT);

    $photo = $Row->photos;

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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $temple['title'] ?> - Print View</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/css.css" rel="stylesheet">

  <style>
    body{
          font-family: georgia;
    }
    .temple-img {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 6px;
    }

    .logo {
      max-height: 60px;
    }

    h2,h2{
        border-bottom: 3px solid #ff8776;
        width: fit-content;
        font-size:24px;
        margin-bottom:15px;
    }
    .content-section{
        padding: 15px;
        background: #fff;
        border-radius: 10px;
        margin-bottom:30px;
    }
    .content-section p:nth-last-child(1){
      margin-bottom:0px;
    }
    .container{
      width:100% !important;
        max-width:100% !important;
    }
    .banner-over-container{
      text-align:center;
    }
    @media print {
      .container{
        width:100% !important;
        max-width:100% !important;
      }
      .no-print {
        display: none !important;
      }
      body {
        margin: 0 !important;
            font-family: georgia !important;
      }
    .logo {
      max-height: 60px !important;
    }
    .banner-over-container{
      text-align:center;
    }
    h2,h2{
        border-bottom: 3px solid #ff8776 !important;
        width: fit-content !important;
        margin-bottom:15px !important;
    }
    .content-section{
        padding: 15px !important;
        background:#fff !important;
        border-radius: 10px !important;
        margin-bottom:15px !important;
        border:1px solid rgb(231, 231, 231);
    }
    .content-section p:nth-last-child(1){
      margin-bottom:0px !important;
    }
      @page {
        margin: 0;
      }
      .page-break {
        page-break-before: always; /* start on new page */
        margin-top: 40px; /* add 40px space from top on new pages */
      }

      .gallery img{
          width: 100%;           /* or 100%, or any fixed size */
  height: 120px;          /* same as width to make square */
  object-fit: cover;      /* crops and centers the image */
  border-radius: 8px;     /* optional for rounded corners */
  margin: 5px; 
      }
    }
  </style>
</head>
<body class="bg-light">

  <!-- Header -->
  <div class="container my-3">
    <img src="https://bhaktiuat.webtechsoftwaresolutions.com/assets/images/logo/bakthi-logo.png" class="logo mb-2" alt="Logo" />
    <h2 class="fw-bold"><?php echo $Row->title; ?></h2>
    <p class="text-muted"><?php echo $Row->address; ?></p>
  </div>

  <!-- Main Content -->
  <div class="container bg-white p-4 shadow rounded mb-4">

    <div class="overflow-hidden position-relative  banner-over-container">

        <img class="w-100 banner-h-420" src="app/uploads/temple/banner/<?php echo $Row->banner; ?>" style="" alt="Temple Image">

        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title; ?></h1>

    </div>

    <?php if (!empty($Row->speciality_title)) : ?>
    <div class="content-section">
      <h2><?php echo $Row->speciality_title; ?></h2>
      <p><?php echo $Row->speciality; ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($Row->sthalam)) : ?>
    <div class="content-section">
      <h2>Temple Overview</h2>
      <p><?php echo $Row->sthalam; ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($Row->puranam)) : ?>
    <div class="content-section">
      <h2>Origin Story</h2>
      <p><?php echo $Row->puranam; ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($Row->varnam)) : ?>
    <div class="content-section">
      <h2>Architecture</h2>
      <p><?php echo $Row->varnam; ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($Row->highlights)) : ?>
    <div class="content-section">
      <h2>Mystical Beliefs</h2>
      <p><?php echo $Row->highlights; ?></p>
    </div>
    <?php endif; ?>

     <?php if (!empty($Row->sevas)) : ?>
    <div class="content-section">
      <h2>Festivals & Daily Rituals</h2>
      <p><?php echo $Row->sevas; ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($Row->time)) : ?>
    <div class="content-section">
      <h2>Temple  Timings</h2>
      <p><?php echo $Row->time; ?></p>
    </div>
    <?php endif; ?>

    
  

    <div class="content-section gallery">
        <?php if (!empty($Row->gallery_image)): ?>
          <h2>Gallery</h2>
          <div class="row">
            <?php 
              $existingImages = array_filter(explode(',', $Row->gallery_image)); // Remove empty entries
              foreach ($existingImages as $image) {

                  $imagePath = "app/uploads/Temple_gallery/" . htmlspecialchars($image);

                  // Check if the image file exists

                  if (trim($image) !== "" && file_exists($imagePath)) {

              ?>

                    <div class="col-2 mb-3">
                      <img src="<?= $imagePath; ?>" class="img-fluid rounded" id="image-<?= htmlspecialchars($image); ?>" alt="Gallery Image">
                    </div>

                     

              <?php

                  }

              }
           ?>
          </div>
        <?php endif; ?>
      </div>
  </div>
<script>
  function setLanguage(langCode) {
    const select = document.querySelector(".goog-te-combo");
    if (!select) return;

    select.value = langCode;
    select.dispatchEvent(new Event("change"));
}

// Listen for ?lang= parameter
(function () {
    const params = new URLSearchParams(window.location.search);
    const lang = params.get("lang");
    if (lang) {
        setTimeout(() => setLanguage(lang), 1000);
    }
})();

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,hi,kn,ml,bn,ta,te'
    }, 'google_translate_element');
}

function toggleTranslate() {
    const translateElement = document.getElementById('google_translate_element');
    translateElement.style.display =
        (translateElement.style.display === 'none' || translateElement.style.display === '') 
        ? 'block' : 'none';
}
(function () {
    const params = new URLSearchParams(window.location.search);
    const currentLang = params.get("lang") || "en";

    document
      .querySelectorAll(".sn_language_links a")
      .forEach(a => {
          if (a.dataset.lang === currentLang) {
              a.classList.add("active");
          }
      });
})();

</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<?php if(isset($_GET['auto_print'])): ?>
<script>
        const params = new URLSearchParams(window.location.search);
        const lang = params.get("lang") || "en";
        setLanguage(lang);
        
        setTimeout(function() {
            window.print();
        }, 1000); // allow google to translate

</script>
<?php endif; ?>
</body>
</html>
