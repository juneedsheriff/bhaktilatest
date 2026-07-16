<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

function pdf_json_error(string $message, int $code = 200): void
{
    if ($code !== 200) {
        http_response_code($code);
    }
    echo json_encode(['error' => true, 'message' => $message]);
    exit;
}

try {
    require __DIR__ . '/dompdf/vendor/autoload.php';
} catch (Throwable $e) {
    pdf_json_error('PDF library not available.');
}

use Dompdf\Dompdf;
use Dompdf\Options;

try {
    @ini_set('memory_limit', '512M');
    @set_time_limit(120);

    $htmlContent = (string) ($_POST['html'] ?? '');
    $bookTitle = (string) ($_POST['title'] ?? 'My_Mantra_Book');

    if ($htmlContent === '' || stripos($htmlContent, 'mantra-detail-box') === false) {
        pdf_json_error('No mantra content to export.');
    }

    $htmlContent = preg_replace('/<audio[^>]*>.*?<\/audio>/is', '', $htmlContent);
    $htmlContent = preg_replace('/<video[^>]*>.*?<\/video>/is', '', $htmlContent);
    $htmlContent = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $htmlContent);
    $htmlContent = preg_replace('/<iframe[^>]*>.*?<\/iframe>/is', '', $htmlContent);
    $htmlContent = preg_replace('/<img[^>]*>/i', '', $htmlContent);

    $bookTitle = preg_replace('/[^A-Za-z0-9_\- ]/', '', $bookTitle);
    $bookTitle = trim($bookTitle) !== '' ? trim($bookTitle) : 'My_Mantra_Book';

    $cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'dompdf_cache';
    if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0755, true)) {
        $cacheDir = sys_get_temp_dir();
    }

    $options = new Options([
        'defaultFont' => 'DejaVu Sans',
        'isRemoteEnabled' => false,
        'isHtml5ParserEnabled' => true,
        'tempDir' => $cacheDir,
        'fontCache' => $cacheDir,
        'chroot' => realpath(__DIR__ . '/..') ?: __DIR__,
    ]);

    $dompdf = new Dompdf($options);

    $finalHTML = '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
        body { font-family: DejaVu Sans, Helvetica, sans-serif; background: #fffdf7; color: #2a1a0f; margin: 0; padding: 10px; }
        .title { text-align: center; font-size: 28px; color: #b06a00; margin-bottom: 15px; font-weight: 700; }
        .divider { width: 70%; height: 1.5px; background: #d6a647; margin: 0 auto 25px auto; opacity: 0.8; }
        .mantra-detail-box { padding: 15px 18px; margin-bottom: 22px; border-radius: 4px; }
        h2, h3, h4, h5 { color: #b06a00; margin-top: 8px; margin-bottom: 8px; font-weight: 600; }
        p { font-size: 14px; line-height: 1.55; margin-bottom: 8px; }
    </style></head><body>'
        . '<div style="text-align:center;font-size:20px;color:#b06a00;font-weight:bold;margin-bottom:8px;">Bhaktikalpa</div>'
        . '<div class="title">' . htmlspecialchars($bookTitle, ENT_QUOTES, 'UTF-8') . '</div>'
        . '<div class="divider"></div>'
        . $htmlContent
        . '</body></html>';

    $dompdf->loadHtml($finalHTML, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    try {
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica', 'normal');
        $canvas->page_text(520, 820, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 10, [0, 0, 0]);
    } catch (Throwable $e) {
        // Optional footer.
    }

    $pdfOutput = $dompdf->output();
    if ($pdfOutput === '') {
        pdf_json_error('PDF generation produced empty output. Try selecting fewer mantras.');
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . str_replace(' ', '_', $bookTitle) . '.pdf"');
    header('Content-Length: ' . strlen($pdfOutput));
    header('Cache-Control: private, max-age=0, must-revalidate');
    echo $pdfOutput;
} catch (Throwable $e) {
    pdf_json_error('PDF generation failed: ' . $e->getMessage());
}
