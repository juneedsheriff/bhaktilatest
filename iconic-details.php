<?php

error_reporting(1);

include('./include/header.php');

include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './include/iconic_detail_helpers.php';
include_once './include/breadcrumb_helpers.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$id = $xssClean->clean_input($_REQUEST['id']);

$select = "SELECT * FROM `iconic_temples` WHERE index_id='$id'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
    $country = $Row->country;
    $state = $Row->state;
    $city = $Row->city;
    $address = $Row->address;
    $temple_name = $Row->title;

    extract(iconic_detail_build_view($DatabaseCo->dbLink, $Row));
    $mainRenderOrder = ['About', 'History', 'Deity', 'Mystical', 'Seva', 'Contact'];

    $commentsQuery = "SELECT * FROM `comments` WHERE type='iconic' AND temple_id='" . mysqli_real_escape_string($DatabaseCo->dbLink, $id) . "' AND is_approved=1 ORDER BY index_id DESC";
    $commentsResult = mysqli_query($DatabaseCo->dbLink, $commentsQuery);
    $allComments = [];
    if ($commentsResult) {
        while ($commentRow = mysqli_fetch_object($commentsResult)) {
            $allComments[] = $commentRow;
        }
    }
    $totalComments = count($allComments);
} else {
    echo '<p>Temple not found.</p>';
    include('./include/footer.php');
    exit;
}

$categoryCrumb = iconic_detail_category_breadcrumb($DatabaseCo->dbLink, $Row);
$breadcrumbItems = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Iconic Temples', 'url' => 'iconic-category.php'],
];
if ($categoryCrumb) {
    $breadcrumbItems[] = $categoryCrumb;
}
$breadcrumbItems[] = ['label' => $Row->title ?? 'Temple Details'];

?>
<style>
.mt-10 { margin-top: 15px; }

.tab-container {
    position: sticky;
    top: 0;
    z-index: 56;
    padding: 10px 0;
    text-align: center;
}

.temple-section-nav { overflow: hidden; }

.temple-section-nav .btn-cus {
    min-height: 2.75rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1.25;
    white-space: normal;
    word-break: break-word;
}

.sth-text img,
#pageReadContent img {
    max-width: 100%;
    height: auto;
}

.theiaStickySidebar .btn-cus {
    padding: 8px 10px;
}

.timing-list {
    list-style: none;
    padding: 0;
    margin: 0;
    color: #000;
    font-size: 15px;
    line-height: 1.6;
    text-align: left;
}

.timing-list p {
    margin-bottom: 0.5rem;
    color: #000;
}

.comment-item-hidden { display: none !important; }

.content p,
.sth-text p {
    text-align: left !important;
}
</style>

<?php render_breadcrumbs($breadcrumbItems); ?>
<link href="assets/css/temple-pages-responsive.css" rel="stylesheet">

<div class="container-fluid m-0 p-0 text-center bg-gradient text-center temple-detail-page">
    <div class="overflow-hidden position-relative banner-over-container">
        <img <?php echo iconic_detail_photo_attrs($Row); ?>>
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary"><?php echo htmlspecialchars($Row->title, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</div>

<div id="printable-content" class="bg-gradient temple-detail-page">
    <div id="printable-area">
        <div class="py-3 py-lg-5">
            <div class="container px-2 px-md-3">
                <div class="col-12">
                    <?php if ($specialityTitle !== '' || $specialityText !== '') : ?>
                        <div class="card shadow mb-5 bg-body rounded text-dark p-4 mb-4 sth-text">
                            <?php if ($specialityTitle !== '') : ?>
                                <h2 class="text-dark text-center"><?php echo htmlspecialchars($specialityTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                            <?php endif; ?>
                            <?php if ($specialityText !== '') : ?>
                                <div class="text-dark sth-text"><?php echo $specialityText; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div id="pageReadContent" class="col-12 col-lg-8 ps-lg-3 ps-xxl-5 content">
                        <?php if (!empty($navSections)) : ?>
                            <div class="tab-container text-center mb-4 hidePrint custom-sticky">
                                <div class="card rounded-4 border-0 bg-gradient temple-section-nav">
                                    <div class="card-body p-2 p-sm-3">
                                        <div class="row g-2 g-sm-3 justify-content-center temple-section-nav__grid">
                                            <?php foreach ($navSections as $section) : ?>
                                                <div class="col-6 col-sm-4 col-lg">
                                                    <button type="button" onclick="scrollToCard('<?php echo iconic_detail_section_id($section); ?>')" class="btn btn-primary btn-cus w-100">
                                                        <?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div id="bannerTitle">
                            <?php foreach ($mainRenderOrder as $anchor) : ?>
                                <?php if ($anchor === 'Contact') : ?>
                                    <?php if (!$contactSection) { continue; } ?>
                                    <div id="Contact" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">
                                        <h2 class="text-dark font-caveat">Contact</h2>
                                        <div><?php echo $contactSection['content']; ?></div>
                                    </div>
                                <?php else : ?>
                                    <?php
                                    if (!isset($sectionByAnchor[$anchor]) || !temple_detail_section_visible($sectionByAnchor[$anchor])) {
                                        continue;
                                    }
                                    $section = $sectionByAnchor[$anchor];
                                    $sectionId = iconic_detail_section_id($section);
                                    ?>
                                    <div id="<?php echo htmlspecialchars($sectionId, ENT_QUOTES, 'UTF-8'); ?>" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">
                                        <h2 class="text-dark font-caveat"><?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                        <div><?php echo $section['content']; ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="row print-disable">
                            <div class="comment-box mt-3">
                                <h4>Leave a Comment</h4>
                                <div class="alert alert-success mt-3 d-none" id="success-message">
                                    Comment successfully submitted and is pending approval!
                                </div>
                                <form action="" method="post" id="submit-comment">
                                    <div class="form-group mb-3">
                                        <p>Name</p>
                                        <input type="text" class="form-control" id="name" placeholder="Your Name" required>
                                    </div>
                                    <div class="form-group">
                                        <p>Comment</p>
                                        <textarea class="form-control" id="comment" rows="4" placeholder="Your Comment" required></textarea>
                                    </div>
                                    <input type="hidden" name="type" id="type" value="iconic" />
                                    <button class="btn btn-primary mt-10" type="submit">Post Comment</button>
                                </form>
                            </div>

                            <?php if ($totalComments > 0) : ?>
                                <h3 class="mt-5">Comments</h3>
                                <div id="comments-section" class="comment-section">
                                    <?php foreach ($allComments as $ci => $Rowc) :
                                        $isHidden = $ci >= 3 ? ' comment-item-hidden' : '';
                                    ?>
                                        <div class="comment-item<?php echo $isHidden; ?>" data-index="<?php echo $ci; ?>">
                                            <p>
                                                <strong><?php echo htmlspecialchars($Rowc->name ?? '', ENT_QUOTES, 'UTF-8'); ?></strong> says,<br>
                                                <?php echo nl2br(htmlspecialchars($Rowc->comment ?? '', ENT_QUOTES, 'UTF-8')); ?>
                                            </p>
                                            <hr>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($totalComments > 3) : ?>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="viewMoreComments" data-show-each="3">View more</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 sidebar mt-4 mt-lg-0">
                        <div class="social-media hidePrint">
                            <a class="btn btn-primary d-inline-block" href="#" data-lang="<?php echo $_GET['lang'] ?? 'en'; ?>" id="printBtn">
                                <i class="fas fa-print"></i>
                            </a>
                            <a class="btn btn-primary d-inline-block" href="#" onclick="shareToWhatsApp(); return false;">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <button id="toggleIcon" class="btn btn-primary d-inline-block">
                                <i class="fa fa-play"></i> Listen to Page
                            </button>
                        </div>

                        <?php if ($timingsContent !== '') : ?>
                            <div class="border p-4 rounded-4 shadow-sm" style="margin-top: 30px;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h4 class="mb-0">Temple <span class="text-primary">Timings</span></h4>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z" />
                                    </svg>
                                </div>
                                <div class="d-flex justify-content-between timing-list flex-column">
                                    <?php echo $timingsContent; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasGallery) : ?>
                            <div id="Photo" class="border p-4 rounded-4 shadow-sm mt-5 temple-sidebar-photos hidePrint">
                                <h4 class="mb-3 text-primary">Photos</h4>
                                <div class="row g-2 review-image zoom-gallery">
                                    <?php foreach ($galleryImages as $image) :
                                        $imagePath = iconic_detail_image_url($image);
                                        if ($imagePath === '') {
                                            continue;
                                        }
                                    ?>
                                        <div class="col-4">
                                            <a href="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="gallery-overlay-hover dark-overlay position-relative d-block overflow-hidden rounded-3 gallery-image-link">
                                                <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Gallery Image" class="rounded-3 gallery-thumb w-100" onerror="this.closest('.col-4').style.display='none';">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($addressSection) : ?>
                            <div class="border p-4 rounded-4 shadow-sm mt-5">
                                <h5 class="mb-3">Temple <span class="text-primary">Address</span></h5>
                                <div class="timing-list"><?php echo $addressSection['content']; ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasLocation) : ?>
                            <div id="Location" class="border p-4 rounded-4 shadow-sm mt-5 print-disable">
                                <h5 class="mb-3">Temple <span class="text-primary">Location</span></h5>
                                <?php if ($mapEmbed !== '') : ?>
                                    <iframe src="<?php echo htmlspecialchars($mapEmbed, ENT_QUOTES, 'UTF-8'); ?>" width="100%" height="300" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                <?php else : ?>
                                    <div id="map" style="width: 100%; height: 300px;"></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function scrollToCard(cardId) {
        const card = document.getElementById(cardId);
        if (!card) {
            return;
        }

        card.scrollIntoView({ behavior: 'smooth' });
        card.classList.add('highlight');
        setTimeout(function () {
            card.classList.remove('highlight');
        }, 1000);
    }
</script>

<?php if ($mapEmbed === '' && !empty(trim((string) $address))) : ?>
<script>
    function initMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement || typeof google === 'undefined') {
            return;
        }

        const fromAddress = <?php echo json_encode(strip_tags((string) $address)); ?>;
        const geocoder = new google.maps.Geocoder();

        geocoder.geocode({ address: fromAddress }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK && results[0]) {
                const location = results[0].geometry.location;
                const map = new google.maps.Map(mapElement, {
                    center: location,
                    zoom: 14,
                });

                new google.maps.Marker({
                    position: location,
                    map: map,
                    title: <?php echo json_encode($temple_name); ?>,
                });
            }
        });
    }

    window.initMap = initMap;
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBG-RZCzEuy7JMyMu4ykftt5ooRcCeqhKY&callback=initMap" async defer></script>
<?php endif; ?>

<script>
(function () {
    var toggleButton = document.getElementById('toggleIcon');
    if (!toggleButton || !window.speechSynthesis) {
        return;
    }

    var synth = window.speechSynthesis;
    var utterance = null;
    var isPlaying = false;
    var selectedVoice = null;
    var idleLabel = '<i class="fa fa-play"></i> Listen to Page';
    var stopLabel = '<i class="fa fa-stop"></i> Stop';

    function getTextToSpeak() {
        var sthalamEl = document.getElementById('Sthalam');
        var bannerTitleEl = document.getElementById('bannerTitle');
        if (sthalamEl && bannerTitleEl) {
            return ((sthalamEl.textContent || '').trim() + ' ' + (bannerTitleEl.textContent || '').trim()).trim();
        }

        var mainContent = document.getElementById('pageReadContent');
        return mainContent ? (mainContent.textContent || '').replace(/\s+/g, ' ').trim() : '';
    }

    function setupVoice() {
        var voices = synth.getVoices();
        selectedVoice = voices.find(function (v) {
            return v.lang === 'hi-IN' || v.name.toLowerCase().indexOf('hindi') !== -1 || v.lang === 'en-IN';
        });
    }

    if (synth.getVoices().length) {
        setupVoice();
    }
    synth.onvoiceschanged = setupVoice;

    function playSpeech() {
        if (synth.speaking) {
            synth.cancel();
        }

        var text = getTextToSpeak();
        if (!text) {
            if (typeof toastr !== 'undefined') {
                toastr.info('No content to read.');
            }
            return;
        }

        utterance = new SpeechSynthesisUtterance(text);
        if (selectedVoice) {
            utterance.voice = selectedVoice;
        }
        utterance.rate = 0.95;
        utterance.pitch = 1;
        utterance.volume = 1;
        utterance.onend = function () {
            isPlaying = false;
            toggleButton.innerHTML = idleLabel;
        };
        utterance.onerror = function () {
            isPlaying = false;
            toggleButton.innerHTML = idleLabel;
        };
        synth.speak(utterance);
        isPlaying = true;
        toggleButton.innerHTML = stopLabel;
    }

    toggleButton.addEventListener('click', function () {
        if (isPlaying) {
            synth.cancel();
            isPlaying = false;
            toggleButton.innerHTML = idleLabel;
        } else {
            playSpeech();
        }
    });

    window.addEventListener('beforeunload', function () {
        if (synth.speaking) {
            synth.cancel();
        }
    });
})();
</script>

<?php $printContent = iconic_detail_print_content($Row); ?>
<div id="printArea" style="display:none;">
    <?php echo $printContent; ?>
</div>

<script>
(function () {
    function getPrintBaseHref() {
        var pathParts = window.location.pathname.split('/');
        pathParts.pop();
        return window.location.origin + pathParts.join('/') + '/';
    }

    function initPrint() {
        var printBtn = document.getElementById('printBtn');
        var printArea = document.getElementById('printArea');
        if (!printBtn || !printArea) {
            return;
        }

        printBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var printContents = printArea.innerHTML;
            var printWindow = window.open('', '_blank', 'width=900,height=650');
            if (!printWindow) {
                alert('Please allow popups to print.');
                return;
            }

            var baseHref = getPrintBaseHref();
            var watermarkStyle = '<style type="text/css">' +
                '.print-watermark{position:fixed;top:0;left:0;width:100%;height:100%;' +
                'display:flex;align-items:center;justify-content:center;pointer-events:none;' +
                'z-index:1;opacity:0.12;font-size:120px;font-weight:bold;color:#000;' +
                'font-family:Arial,sans-serif;transform:rotate(-25deg);-webkit-print-color-adjust:exact;print-color-adjust:exact;}' +
                '.print-content-wrap{position:relative;z-index:2;}' +
                '</style>';
            var watermarkHtml = '<div class="print-watermark" aria-hidden="true">Bhaktikalpa</div><div class="print-content-wrap">' + printContents + '</div>';

            printWindow.document.open();
            printWindow.document.write('<html><head><title>Print Temple</title><base href="' + baseHref + '">' + watermarkStyle + '</head><body>');
            printWindow.document.write(watermarkHtml);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.onafterprint = function () {
                printWindow.close();
            };

            var didPrint = false;
            function triggerPrint() {
                if (didPrint || printWindow.closed) {
                    return;
                }
                didPrint = true;
                printWindow.focus();
                printWindow.print();
            }

            printWindow.onload = triggerPrint;
            setTimeout(triggerPrint, 400);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrint);
    } else {
        initPrint();
    }
})();
</script>

<?php include('./include/footer.php'); ?>

<script>
$(document).ready(function () {
    $('#viewMoreComments').on('click', function () {
        var hidden = $('#comments-section .comment-item-hidden');
        var showCount = parseInt($(this).data('show-each') || 3, 10);
        hidden.slice(0, showCount).removeClass('comment-item-hidden');
        if ($('#comments-section .comment-item-hidden').length === 0) {
            $(this).hide();
        }
    });
});
</script>
