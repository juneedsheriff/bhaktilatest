<?php
require 'dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Get POST data
$htmlContent = $_POST['html'] ?? '';
$bookTitle = $_POST['title'] ?? 'My_Mantra_Book';

// Security clean
$bookTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $bookTitle);

// Configure PDF Options
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host;
}
$baseUrl = getBaseUrl();
$logoUrl = $baseUrl . "/assets/images/logo/bakthi-logo.png";
// Devotional clean design
$finalHTML = "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>
<link href='https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Tiro+Devanagari+Hindi:wght@400;600&display=swap' rel='stylesheet'>

<style>

    body {
        font-family: 'Tiro Devanagari Hindi', serif;
        background: #fffdf7; /* Very soft saffron */
        color: #2a1a0f;
        margin: 0;
        padding: 10px;

    }

    .header-logo {
        text-align: center;
        margin-bottom: 8px;
    }

    .header-logo img {
        width: 80px;
        height: auto;
        opacity: 0.95; /* Slight polish */
    }

    .title {
        text-align: center;
        font-family: 'Caveat', cursive;
        font-size: 38px;
        color: #b06a00;
        margin-bottom: 15px;
        font-weight: 700;
    }

    .divider {
        width: 70%;
        height: 1.5px;
        background: #d6a647;
        margin: 0 auto 25px auto;
        opacity: 0.8;
    }

    .mantra-detail-box {
        padding: 15px 18px;
        margin-bottom: 22px;
     
        border-radius: 4px;
        box-shadow: 0 0 3px rgba(0,0,0,0.05);

    }

    h2, h3, h4, h5 {
        color: #b06a00;
        margin-top: 8px;
        margin-bottom: 8px;
        font-weight: 600;

    }

    p {
        font-size: 18px;
        line-height: 1.55;
        margin-bottom: 8px;

    }

</style>
</head>

<body>

<div class='header-logo'>
    <img src='".$logoUrl."' alt='Bhakti Logo'>
</div>

<div class='title'>$bookTitle</div>

<div class='divider'></div>

$htmlContent

</body>
</html>
";

// Load HTML
$dompdf->loadHtml($finalHTML);

// A4
$dompdf->setPaper('A4', 'portrait');

// Render
$dompdf->render();

// Footer page numbers
$canvas = $dompdf->getCanvas();
$font = $dompdf->getFontMetrics()->getFont("Helvetica", "normal");

$canvas->page_text(
    520, 820,
    'Page {PAGE_NUM} of {PAGE_COUNT}',
    $font, 10,
    [0, 0, 0]
);

// Open in browser (preview)
$dompdf->stream($bookTitle . '.pdf', ["Attachment" => 0]);
