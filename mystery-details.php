<?php

error_reporting(1);



include('./include/header.php');



include_once './app/class/XssClean.php';

include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';

include_once './include/mystery_detail_helpers.php';

include_once './include/mystery_table_helpers.php';



$DatabaseCo = new DatabaseConn();

$xssClean = new xssClean();

$db = $DatabaseCo->dbLink;



$id = $xssClean->clean_input($_REQUEST['id'] ?? '');

$item = mystery_table_get_by_id($db, $id);



if ($item === null) {

    echo '<div class="container py-5"><p class="text-center">Mystery temple not found.</p></div>';

    include_once './include/footer.php';

    exit;

}



$Row = (object) [

    'title' => $item['name'] ?? $item['title'],

    'photos' => $item['image1'] ?? $item['photos'] ?? '',

];



$view = mystery_detail_build_view($item);

extract($view);

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

.btn-cus { padding: 8px 10px; }

.card.highlight {

    box-shadow: 0 0 0 3px #ff8776;

    transition: box-shadow 0.3s ease;

}

.mystery-detail-content img {

    max-width: 100%;

    height: auto;

}

</style>



<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">

    <div class="overflow-hidden position-relative banner-over-container">

        <img <?php echo mystery_detail_banner_attrs($item); ?>>

        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-primary"><?php echo htmlspecialchars($Row->title, ENT_QUOTES, 'UTF-8'); ?></h1>

    </div>

</div>



<div id="printable-content" class="bg-gradient">

    <div id="printable-area">

        <div class="py-5">

            <div class="container">

                <div class="col-12">

                    <?php if ($specialityTitle !== '' || $specialityText !== '' || $godName !== '' || $placeLabel !== '') : ?>

                        <div class="card shadow mb-5 bg-body rounded text-dark p-4 mb-4 sth-text">

                            <?php if ($specialityTitle !== '') : ?>

                                <h2 class="text-dark text-center"><?php echo htmlspecialchars($specialityTitle, ENT_QUOTES, 'UTF-8'); ?></h2>

                            <?php endif; ?>

                            <?php if ($godName !== '') : ?>

                              

                            <?php elseif ($placeLabel !== '') : ?>

                                <p class="text-dark sth-text text-center mb-2"><strong><?php echo htmlspecialchars($placeLabel, ENT_QUOTES, 'UTF-8'); ?></strong></p>

                            <?php endif; ?>

                            <?php if ($specialityText !== '') : ?>

                                <div class="text-dark sth-text mystery-detail-content"><?php echo $specialityText; ?></div>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                </div>



                <div class="row">

                    <div id="pageReadContent" class="col-lg-8 ps-xxl-5 content">

                        <?php if (!empty($navSections)) : ?>

                            <div class="tab-container text-center mb-4 hidePrint custom-sticky">

                                <div class="card rounded-4 border-0 bg-gradient">

                                    <div class="row m-3 justify-content-center">

                                        <?php foreach ($navSections as $section) : ?>

                                            <div class="col-6 col-sm-4 col-md-auto">

                                                <button type="button" onclick="scrollToCard('<?php echo htmlspecialchars($section['anchor'] ?? 'Photo', ENT_QUOTES, 'UTF-8'); ?>')" class="btn btn-primary btn-cus">

                                                    <?php echo htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?>

                                                </button>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>



                        <div id="bannerTitle">

                            <?php if ($hasHistory) : ?>

                                <div class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">

                                    <div class="mystery-detail-content"><?php echo $historyText; ?></div>

                                </div>

                            <?php endif; ?>



                            <?php if ($hasGallery) : ?>

                                <div id="Photo" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark">

                                    <h2 class="text-dark font-caveat">Photos</h2>

                                    <div class="row mt-3 g-2 review-image zoom-gallery">

                                        <?php foreach ($galleryImages as $image) :

                                            $imagePath = mystery_detail_gallery_url($image);

                                            if ($imagePath === '') {

                                                continue;

                                            }

                                        ?>

                                            <div class="col-6">

                                                <a href="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" class="gallery-overlay-hover dark-overlay position-relative d-block overflow-hidden rounded-3 gallery-image-link">

                                                    <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>" alt="Gallery Image" class="rounded-3 gallery-thumb" onerror="this.closest('.col-6').style.display='none';">

                                                </a>

                                            </div>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            <?php endif; ?>

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

                                    <input type="hidden" name="type" id="type" value="mystery" />

                                    <button class="btn btn-primary mt-10" type="submit">Post Comment</button>

                                </form>

                            </div>

                            <?php

                            $commentId = (int) $item['index_id'];

                            $query = "SELECT * FROM `comments` WHERE type='mystery' AND temple_id='{$commentId}' AND is_approved=1 ORDER BY index_id DESC";

                            $result = mysqli_query($DatabaseCo->dbLink, $query);

                            $allComments = [];

                            if ($result) {

                                while ($row = mysqli_fetch_object($result)) {

                                    $allComments[] = $row;

                                }

                            }

                            $totalComments = count($allComments);

                            if ($totalComments > 0) {

                            ?>

                                <h3 class="mt-5">Comments</h3>

                                <div id="comments-section" class="comment-section">

                                    <?php foreach ($allComments as $ci => $Rowc) {

                                        $isHidden = $ci >= 3 ? ' comment-item-hidden' : '';

                                    ?>

                                        <div class="comment-item<?php echo $isHidden; ?>" data-index="<?php echo $ci; ?>">

                                            <p><strong><?php echo htmlspecialchars($Rowc->name); ?></strong> says,<br><?php echo nl2br(htmlspecialchars($Rowc->comment)); ?></p>

                                            <hr>

                                        </div>

                                    <?php } ?>

                                </div>

                                <?php if ($totalComments > 3) : ?>

                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="viewMoreComments" data-show-each="3">View more</button>

                                <?php endif; ?>

                            <?php } ?>

                        </div>

                    </div>



                    <div class="col-lg-4 sidebar">

                        <div class="social-media hidePrint">

                            <a class="btn btn-primary d-inline-block" href="#" id="printBtn">

                                <i class="fas fa-print"></i>

                            </a>

                            <a class="btn btn-primary d-inline-block" href="#" onclick="shareToWhatsApp(); return false;">

                                <i class="fab fa-whatsapp"></i>

                            </a>

                        </div>



                        <?php if ($hasLocation) : ?>

                            <div class="border p-4 rounded-4 shadow-sm mt-5">

                                <h5 class="mb-3">Temple <span class="text-primary">Location</span></h5>

                                <div class="timing-list text-dark"><?php echo htmlspecialchars($placeLabel, ENT_QUOTES, 'UTF-8'); ?></div>

                            </div>

                        <?php endif; ?>



                        <div class="border p-4 rounded-4 shadow-sm mt-5">

                            <h5 class="mb-3">More <span class="text-primary">Mystery Temples</span></h5>

                            <a href="mystery.php" class="btn btn-primary">View All Mystery Temples</a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>



<div id="printArea" style="display:none;">

    <?php echo mystery_detail_print_html($item, $view); ?>

</div>



<script>

function scrollToCard(cardId) {

    const card = document.getElementById(cardId);

    if (!card) return;

    card.scrollIntoView({ behavior: 'smooth', block: 'start' });

    card.classList.add('highlight');

    setTimeout(function () {

        card.classList.remove('highlight');

    }, 1000);

}



document.getElementById('printBtn').addEventListener('click', function (e) {

    e.preventDefault();

    var printContents = document.getElementById('printArea').innerHTML;

    var printWindow = window.open('', '', 'width=900,height=650');

    var watermarkStyle = '<style type="text/css">' +

        '.print-watermark{position:fixed;top:0;left:0;width:100%;height:100%;' +

        'display:flex;align-items:center;justify-content:center;pointer-events:none;' +

        'z-index:1;opacity:0.12;font-size:120px;font-weight:bold;color:#000;' +

        'font-family:Arial,sans-serif;transform:rotate(-25deg);}' +

        '.print-content-wrap{position:relative;z-index:2;}' +

        '</style>';

    var watermarkHtml = '<div class="print-watermark" aria-hidden="true">Bhaktikalpa</div><div class="print-content-wrap">' + printContents + '</div>';

    printWindow.document.write('<html><head><title>Print Mystery Temple</title>' + watermarkStyle + '</head><body>');

    printWindow.document.write(watermarkHtml);

    printWindow.document.write('</body></html>');

    printWindow.document.close();

    printWindow.focus();

    printWindow.print();

    printWindow.close();

});

</script>



<?php include_once './include/footer.php'; ?>

<script>

$(function () {

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

<style>.comment-item-hidden { display: none !important; }</style>

