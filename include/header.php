<?php

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

if (!isset($DatabaseCo) || !($DatabaseCo instanceof DatabaseConn) || !$DatabaseCo->isConnected()) {
    $DatabaseCo = new DatabaseConn();
}

include_once './app/class/XssClean.php';

$xssClean = new xssClean();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

?>

<!doctype html>

<html lang="en">

<head>

<!-- Required meta tags -->

<meta charset="utf-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="assets/images/logo/small.png">

<title>Bhaktikalpa</title>

<link href="assets/plugins/aos/aos.min.css" rel="stylesheet">

<link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<link href="assets/plugins/fontawesome/css/all.min.css" rel="stylesheet">

<link href="assets/plugins/OwlCarousel2/css/owl.carousel.min.css" rel="stylesheet">

<link href="assets/plugins/OwlCarousel2/css/owl.theme.default.min.css" rel="stylesheet">

<link href="assets/plugins/jquery-fancyfileuploader/fancy-file-uploader/fancy_fileupload.css" rel="stylesheet">

<link href="assets/plugins/ion.rangeSlider/ion.rangeSlider.min.css" rel="stylesheet">

<link href="assets/plugins/magnific-popup/magnific-popup.css" rel="stylesheet">

<link href="assets/plugins/select2/select2.min.css" rel="stylesheet">

<link href="assets/plugins/select2-bootstrap-5/select2-bootstrap-5-theme.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@600;700&display=swap" rel="stylesheet">

<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4iuUg1YDRIBRZ5e-jdssfqDuT9VLiOnY"></script>-->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<!-- Custom style for this template -->

<link href="assets/css/css.css" rel="stylesheet">

<link href="assets/css/responsive.css" rel="stylesheet">

<link href="assets/css/custom.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/css/intlTelInput.css"/>
<style>

html, body {

    overflow-x: hidden;

}

p,.sub-title.fs-16  {

    font-family: 'georgia';

    font-size: 18px !important;

    text-align: justify;

    word-spacing: -0.5px;

}

img{ object-fit: cover;}

img.logo-dark{ object-fit: unset;}

.banner-h-420{height: 420px;}

.toast-top-right{top: auto !important;bottom: 20% !important;}

.banner-over-container {

    position: relative;

    display: inline-block;

    width: 100%;

}

.banner-over-title {

    position: absolute;

    bottom: 0;

    left: 0;

    background: rgba(0, 0, 0, 0.45);

    color: #fff !important;

    padding: 0 !important;

    text-align: center;

    width: 100%;

    margin: 0 !important;

}

.text-justify {

    text-align: justify;

    text-justify: inter-word;

    word-spacing: -0.5px;

}

.important-text {

    top: 0px;

    font-size: 50px !important;

    line-height: 0.8;

    vertical-align: middle;

    --bs-text-opacity: 1;

    color: #ff8776 !important;

    display: inline-block;

}

.card-img-wrap img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}

.gm-style-iw-chr {display:none;}

.gm-style .gm-style-iw-tc {

    display: block !important;

}

.comment-section { margin-top: 30px; }

.comment { border: 1px solid #ddd; padding: 10px; margin-top: 15px; }

.comment span { font-weight: bold; }

#Sthalam em{

    font-size: 17px;

    line-height: 32px;

    font-family: georgia;

    margin: 1em auto;

    font-weight: normal;

    font-style: normal;

}

 .p1 {margin-top: 25% !important;}

.iconic-head, .iconic-head p{font-size:2.5rem !important;color:black;font-weight:bold;}

.iconic-subhead, .iconic-subhead p{

    font-size: 28px !important;

    line-height: 42px;

    font-family: georgia;

    font-style: italic;

    letter-spacing: 1px;

    font-weight: bold;

    text-align: left;

    word-spacing: -0.5px;

}

.iconic-banner-h{ height: 640px;}

/*.owl-item{ width: 330px !important; margin-right:0px;}*/

.iconic-owl .image-container img { height: 450px !important;}

.icon-desc::first-letter {font-size: 350%; font-family: georgia !important; color: #000;line-height: 0.8;}

@media (max-width: 768px) {

    .p1 { margin-top: 140% !important;}

    .iconic-banner-h{ min-height: unset !important;}

    .iconic-banner-mob{ height: auto !important;}

}

@media (min-width: 1900px) {

    /*.owl-item{ width: 360px !important;}*/

    .iconic-owl .image-container img { height: 530px !important; width:360px !important;}

}

@page {

    margin: 0;

    padding:60px;

}

 @media print {

        header, footer, .comment-box, .print-disable {

            display: none;

        }

 }
.notranslate{
    text-align:right;
}
    </style>

</head>

<body style="transform: none; position: inherit;min-height: 100%; top: 0px;">

    <!-- start navbar -->

    <?php
$menuItems = [];

/* ---------- Static Menu (with classes if needed) ---------- */
$menuItems[] = [
    'title' => 'Home',
    'url'   => 'index.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Temples In India',
    'url'   => 'temples-in-india.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Temples In Abroad',
    'url'   => 'abroad.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Iconic Temples',
    'url'   => 'iconic-category.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Mantras & Stotras',
    'url'   => 'mantras-new.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Mystery Temples',
    'url'   => 'mystery.php',
    'class' => 'd-block d-lg-none d-md-none'
];

$menuItems[] = [
    'title' => 'Nearby Temples',
    'url'   => 'findmylocation.php',
    'class' => 'd-block d-lg-none d-md-none'
];

/* ---------- Dynamic Categories ---------- */
$select = "SELECT index_id, name FROM category WHERE index_id != '0'";
$result = mysqli_query($DatabaseCo->dbLink, $select);

while ($row = mysqli_fetch_assoc($result)) {
    $menuItems[] = [
        'title' => $row['name'],
        'url'   => 'saints.php?id=' . $row['index_id']
        // no class → visible everywhere
    ];
}

/* ---------- Alphabetical Sort (Forum always last) ---------- */
usort($menuItems, function ($a, $b) {
    return strcasecmp($a['title'], $b['title']);
});

$menuItems[] = ['title' => 'Forum', 'url' => 'forum.php'];

// Current page for active nav state (script name only, e.g. index.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="mySidenav" class="sidenav download">

    <a href="javascript:void(0)" class="closebtn mt-1" onclick="closeNav()">&times;</a>

    <?php foreach ($menuItems as $item):
        $item_script = basename(parse_url($item['url'], PHP_URL_PATH));
        $sidenav_active = ($item_script === $current_page) ? ' active' : '';
        $sidenav_class = trim((isset($item['class']) ? $item['class'] : '') . $sidenav_active);
    ?>
        <a 
            href="<?php echo htmlspecialchars($item['url']); ?>"
            class="<?php echo $sidenav_class; ?>"
        >
            <?php echo htmlspecialchars($item['title']); ?>
        </a>
        <hr class="<?php echo isset($item['class']) ? $item['class'] : ''; ?>">
    <?php endforeach; ?>

</div>



    <nav class="navbar navbar-expand-lg navbar-light sticky-top">

        <div class="container-fluid">

            <a class="navbar-brand m-0" href="index.php">

                <img class="logo-white" src="assets/images/logo/bakthi-logo.png" alt="">

                <img class="logo-dark" src="assets/images/logo/bakthi-logo.png" alt="">

            </a>

            <div class="d-flex order-lg-2">
            <a href="live-darshan.php"><img src="assets/images/icon/cloud.png" class="download" alt="" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="Live Darshan" style="width:44px;margin-top: 7px; margin-right:10px;"></a>
                <a href="downloadpdf.php"><img src="assets/images/icon/arrow.png" class="download" alt="" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="Download mantras" style="width:34px;margin-top: 7px; margin-right: 0px;"></a>

                <!-- start button -->

                <a href="https://www.youtube.com/channel/UCoYc4EJSNFkLaIjXM2bCJjQ"  class="play-pause-button-color" style=" margin-top: 10px; margin-right:10px" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="Our channel"><i class=" fa fa-youtube" style="width:28px;height:28px;margin-top:9px;margin-left: 8px; margin-right: 8px;"></i></a>

                <!-- end /. button -->

                <!-- start button -->

                <!-- Language Icon with Dropdown -->

                <nav class="navbar navbar-expand-lg navbar-light sticky-top" style="display:none;">

                    <div class="container">

                        <!-- Other navbar content -->

                        <div class="d-flex">

                            <!-- Google Translate Icon with Dropdown -->

                            <div onclick="toggleTranslate()"><img src="assets/images/icon/translate.png" class="download google" alt="" width="40px" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="custom-tooltip" data-bs-title="Translator"></div>

                            <!-- Google Translate Dropdown -->

                            <div id="google_translate_element">

                            </div>

                        </div>

                    </div>

                </nav>

                <div>

                </div>

                <!-- end /. button -->

                <!-- start button -->

                <span class="default-btn download downloadi"><span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span></span>

                <!-- end /. button -->

            </div>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav m-auto mb-2 mb-lg-0">

                    <li class="nav-item dropdown">

                        <a class="nav-link<?php echo ($current_page === 'index.php') ? ' active' : ''; ?>" href="index.php">

                            Home

                        </a>

                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle material-ripple<?php echo (in_array($current_page, ['temples-in-india.php', 'abroad.php'], true)) ? ' active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <i class="typcn typcn-weather-stormy top-menu-icon"></i>Temples

                        </a>

                        <ul class="dropdown-menu">

                            <li><a class="dropdown-item<?php echo ($current_page === 'temples-in-india.php') ? ' active' : ''; ?>" href="temples-in-india.php">Temples In India</a></li>

                            <li><a class="dropdown-item<?php echo ($current_page === 'abroad.php') ? ' active' : ''; ?>" href="abroad.php">Temples In Abroad</a></li>

                            <!-- <li><a class="dropdown-item" href="iconic.php">Iconic Temples</a></li> -->

                        </ul>

                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link<?php echo ($current_page === 'iconic-category.php' || $current_page === 'iconic.php') ? ' active' : ''; ?>" href="iconic-category.php">

                            Iconic Temples

                        </a>

                    </li>

                    <li class="nav-item dropdown">

                        <a class="nav-link<?php echo ($current_page === 'mantras-new.php') ? ' active' : ''; ?>" href="mantras-new.php">

                            Mantras&nbsp;&amp;&nbsp;Stotras

                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link<?php echo ($current_page === 'mystery.php') ? ' active' : ''; ?>" href="mystery.php">Mystery&nbsp;Temples</a>

                    </li>

                    <!-- <li class="nav-item dropdown">

                        <a class="nav-link" href="saints.php" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">

                            Saints&nbsp;&amp;&nbsp;Poets

                        </a>

                    </li> -->

                    <li class="nav-item">

                        <a class="nav-link<?php echo ($current_page === 'findmylocation.php') ? ' active' : ''; ?>" href="findmylocation.php">Nearby Temples</a>

                    </li>



                    <li class="nav-item">

                        <a class="nav-link" href="#">Puja Store</a>

                    </li>
                    
                    <li class="nav-item">

                        <a class="nav-link" href="#">Yatra</a>

                    </li>

                </ul>



            </div>

        </div>

    </nav>

    <!-- end /. navbar -->

    <!-- <a href="#" class="sticky-button">Download Mantras</a> -->
<style>
    .sn_language_links{
            margin: 10px 0;
            padding:10px 0;
            margin-bottom:0px;
    }
    .sn_language_links p{

    font-size: 15px !important;
    color: #444;
    margin-bottom:0px;
}

.sn_language_links a {
    color: #000;
    text-decoration: none;
    font-weight: 600;
}

.sn_language_links a:hover {
    text-decoration: underline;
}
.breadcrumb-sec {
    background:#f5f5f5;
    
}
.sn_language_links a.active {
    font-weight: bold;
    text-decoration: underline;
    color: #ff8776; /* blue */
}
</style>
<section class="breadcrumb-sec">
    <div class="container">
        <div class="sn_language_links">
    <p class="notranslate">
      
       <?php
// Get current URL
$current_url = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Remove existing lang parameter if already present
$current_url = preg_replace('/(\?|&)lang=[a-z]{2}/', '', $current_url);

// Remove any trailing ? or &
$current_url = rtrim($current_url, '?&');

// Function to append lang parameter correctly
function addLang($url, $lang) {
    return $url . (strpos($url, '?') === false ? '?' : '&') . "lang=" . $lang;
}
?>
<a href="<?php echo htmlspecialchars(addLang($current_url, 'en')); ?>">A</a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'hi')); ?>">अ</a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'kn')); ?>">ಕ </a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'ml')); ?>">അ</a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'bn')); ?>">অ</a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'ta')); ?>">அ</a> /
    <a href="<?php echo htmlspecialchars(addLang($current_url, 'te')); ?>">అ</a>
    </p>
    </div>
    </div>
</section>

<?php
// Build correction form URL so it works on localhost and live (same folder as current page)
$script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
if ($script_dir === '' || $script_dir === '.') {
    $correction_form_action = 'correction_submit.php';
} else {
    $correction_form_action = rtrim($script_dir, '/') . '/correction_submit.php';
}

$correction_page_url = $_SERVER['REQUEST_URI'] ?? '';
if (!empty($_SERVER['HTTP_HOST'])) {
    $correction_scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $correction_page_url = $correction_scheme . '://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] ?? '');
}
?>
 
        <!-- <a class="btn-show-correction-form" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#correctionModal">Any Corrections?</a> -->
 
<!-- Correction form modal -->
<div class="modal fade" id="correctionModal" tabindex="-1" aria-labelledby="correctionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="correctionModalLabel">Send your corrections</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="correctionForm" data-action="<?php echo htmlspecialchars($correction_form_action); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <div class="mb-3">
                        <label class="form-label">Page URL</label>
                        <input type="url" name="page_url" id="page_url" class="form-control" disabled value="<?php echo htmlspecialchars($correction_page_url); ?>" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="user_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="user_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="user_phone" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correction Details</label>
                        <textarea name="correction_details" class="form-control" rows="4" required></textarea>
                    </div>
                    <div id="correctionResponse" class="mb-3 fw-bold"></div>
                    <button type="submit" class="btn btn-primary">Submit Correction</button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
.correction-box{
    padding:20px;
    border-bottom:1px solid #e3e1e1;
    border-top:1px solid #e3e1e1;
    margin-top:20px;
    background:#f9f9f9;
     
}
.correction-box input,
.correction-box textarea{
    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
    border:1px solid #ddd;
    border-radius:5px;
}
.correction-box button{
    background:#0066ff;
    color:#fff;
    border:none;
    padding:12px 20px;
    border-radius:6px;
    cursor:pointer;
}
.btn-show-correction-form{
    position: fixed;
    right: -57px;
    top: 30%;
    transform: translateY(-50%) rotate(90deg);
    background: #fb523a;
    color: #fff;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 0px 0px 8px 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    z-index: 9999;
    transition: all 0.3s ease;
}
.btn-show-correction-form:hover{
    background:#c53824
}
/* Keep correction modal above backdrop and other overlays (sticky bars, etc.) */
#correctionModal.modal {
    z-index: 9998;
}
#correctionModal .modal-dialog {
    z-index: 9999;
    position: relative;
}
 
/* Correction form: left-align text and custom form-control style */
#correctionModal .modal-header,
#correctionModal .modal-body,
#correctionModal .modal-title,
#correctionModal .form-label,
#correctionModal .form-control,
#correctionModal #correctionResponse,
#correctionModal .btn {
    text-align: left;
}
#correctionModal .modal-header {
    border-bottom: 1px solid #dee2e6;
}
#correctionModal .form-label {
    display: block;
    margin-bottom: 0.35rem;
    font-weight: 600;
    color: #333;
}
#correctionModal .form-control {
    display: block;
    width: 100%;
    padding: 0.6rem 0.85rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    border: 1px solid #8d9399;
    
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}
#correctionModal .form-control:focus {
    border-color: #fb523a;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(251, 82, 58, 0.2);
}
#correctionModal .form-control::placeholder {
    color: #6c757d;
}
#correctionModal textarea.form-control {
    min-height: 100px;
    resize: vertical;
}
#correctionModal .btn-primary {
    text-align: left;
}
</style>

