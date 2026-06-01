<?php
try {
    require __DIR__ . '/dompdf/vendor/autoload.php';
} catch (Throwable $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'PDF library not available.']);
    exit;
}

use Dompdf\Dompdf;
use Dompdf\Options;

// Get POST data
$htmlContent = $_POST['html'] ?? '';
$bookTitle = $_POST['title'] ?? 'My_Mantra_Book';

// Strip elements dompdf cannot render (audio, video, script, iframe)
$htmlContent = preg_replace('/<audio[^>]*>.*?<\/audio>/is', '', $htmlContent);
$htmlContent = preg_replace('/<video[^>]*>.*?<\/video>/is', '', $htmlContent);
$htmlContent = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $htmlContent);
$htmlContent = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $htmlContent);

// Security clean
$bookTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $bookTitle);

// Configure PDF Options - use writable dir to avoid fwrite stream errors
$cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'dompdf_cache';
if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true)) {
    $cacheDir = sys_get_temp_dir();
}
$options = new Options([
    'defaultFont' => 'Helvetica',
    'isRemoteEnabled' => false,
    'tempDir' => $cacheDir,
    'fontCache' => $cacheDir
]);

$dompdf = new Dompdf($options);

try {
// Use built-in fonts only - remote Google Fonts can cause empty PDF output
$finalHTML = "
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8'>

<style>

    body {
        font-family: Helvetica, DejaVu Sans, sans-serif;
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
        font-family: Helvetica, sans-serif;
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

<div class='header-logo' style='font-size:24px;color:#b06a00;font-weight:bold;'>Bhaktikalpa</div>

<div class='title'>$bookTitle</div>

<div class='divider'></div>

$htmlContent

</body>
</html>
";

// Load HTML
$dompdf->loadHtml($finalHTML, 'UTF-8');

// A4
$dompdf->setPaper('A4', 'portrait');

// Render
$dompdf->render();

try {
    $canvas = $dompdf->getCanvas();
    $font = $dompdf->getFontMetrics()->getFont("Helvetica", "normal");
    $canvas->page_text(520, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 10, [0, 0, 0]);
} catch (Throwable $e) {
    // Footer optional, continue
}

// Output PDF as string (avoids fwrite/stream issues on restricted hosts)
$pdfOutput = $dompdf->output();
if (strlen($pdfOutput) === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'PDF generation produced empty output. Try selecting fewer mantras or different content.']);
    exit;
}
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $bookTitle . '.pdf"');
header('Content-Length: ' . strlen($pdfOutput));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $pdfOutput;

} catch (Throwable $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'PDF generation failed: ' . $e->getMessage()]);
    exit;
}
