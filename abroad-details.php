<?php
error_reporting(0);
include('./include/header.php');
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './include/abroad_listing_helpers.php';
include_once './include/breadcrumb_helpers.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$id = $xssClean->clean_input($_REQUEST['id']);

$select = "SELECT * FROM `abroad` WHERE index_id='$id'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
    $country = $Row->country;
    $state = $Row->state;
    $city = $Row->city;
    $address = $Row->address;
} else {
    echo '<p>Temple not found.</p>';
    include('./include/footer.php');
    exit;
}

$temple_name = $Row->title;
$placeLabel = abroad_listing_place_label($DatabaseCo->dbLink, (array) $Row);
$godName = abroad_detail_god_name($DatabaseCo->dbLink, $Row);
$sections = abroad_detail_sections($DatabaseCo->dbLink, $Row);
$mapEmbed = abroad_detail_map_embed_src($Row);
$hasLocation = abroad_detail_location_visible($Row);
$galleryImages = abroad_detail_gallery_images($Row);
$hasGallery = count($galleryImages) > 0;

$sectionByAnchor = [];
foreach ($sections as $sectionItem) {
    $sectionByAnchor[$sectionItem['anchor']] = $sectionItem;
}

$navSections = [];
foreach (['About', 'History', 'Deity', 'Mystical', 'Seva', 'Contact'] as $anchor) {
    if (isset($sectionByAnchor[$anchor]) && abroad_detail_section_visible($sectionByAnchor[$anchor])) {
        $navSections[] = $sectionByAnchor[$anchor];
    }
}
if ($hasGallery) {
    $navSections[] = ['anchor' => 'Photo', 'title' => 'Photos'];
}
if ($hasLocation) {
    $navSections[] = ['anchor' => 'Location', 'title' => 'Location'];
}

$commentsQuery = "SELECT * FROM `comments` WHERE type='abroad' AND temple_id='" . mysqli_real_escape_string($DatabaseCo->dbLink, $id) . "' AND is_approved=1 ORDER BY log_date DESC";
$commentsResult = mysqli_query($DatabaseCo->dbLink, $commentsQuery);
$allComments = [];
if ($commentsResult) {
    while ($commentRow = mysqli_fetch_object($commentsResult)) {
        $allComments[] = $commentRow;
    }
}
$totalComments = count($allComments);

$timingsContent = abroad_detail_timings_text($Row);
$addressSection = null;
$contactSection = null;
foreach ($sections as $section) {
    if ($section['anchor'] === 'Address' && abroad_detail_section_visible($section)) {
        $addressSection = $section;
    }
    if ($section['anchor'] === 'Contact' && abroad_detail_section_visible($section)) {
        $contactSection = $section;
    }
}

function abroad_detail_section_id(array $section)
{
    $map = [
        'About' => 'Sthalam',
        'History' => 'Puranam',
        'Deity' => 'Varnam',
        'Mystical' => 'Highlights',
        'Seva' => 'Sevas',
        'Photo' => 'Photo',
        'Address' => 'Address',
        'Contact' => 'Contact',
        'Location' => 'Location',
    ];

    return $map[$section['anchor']] ?? $section['anchor'];
}

$mainRenderOrder = ['About', 'History', 'Deity', 'Mystical', 'Seva', 'Contact'];
?>
<style>
    .content p,
    .sth-text p {
        text-align: left !important;
    }

    .sidebar .timing-list {
        color: #000;
        font-size: 15px;
        line-height: 1.6;
        text-align: left;
    }

    .sidebar .timing-list p {
        margin-bottom: 0.5rem;
        color: #000;
    }

    .sidebar .timing-list table {
        width: 100%;
        font-size: 14px;
    }

    .sidebar .timing-list table td {
        padding: 6px 4px;
        vertical-align: top;
        border-bottom: 1px solid #e4ebff;
        color: #000;
    }
</style>

<?php
render_breadcrumbs([
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Temples Abroad', 'url' => 'abroad.php'],
    ['label' => $Row->title ?? 'Temple Details'],
]);
?>

<div class="container-fluid m-0 p-0 text-center bg-gradient temple-detail-page">
    <div class="overflow-hidden position-relative banner-over-container">
        <img <?php echo abroad_detail_banner_attrs($Row); ?>>
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary"><?php echo htmlspecialchars($Row->title, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</div>

<div id="printable-content" class="bg-gradient temple-detail-page">
    <div id="printable-area">
        <div class="py-5">
            <div class="container">
          

                <div class="row">
                    <div id="pageReadContent" class="col-lg-8 ps-xxl-5 content">
                        <?php if (!empty($navSections)) : ?>
                            <div class="tab-container text-center mb-4 hidePrint custom-sticky">
                                <div class="card rounded-4 border-0 bg-gradient">
                                    <div class="row m-3 justify-content-center">
                                        <?php foreach ($navSections as $section) : ?>
                                            <div class="col-6 col-sm-4 col-md-auto">
                                                <button type="button" onclick="scrollToCard('<?php echo abroad_detail_section_id($section); ?>')" class="btn btn-primary btn-cus">
                                                    <?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
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
                                    if (!isset($sectionByAnchor[$anchor]) || !abroad_detail_section_visible($sectionByAnchor[$anchor])) {
                                        continue;
                                    }
                                    $section = $sectionByAnchor[$anchor];
                                    $sectionId = abroad_detail_section_id($section);
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
                                    <input type="hidden" name="type" id="type" value="abroad" />
                                    <button class="btn btn-primary mt-3" type="submit">Post Comment</button>
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

                    <div class="col-lg-4 sidebar">
                        <div class="social-media hidePrint">
                            <a class="btn btn-primary d-inline-block" href="" data-lang="<?php echo $_GET['lang'] ?? 'en'; ?>" id="printBtn">
                                <i class="fas fa-print"></i>
                            </a>
                            <a class="btn btn-primary d-inline-block" href="#" onclick="shareToWhatsApp(); return false;">
                                <i class="fab fa-whatsapp"></i>
                            </a>
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
                                <div class="timing-list"><?php echo $timingsContent; ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasGallery) : ?>
                            <div id="Photo" class="border p-4 rounded-4 shadow-sm mt-5 temple-sidebar-photos hidePrint">
                                <h4 class="mb-3 text-primary">Photos</h4>
                                <div class="row g-2 review-image zoom-gallery">
                                    <?php foreach ($galleryImages as $image) :
                                        $imagePath = abroad_detail_image_url($image);
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
                                <div><?php echo $addressSection['content']; ?></div>
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

<?php if ($mapEmbed === '' && abroad_detail_has_content($address)) : ?>
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

<?php include('./include/footer.php'); ?>
