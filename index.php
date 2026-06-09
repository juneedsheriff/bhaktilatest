<?php
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './include/mystery_table_helpers.php';

$DatabaseCo = new DatabaseConn();
$galleryItems = [];

if ($DatabaseCo->isConnected()) {
    $templeGalleryQuery = "SELECT index_id, title, photos FROM temples
        WHERE status='approved' AND country='IN' AND photos IS NOT NULL AND photos != ''
        ORDER BY order_by ASC LIMIT 50";
    $templeGalleryResult = $DatabaseCo->safeQuery($templeGalleryQuery);

    if ($templeGalleryResult instanceof mysqli_result) {
        while ($row = $templeGalleryResult->fetch_assoc()) {
            $galleryItems[] = [
                'title' => $row['title'],
                'image' => 'app/uploads/temple/' . trim($row['photos']),
                'url' => 'temple-details.php?id=' . (int) $row['index_id'],
                'category' => 'temples-in-india',
            ];
        }
        $templeGalleryResult->free();
    }

    $iconicGalleryQuery = "SELECT index_id, title, photos FROM iconic_temples
        WHERE LOWER(TRIM(COALESCE(status, ''))) = 'approved'
        AND photos IS NOT NULL AND photos != ''
        ORDER BY order_by ASC LIMIT 50";
    $iconicGalleryResult = $DatabaseCo->safeQuery($iconicGalleryQuery);

    if ($iconicGalleryResult instanceof mysqli_result) {
        while ($row = $iconicGalleryResult->fetch_assoc()) {
            $galleryItems[] = [
                'title' => $row['title'],
                'image' => 'app/uploads/iconic_temple/' . trim($row['photos']),
                'url' => 'iconic-details.php?id=' . (int) $row['index_id'],
                'category' => 'iconic-temples',
            ];
        }
        $iconicGalleryResult->free();
    }

    $mantraGalleryQuery = "SELECT index_id, title, photos FROM mantras_subcategory
        WHERE status='approved' AND photos IS NOT NULL AND photos != ''
        ORDER BY order_by ASC LIMIT 50";
    $mantraGalleryResult = $DatabaseCo->safeQuery($mantraGalleryQuery);

    if ($mantraGalleryResult instanceof mysqli_result) {
        while ($row = $mantraGalleryResult->fetch_assoc()) {
            $galleryItems[] = [
                'title' => $row['title'],
                'image' => 'app/uploads/gods/' . trim($row['photos']),
                'url' => 'mantras-details.php?id=' . (int) $row['index_id'],
                'category' => 'mantras-stotras',
            ];
        }
        $mantraGalleryResult->free();
    }

    try {
        $mysteryGalleryRows = mystery_table_load_recent($DatabaseCo->dbLink, 50);
        foreach ($mysteryGalleryRows as $row) {
            if (empty($row['image_url'])) {
                continue;
            }
            $galleryItems[] = [
                'title' => $row['title'],
                'image' => $row['image_url'],
                'url' => mystery_table_detail_url($row),
                'category' => 'mystery-temples',
            ];
        }
    } catch (Throwable $e) {
        // Skip mystery gallery when table/schema is unavailable on this server.
    }
}

$galleryHalf = (int) ceil(count($galleryItems) / 2);
$galleryTopRow = array_slice($galleryItems, 0, $galleryHalf);
$galleryBottomRow = array_slice($galleryItems, $galleryHalf);

include_once './include/header.php';
?>
<style>
    .correction-box {
   display:none;
}
</style>
    <link href="assets/css/home.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">



        <div class="hero-banner overflow-hidden position-relative">

            <div class="hero-audio-control">
                <button id="playPauseBtn" type="button" class="hero-audio-btn" onclick="togglePlayPause()" aria-label="Play Om audio" title="Play Om audio">
                    <i id="playPauseIcon" class="fa fa-play"></i>
                    <span class="hero-audio-dots" aria-hidden="true">•••</span>
                </button>
                <audio id="audioPlayer" src="assets/audio/OMKARAM.mp3"></audio>
            </div>

            <!-- start background header carousel -->

            <div class="header-carousel owl-carousel owl-theme">

                <div class="hero-slide">

                    <img class="hero-slide-img" src="assets/images/banner/banner01.jpg" alt="Bhaktikalpa" fetchpriority="high">

                    <p class="hero-slide-caption font-caveat">Ram Mandir, Ayodhya                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner2.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Jagannath Temple, Puri                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner3.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Tirupathi Balaji, Tirumala                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner4.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Meenakshi Temple, Madurai                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner5.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Badrinath Temple, Uttarakhand                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner6.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Konark Sun Temple, Konark                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner7.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Padmanabhaswamy Temple, Thiruvananthapuram                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner8.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Ram mandir, Bhadrachalam                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner09.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Kanaka Durga Temple,Vijayawada                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner10.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Kanchi Kamakshi,Kanchipuram                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner11.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Kanipakam Vinayaka Temple,Chittoor                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner12.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Maa Mundeshwari Temple,Bihar                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner13.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Shri Saibaba Sansthan Trust, Shirdi, Ahmednagar                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner14.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Sri Ranganatha Swamy Temple, Srirangam                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner15.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Somnath Temple,Gujarat                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner16.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Aksardam Temple,Delhi                    </p>

                </div>

                <div class="hero-slide">

                    <img class="owl-lazy hero-slide-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="assets/images/banner/banner017.jpg" alt="Bhaktikalpa">

                    <p class="hero-slide-caption font-caveat">Jatoli Shiv Temple,Himachal Pradesh</p>

                </div>

            </div>

            <!-- end /. background header carousel -->

            <div class="header-carousel-controls">
                <button type="button" class="header-carousel-prev" aria-label="Previous slide">
                    <span class="header-carousel-count">1/17</span>
                    <span class="header-carousel-arrow" aria-hidden="true">‹</span>
                </button>
                <button type="button" class="header-carousel-next" aria-label="Next slide">
                    <span class="header-carousel-arrow" aria-hidden="true">›</span>
                    <span class="header-carousel-count">1/17</span>
                </button>
            </div>

        </div>



<!-- end /. hero header -->


<div class="position-relative overflow-hidden bg-gradient  print-disable text-center p-4" id="scroll">

<div class="icons">

<button id="toggleIcon" class="btn btn-primary btn-sm listen-page-btn d-inline-block" type="button" aria-label="Listen to page content" title="Listen to page content">

        <i class="fa fa-volume-up"></i> Listen to Page

    </button>

</div>

</div>
<!-- start about section -->

<div class="py-1" id="pageReadContent">

    <div class="container py-2">

        <div class="row justify-content-center">

            <div class="col-sm-10 col-md-10 col-lg-8">

                <!-- start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- start subtitle -->

 
                    <!-- end /. subtitle -->

                    <!-- start title -->

                    <h2 class="display-5   mb-3 section-header__title text-capitalize section-title-qwigley">Welcome to Bhaktikalpa</h2>

                    <!-- end /. title -->

                    <!-- start description -->

                    <div class="sub-title fs-16 text-center">Your sacred space for devotion, <span class="text-primary fw-semibold">Inspiration, and spiritual growth.</span></div>

                    <!-- end /. description -->

                </div>

                <!-- end /. section header -->

            </div>

        </div>

        <div class="row g-4">

            <div class="col-md-6">

                <div class="column-text-box text-justify left">

                <p><span class="float-start important-text position-relative text-primary fs-50"><strong>B</strong></span>haktikalpa is an informative sacred Kalpavriksha that encompasses all temples and slokas in Hinduism, representing devotion and spirituality. Its branches symbolize various aspects of spirituality, while the roots represent the foundation of faith and belief. By practicing Bhakti, one can establish a personal connection with the divine and attain inner peace, happiness, and spiritual fulfillment.</p>



<p>Bhaktikalpa is considered the highest form of spiritual practice, aiming to devote oneself to the deity. This devotion purifies the mind and heart, leading to spiritual liberation.Bhaktikalpa is envisioned as an informative, sacred "Kalpavriksha" or wish-fulfilling tree, encompassing the vast wisdom of Hindu temples, slokas, and spiritual teachings. Representing the essence of devotion and spirituality, each branch of Bhaktikalpa symbolizes different aspects of spiritual practice, such as worship, meditation, and community service, while its roots signify the deep foundation of faith and ancient traditions.</p>

<p>By embracing Bhakti, one cultivates a direct, personal connection with the divine, fostering inner peace, contentment, and spiritual growth. Bhaktikalpa is dedicated to exploring the iconic temples, mysterious temples, mantras and stotras, saints and poets, and holy sites of India, showcasing the country's rich cultural and religious heritage. The initiative offers detailed information about ancient temples, tirthas, saints, sages, and natural heritage sites.</p>



                </div>

            </div>

            <div class="col-md-6 ps-xxl-5">

                <!-- start about image masonry -->

                <div class="ps-xl-4 position-relative">

                    <div class="row g-3">

                        <div class="col-6">

                            <div class="about-image-wrap mb-3 rounded-4">

                                <img src="assets/images/about/god1.jpeg" alt="" class="h-100 w-100 object-fit-cover about-image-one rounded-3" loading="lazy" decoding="async">

                            </div>

                            <div class="about-image-wrap rounded-4">

                                <img src="assets/images/about/god2.jpg" alt="" class="h-100 w-100 object-fit-cover about-image-two rounded-3" loading="lazy" decoding="async">

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="about-image-wrap mb-3 rounded-4">

                                <img src="assets/images/about/god3.jpg" alt="" class="h-100 w-100 object-fit-cover about-image-three rounded-3" loading="lazy" decoding="async">

                            </div>

                            <div class="about-image-wrap rounded-4">

                                <img src="assets/images/about/god4.jpeg" alt="" class="h-100 w-100 object-fit-cover about-image-four rounded-3" loading="lazy" decoding="async">

                            </div>

                        </div>

                    </div>

                </div>

                <!-- end /. about image masonry -->

            </div>

        </div>

    </div>

</div>

<!--end-->


<div class="statistics_information">
                <h2 class="section-title-qwigley">Temples In India</h2>
            </div>
<!-- start listings carousel -->

<div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="temples-in-india.php" style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/donation/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Tirupathi Balaji, Tirumala
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Sabarimala, Sabarimala
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Badrinath, Uttarakhand
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Konark,Puri
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ram mandir, Bhadrachalam
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kamakshi,Kanchipuram
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Padmanabhaswamy,Kerela
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ranganatha,Srirangam
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-5.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Srisailam,Kurnool
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="temples-in-india.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/donation/work-g-6.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Maa Mundeshwari,Bihar
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>


<!-- end /. listings carousel -->
<div class="container-fluid  my-4 px-4">
<p>
                                             India is a land of temples, and the country is home to numerous magnificent temples that are not only architectural marvels but also spiritual sanctuaries. These temples are an integral part of the country's culture and heritage and are deeply revered by millions of people. Each temple has a unique history, architecture, and religious significance. Some of the most famous temples in India include the Golden Temple in Amritsar, the Konark Sun Temple in Odisha, the Meenakshi Temple in Madurai, the Brihadeeswarar Temple in Thanjavur, and the Kedarnath Temple in Uttarakhand. These temples are not just places of worship but also tourist attractions, drawing visitors from all over the world. The vibrant culture, intricate architecture, and spiritual atmosphere of these temples are truly mesmerizing and offer a glimpse into India's rich heritage and traditions.
                                            </p>

</div>
<!--iconic start listings carousel -->

<div class="statistics_information">
                <h2 class="section-title-qwigley">
                Mantras & Stotras</h2>
            </div>
            <div>
            <div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="mantras-new.php" style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Shiva
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Mahalakshmi
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ganesh
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Venkateswara
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Durga Devi
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Lakshmi Narashima Swamy
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kanchi Lamakshi
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kubera
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-5.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Tripura Sundari Devi
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mantras-new.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/bhak/work-g-6.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Hanuman
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="container-fluid  my-4 px-4">
<p>
 
Mantras and Stotras are an integral part of Hinduism and are considered to be powerful tools for spiritual growth and inner peace. Mantras are sacred chants or sounds that are believed to have a positive impact on the mind and body, while Stotras are hymns that are sung or recited in praise of the divine. The Gayatri Mantra is one of the most popular mantras in Hinduism, and is believed to have the power to illuminate the mind and lead to spiritual enlightenment. The LalitaSahasranama is a famous Stotra dedicated to the goddess Lalita, and is believed to bestow blessings and fulfill wishes. Other popular Mantras and Stotras include the Mahamrityunjaya Mantra, the Hanuman Chalisa, and the Vishnu Sahasranama. Reciting these powerful Mantras and Stotras is believed to have a transformative effect on one's spiritual journey, bringing peace, harmony, and enlightenment.
                                                                                        </p>

</div>
        
<div class="statistics_information">
                <h2 class="section-title-qwigley">
              
                Iconic Temples</h2>
            </div>

            <div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="iconic-category.php" style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/types/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Jyotirlingas
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Panchabhoota Sthalams
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Pancharama Kshetras
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Divya Desam
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Shaktipeetams
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Swayambhu
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Paadal Petra Sthalams
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-5.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Char Dham
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Aathara Sthalams
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="iconic-category.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/types/work-g-6.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Adobes Of Murugan
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-fluid  my-4 px-4">
<p>
 
 
Special temples in Hinduism hold significant importance and are revered by millions of devotees. Jyotirlingas are considered to be the most sacred shrines of Lord Shiva and are believed to represent his manifestation. Paadal Petra Sthalams are a group of 275 temples in South India, visited and praised by Tamil saints, and are significant in the Tamil Shaivism tradition. Other notable special temples include the Chardham Yatra, a pilgrimage to four shrines in Uttarakhand, the PuriJagannath Temple in Odisha, and the Kashi Vishwanath Temple in Varanasi. These temples attract thousands of pilgrims every year, seeking blessings and spiritual fulfillment. The vibrant culture, intricate architecture, and spiritual atmosphere of these temples offer a glimpse into India's rich heritage and traditions.
                                                                                                                                    </p>

</div>
        
<div class="statistics_information">
                <h2 class="section-title-qwigley">
              
              
                Mystery Temples</h2>
            </div>

            <div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="mystery.php" style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kal Bhairav Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kamakhya Devi Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Sree Padmanabhaswamy Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Venkateshwara Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Meenakshi Amman Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Veerabhadra Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kailasa Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-5.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Lingaraja Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kurumba Bhagavati Temple
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="mystery.php" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/mysterous/work-g-6.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ananthapura Lake Temple
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="container-fluid  my-4 px-4">
<p>

India is home to numerous temples that have an air of mystery and intrigue surrounding them, with legends and stories that have been passed down for generations. One such temple is the Kailasa Temple in Ellora, Maharashtra, carved entirely out of a single rock, and believed to have been built in the 8th century. Another example is the KalBhairav Temple in Ujjain, Madhya Pradesh, known for its eerie atmosphere and association with occult practices. The Brihadeeswarar Temple in Thanjavur is another mysterious temple, with its unique architecture and intricate carvings that are still shrouded in mystery. The exact origins and purpose of these temples remain unclear, adding to their enigmatic allure and making them popular tourist attractions for those seeking to unravel their mysteries.
</p>                                                                                                                                    </p>

</div>
<div class="statistics_information">
                <h2 class="section-title-qwigley">
              
              
                Narasimha Kshetras</h2>
            </div>

            <div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="iconic-category-details.php?id=62" style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/narasshima/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Sri Prahlada Narasimha Iskcon Bangalore
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category-details.php?id=62" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/narasshima/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Sri Sthanu Narasimha Iskon Mayapur
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category-details.php?id=62" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/narasshima/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Manjeshwar Temple Kerala
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="iconic-category-details.php?id=62" class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/narasshima/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Shree Lakshmi Narasimha Temple pune
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="container-fluid  my-4 px-4">
<p>

 

NarasimhaKshetras, also known as the temples dedicated to Lord Narasimha, are revered by many Hindus for their association with this powerful incarnation of Lord Vishnu. One of the most famous NarasimhaKshetras is the Ahobilam Temple in Andhra Pradesh, which is believed to be the place where Lord Narasimha appeared to save his devotee Prahlada from his tyrant father. The Simhachalam Temple in Visakhapatnam is another important NarasimhaKshetra, with a deity believed to have been worshipped by Lord Rama himself. Other notable NarasimhaKshetras include the Narasimha Temple in Hampi, the Narasimha Temple in Mangalagiri, and the Lakshmi Narasimha Temple in Yadagirigutta. These temples attract devotees from all over India and offer a unique opportunity to connect with this powerful and revered incarnation of Lord Vishnu.
                                            </p>                                                                                                                                    </p>

</div>
<div class="statistics_information">
                <h2 class="section-title-qwigley">
              
        
                Saints & Poets</h2>
            </div>
            <div class="container-fluid bhaktitempls">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-lg-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ramanuja
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-lg-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Adi Sankaracharya
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-lg-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Samarth Ramadas
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-3  col-sm-6">
                        <a href="saints.php?id=5 style="color: white;" class="">
                            <div class="project-img">
                                <img src="assets/images/poet/work-lg-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Potuluri Veerabrahmam
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-1.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Mirabai
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-2.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Kabirdas
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-3.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Surdas
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-5.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Ramanandacharya
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-4.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Tukaram
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <a href="saints.php?id=5 class="" style="color: white;">
                            <div class="project-img">
                                <img src="assets/images/poet/work-g-6.jpg" alt="" width="100%">
                                <div class="expand transform-center" style="background-color: #0000006b; padding: 5px;
                                    text-align: center;">
                                    Namdev
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="container-fluid  my-4 px-4">
<p>

 

 
India has been home to many great saints and poets who have left an indelible mark on the country's cultural and spiritual landscape. Some of the most well-known saints include Saint Kabir, Saint Tukaram, and Saint Mirabai, who used poetry to express their devotion to the divine. These saints have left a lasting legacy of spirituality and devotion, inspiring generations of people to connect with their inner selves and pursue a path of righteousness. The poets of India, such as Rabindranath Tagore, Kalidasa, and Mirza Ghalib, have also made significant contributions to literature and culture, showcasing the richness and diversity of the Indian subcontinent. Their works continue to be studied and celebrated to this day, serving as a testament to the enduring power of their words and ideas.
                                                                                        </p>                                                                                                                                    </p>

</div>
 

<!-- end /. listings carousel -->


 

<!-- start Photo Gallery -->

<div class="gallery-container">

    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Photo Gallery</div>

    <div class="filter-buttons">
        <button class="filter-btn active" data-filter="all" type="button">All</button>
        <button class="filter-btn" data-filter="temples-in-india" type="button">Temples In India</button>
        <button class="filter-btn" data-filter="iconic-temples" type="button">Iconic Temples</button>
        <button class="filter-btn" data-filter="mantras-stotras" type="button">Mantras &amp; Stotras</button>
        <button class="filter-btn" data-filter="mystery-temples" type="button">Mystery Temples</button>
    </div>

<?php
function render_gallery_slide(array $item)
{
    $title = htmlspecialchars((string) ($item['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars((string) ($item['image'] ?? ''), ENT_QUOTES, 'UTF-8');
    $url = htmlspecialchars((string) ($item['url'] ?? '#'), ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars((string) ($item['category'] ?? ''), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="swiper-slide gallery-slide" data-category="<?= $category ?>">
        <a href="<?= $url ?>" class="gallery-slide-link">
            <img src="<?= $image ?>" loading="lazy" decoding="async" alt="<?= $title ?>" onerror="this.onerror=null; this.src='assets/images/default-image.png';">
            <span class="gallery-slide-title"><?= $title ?></span>
        </a>
    </div>
    <?php
}
?>
<div class="gallery-wrapper">
    <div class="swiper gallery-swiper top">
        <div class="swiper-wrapper">
            <?php foreach ($galleryTopRow as $item) {
                render_gallery_slide($item);
            } ?>
        </div>
    </div>

    <div class="swiper gallery-swiper bottom">
        <div class="swiper-wrapper">
            <?php foreach ($galleryBottomRow as $item) {
                render_gallery_slide($item);
            } ?>
        </div>
    </div>
</div>

</div>

<script>
(function () {
    const filterButtons = document.querySelectorAll('.gallery-container .filter-btn');
    const gallerySlides = document.querySelectorAll('.gallery-slide');

    function applyGalleryFilter(filter) {
        gallerySlides.forEach(function (slide) {
            const category = slide.getAttribute('data-category');
            const show = filter === 'all' || category === filter;
            slide.style.display = show ? '' : 'none';
        });

        if (window.gallerySwiperTop) {
            window.gallerySwiperTop.update();
        }
        if (window.gallerySwiperBottom) {
            window.gallerySwiperBottom.update();
        }
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const filter = button.getAttribute('data-filter');
            filterButtons.forEach(function (btn) {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            applyGalleryFilter(filter);
        });
    });
})();
</script>

<script>

    document.addEventListener('DOMContentLoaded', () => {

    const cards = document.querySelectorAll('.listings-carousel .card');

    let maxHeight = 0;



    // Find the max height

    cards.forEach(card => {

        card.style.height = 'auto'; // Reset height to natural

        maxHeight = Math.max(maxHeight, card.offsetHeight);

    });



    // Set all cards to the max height

    cards.forEach(card => {

        card.style.height = maxHeight + 'px';

    });

});



</script>

<!-- end gallery section -->

<?php include_once './include/live-darshan-list.php' ?>

<?php include_once './include/footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
window.gallerySwiperTop = new Swiper('.gallery-swiper.top', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    loop: true,
    speed: 8000,
    autoplay: {
        delay: 0,
        disableOnInteraction: false,
        reverseDirection: false,
    },
    freeMode: true,
    freeModeMomentum: false,
});

window.gallerySwiperBottom = new Swiper('.gallery-swiper.bottom', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    loop: true,
    speed: 8000,
    autoplay: {
        delay: 0,
        disableOnInteraction: false,
        reverseDirection: true,
    },
    freeMode: true,
    freeModeMomentum: false,
});
</script>

<script>
(function() {
  var toggleButton = document.getElementById('toggleIcon');
  if (!toggleButton || !window.speechSynthesis) return;

  var synth = window.speechSynthesis;
  var utterance = null;
  var isPlaying = false;
  var selectedVoice = null;

  // Get text to read: use Sthalam+bannerTitle if present (e.g. temple page), else index page content
  function getTextToSpeak() {
    var sthalamEl = document.getElementById('Sthalam');
    var bannerTitleEl = document.getElementById('bannerTitle');
    if (sthalamEl && bannerTitleEl) {
      return (sthalamEl.textContent || '').trim() + ' ' + (bannerTitleEl.textContent || '').trim();
    }
    var parts = [];
    var heroTitle = document.querySelector('.hero-header_title');
    var heroLead = document.querySelector('.overlay-content .lead');
    var mainContent = document.getElementById('pageReadContent');
    if (heroTitle) parts.push((heroTitle.textContent || '').trim());
    if (heroLead) parts.push((heroLead.textContent || '').trim());
    if (mainContent) parts.push((mainContent.textContent || '').trim());
    var text = parts.filter(Boolean).join('. ');
    return text.replace(/\s+/g, ' ').trim();
  }

  function setupVoice() {
    var voices = synth.getVoices();
    selectedVoice = voices.find(function(v) {
      return v.lang === 'hi-IN' || v.name.toLowerCase().indexOf('hindi') !== -1 || v.lang === 'en-IN';
    });
  }
  if (synth.getVoices().length) setupVoice();
  synth.onvoiceschanged = setupVoice;

  function playSpeech() {
    if (synth.speaking) synth.cancel();
    var text = getTextToSpeak();
    if (!text) {
      if (typeof toastr !== 'undefined') toastr.info('No content to read.');
      return;
    }
    utterance = new SpeechSynthesisUtterance(text);
    if (selectedVoice) utterance.voice = selectedVoice;
    utterance.rate = 0.95;
    utterance.pitch = 1;
    utterance.volume = 1;
    utterance.onend = function() {
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-volume-up"></i> Listen to Page';
    };
    utterance.onerror = function() {
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-volume-up"></i> Listen to Page';
    };
    synth.speak(utterance);
    isPlaying = true;
    toggleButton.innerHTML = '<i class="fa fa-stop"></i> Stop Reading';
  }

  toggleButton.addEventListener('click', function() {
    if (isPlaying) {
      synth.cancel();
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-volume-up"></i> Listen to Page';
    } else {
      playSpeech();
    }
  });

  window.addEventListener('beforeunload', function() {
    if (synth.speaking) synth.cancel();
  });

  var voiceSelect = document.getElementById('voiceSelect');
  if (voiceSelect) {
    function populateVoiceList() {
      var voices = synth.getVoices();
      voiceSelect.innerHTML = '';
      voices.forEach(function(voice, index) {
        var option = document.createElement('option');
        option.textContent = voice.name + ' (' + voice.lang + ')';
        option.value = index;
        voiceSelect.appendChild(option);
      });
    }
    if (synth.getVoices().length) populateVoiceList();
    synth.onvoiceschanged = populateVoiceList;
  }
})();
</script>