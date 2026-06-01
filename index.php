<?php include_once './include/header.php';



include_once './app/class/databaseConn.php';

include_once './app/lib/requestHandler.php';



$DatabaseCo = new DatabaseConn();



$titleQuery = "SELECT DISTINCT title FROM gallery";

$titleResult = $DatabaseCo->dbLink->query($titleQuery);



if (!$titleResult) {

    die("Error in title query: " . $DatabaseCo->dbLink->error);

}



// Fetch all gallery items

$galleryQuery = "SELECT * FROM gallery";

$galleryResult = $DatabaseCo->dbLink->query($galleryQuery);



if (!$galleryResult) {

    die("Error in gallery query: " . $DatabaseCo->dbLink->error);

}

?>
<style>
    .correction-box {
   display:none;
}
</style>
    <link href="assets/css/home.css" rel="stylesheet">



        <div class="hero-header-waves mx-3 overflow-hidden position-relative rounded-4">

            <!-- start background header carousel -->

            <div class="header-carousel owl-carousel owl-theme position-absolute top-0 start-0 end-0 h-100">

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner01.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 fs-2 px-3 p1 font-caveat">Ram Mandir, Ayodhya</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner2.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Jagannath Temple, Puri</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner3.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Tirupathi Balaji, Tirumala</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner4.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Meenakshi Temple, Madurai</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner5.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">



                        <p class="fs-2 px-3 p1 font-caveat">Badrinath Temple, Uttarakhand</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner6.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Konark Sun Temple, Konark</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner7.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Padmanabhaswamy Temple, Thiruvananthapuram</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner8.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Ram mandir, Bhadrachalam</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner09.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Kanaka Durga Temple,Vijayawada</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner10.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Kanchi Kamakshi,Kanchipuram</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner11.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Kanipakam Vinayaka Temple,Chittoor</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner12.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Maa Mundeshwari Temple,Bihar</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner13.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Shri Saibaba Sansthan Trust, Shirdi, Ahmednagar</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner14.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Sri Ranganatha Swamy Temple, Srirangam</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner15.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Somnath Temple,Gujarat</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner16.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Aksardam Temple,Delhi</p>

                    </div>

                </div>

                <div class="position-relative h-100">

                    <img class="h-100 object-fit-cover" src="assets/images/banner/banner017.jpg" alt="Bhaktikalpa">

                    <div class="dark-overlay card-img-overlay d-flex flex-column justify-content-center align-items-center text-center">

                        <p class="fs-2 px-3 p1 font-caveat">Jatoli Shiv Temple,Himachal Pradesh</p>

                    </div>

                </div>



            </div>



            <!-- end /. background header carousel -->

            <div class="align-items-top d-flex dark-overlay mt-3 mx-3 overflow-hidden position-relative rounded-4" style=" height: 75vh !important; ">

                <!-- start background image -->

                <!-- <img class="bg-image" src="assets/images/header/lg-01.jpg" alt="Image"> -->

                <!-- end /. background image -->

                <div class="container overlay-content d-flex flex-column justify-content-center align-items-center">

                    <h1 class="display-1 mt-5 fw-bold hero-header_title text-capitalize text-white text-center mb-5" style=" text-shadow: 2px 1px #333333; ">Discover<br class="d-none d-lg-block"> <span class="font-caveat">India's</span> Sacred Heritage</h1>

                    <div class="lead mb-4 mb-sm-5 text-center text-white fs-2" style=" text-shadow: 2px 1px #333333; ">Journey through timeless temples and spiritual traditions</div>

                    <div class="row justify-content-center">

                        <div class="col-lg-10 col-md-12">

                            <!-- start search content -->

                            

                            <!-- end search content -->

                            <!-- Autocomplete Results -->

                            <div class="col-12">

                                <div class="list-group mt-2" id="show-list">

                                    <!-- Autocomplete results will appear here -->

                                </div>

                            </div>

                        </div>

                    </div>



                </div>

            </div>

            <!-- waves container -->

            <div class="position-relative z-2">

                <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewbox="0 24 150 28" preserveaspectratio="none" shape-rendering="auto">

                    <defs>

                        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>

                    </defs>

                    <g class="parallax">

                        <use xlink:href="#gentle-wave" x="48" y="0"></use>

                        <use xlink:href="#gentle-wave" x="48" y="3"></use>

                        <use xlink:href="#gentle-wave" x="48" y="5"></use>

                        <use xlink:href="#gentle-wave" x="48" y="7"></use>

                    </g>

                </svg>

            </div>

            <!-- end /. waves container -->

        </div>



<!-- end /. hero header (waves) -->


<div class="position-relative overflow-hidden bg-gradient  print-disable text-center p-4" id="scroll">

<div class="icons">

<button id="toggleIcon" class="btn btn-primary d-inline-block">

        <i class="fa  fa-play"></i> Play
 
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

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Welcome to</div>

                    <!-- end /. subtitle -->

                    <!-- start title -->

                    <h2 class="display-5 fw-semibold mb-3 section-header__title text-capitalize">Bhaktikalpa</h2>

                    <!-- end /. title -->

                    <!-- start description -->

                    <div class="sub-title fs-16">Your sacred space for devotion, <span class="text-primary fw-semibold">Inspiration, and spiritual growth.</span></div>

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

                                <img src="assets/images/about/god1.jpeg" alt="" class="h-100 w-100 object-fit-cover about-image-one rounded-3">

                            </div>

                            <div class="about-image-wrap rounded-4">

                                <img src="assets/images/about/god2.jpg" alt="" class="h-100 w-100 object-fit-cover about-image-two rounded-3">

                            </div>

                        </div>

                        <div class="col-6">

                            <div class="about-image-wrap mb-3 rounded-4">

                                <img src="assets/images/about/god3.jpg" alt="" class="h-100 w-100 object-fit-cover about-image-three rounded-3">

                            </div>

                            <div class="about-image-wrap rounded-4">

                                <img src="assets/images/about/god4.jpeg" alt="" class="h-100 w-100 object-fit-cover about-image-four rounded-3">

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



<!-- start listings carousel -->

<div class="py-5 position-relative overflow-hidden" style="background:linear-gradient(141.76deg, #F5D9D5 0.59%, #F5EAB4 39.43%, #1f8a4c 100%) !important;">

    <div class="container py-4">

        <div class="row">

            <div class=" col-11">

                <!-- Start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- Subtitle -->

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">

                        Temples In India

                    </div>

                    <!-- Description -->

                    <div class="sub-title fs-16">

    India's temples are spiritual havens,

    <span class="text-primary fw-semibold">

        Renowned for their divine ambiance and architectural grandeur.

    </span>

</div>



                </div>

            </div>

            <div class="col-12 mb-4 col-lg-1" align="right"><br><a href="temple.php"  class="btn btn-primary">View&nbsp;all&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                        <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                    </svg></a></div>



        </div>



        <!-- Carousel for temple listings -->

        <div class="listings-carousel owl-carousel owl-theme owl-nav-bottom">

    <?php

    // Fetch all data from temples table with limit and offset

    $select = "SELECT * FROM `temples` WHERE index_id !='0' ORDER BY index_id DESC";

    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



    // Check if any rows are returned

    if (mysqli_num_rows($SQL_STATEMENT) > 0) {

        while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

            $photos = $Row['photos'];

            $title = $Row['title'];

    ?>

            <!-- Start listing card -->

            <div class="card rounded-3 w-100 flex-fill overflow-hidden">

                <!-- Card image -->

                <div class="card-img-wrap card-image-hover overflow-hidden">

                    <a href="temple.php"   class="stretched-link"></a>

                    <!-- Ensure consistent image dimensions -->

                    <div class="image-container">

                        <img src="app/uploads/temple/<?php echo $photos; ?>" alt="<?php echo $title; ?>" class="img-fluid">

                    </div>

                </div>



                <div class="d-flex flex-column position-relative p-2 card-content">

                    <!-- Icon at the top of the card -->

                    <div class="align-items-center bg-primary cat-icon d-flex justify-content-center 

                                position-absolute rounded-circle text-white">

                        <a href="temple.php" class="stretched-link"></a>

                        <i class="fa-solid fa-gopuram"></i>

                    </div>

                    <!-- Card title -->

                    <h4 class="fs-5 fw-semibold mb-0">

                        <a href="temple.php"  >

                            <?php echo $title; ?>

                        </a>

                    </h4>

                    <p><a href="temple.php"  >Read more</a></p>

                </div>

            </div>

            <!-- End listing card -->

    <?php

        }

    } else {

        echo "<p class='text-center'>No temples found.</p>";

    }

    ?>

</div>



</div>

    </div>



<!-- end /. listings carousel -->

<!--iconic start listings carousel -->

<div class="py-5 position-relative overflow-hidden">

    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="col-sm-10 col-md-10 col-lg-8 col-xl-7">

                <!-- start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">



                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Iconic Temples</div>

                    <div class="sub-title fs-16">India’s iconic temples like <span class="text-primary fw-semibold">Jagannath in Puri, Meenakshi in Madurai, and Kedarnath in the Himalayas showcase the nation’s rich spiritual and architectural heritage.</span></div>

                </div>
              

            </div>

               <div class="col-12 mb-4 col-lg-2" align="right"><br>
                 <a href="iconic-category.php"  class="btn btn-primary mt-3 " align="center">View All <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                    <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                </svg></a>
                </div>

        </div>

        <div class="listings-carousel owl-carousel owl-theme owl-nav-bottom">

            <?php

            // Fetch all data from temples table with limit and offset



            $select = "SELECT * FROM `iconic` ORDER BY order_by  ASC";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

            ?>

                    <!-- start listing card -->

                    <div class="card rounded-3 w-100 flex-fill overflow-hidden">

                <!-- Card image -->

                <div class="card-img-wrap card-image-hover overflow-hidden">

                    <a href="iconic-category-details.php?id=<?php echo $Row['index_id'];?>"  class="stretched-link"></a>

                    <!-- Ensure consistent image dimensions -->

                    <div class="image-container">

                        <img src="app/uploads/iconic/<?php echo $photos; ?>" alt="<?php echo $title; ?>" class="img-fluid">

                    </div>

                </div>



                <div class="d-flex flex-column position-relative p-2 card-content">

                    <!-- Icon at the top of the card -->

                    <div class="align-items-center bg-primary cat-icon d-flex justify-content-center 

                                position-absolute rounded-circle text-white">

                        <a href="iconic-category-details.php?id=<?php echo $Row['index_id'];?>"   class="stretched-link"></a>

                        <i class="fa-solid fa-gopuram"></i>

                    </div>

                    <!-- Card title -->

                    <h4 class="fs-5 fw-semibold mb-0">

                        <a href="iconic-category-details.php?id=<?php echo $Row['index_id'];?>"  >

                            <?php echo $title; ?>

                        </a>

                    </h4>

                    <p><a href="iconic-category-details.php?id=<?php echo $Row['index_id'];?>"  >Read more</a></p>

                </div>

            </div>

                    <!-- end /. listing card -->

                    <!-- end /. region card -->

            <?php

                }

            } else {

                echo "<p class='text-center'>No temples found.</p>";

            }

            ?>



        </div>

       

    </div>

</div>

<!-- end /. listings carousel -->



<!-- start Narasimha section -->

<div class="py-5" style="background: linear-gradient(135deg, #ffb092, #ffe08e, #fff2c5);">

    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="col-sm-10 col-md-10 col-lg-8">

                <!-- start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- start subtitle -->

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Narasimha Kshetras</div>

                    <!-- end /. subtitle -->

                </div>

                <!-- end /. section header -->

            </div>

        </div>

        <div class="row g-4">

            <div class="col-md-6">

                <div class="ps-xl-4 position-relative">

                    <div class="row g-3">

                        <div class="col-lg-6">

                            <div class="thumbnail-wrapper">

                                <div class="thumbnail image-1">

                                    <img data-parallax="{&quot;x&quot;: 0, &quot;y&quot;: -20}" src="assets/images/narasimha/405.jpg" width="70%" alt="Education Images" style="transform:translate3d(0px, -2.365px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1); -webkit-transform:translate3d(0px, -2.365px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1);margin-left: 10px; border-radius: 10px;">

                                </div>

                                <div class="thumbnail image-2 d-none d-xl-block">

                                    <img data-parallax="{&quot;x&quot;: 0, &quot;y&quot;: 60}" src="assets/images/narasimha/396.jpg" width="80%" alt="Education Images" style="transform:translate3d(0px, 5.755px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1); -webkit-transform:translate3d(0px, 5.755px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1);margin-left: 50px; margin-top: 63px; border-radius: 10px;">

                                </div>

                                <div class="thumbnail image-3 d-none d-md-block">

                                    <img data-parallax="{&quot;x&quot;: 0, &quot;y&quot;: 80}" src="assets/images/narasimha/308.jpg" width="80%" alt="Education Images" style="transform:translate3d(0px, 0.029px, 0px) rotateX(0deg) rotateY(0deg) rotateZ(0deg) scaleX(1) scaleY(1) scaleZ(1); margin-left:160px;border-radius: 10px; margin-top:-76px;">

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-6 ps-xxl-5">

                <div class="column-text-box left">

                    <p><span class="float-start important-text position-relative text-primary text-justify fs-50"><strong>N</strong></span>arasimhaKshetras, also known as the temples dedicated to Lord Narasimha, are revered by many Hindus for their association with this powerful incarnation of Lord Vishnu. One of the most famous NarasimhaKshetras is the Ahobilam Temple in Andhra Pradesh, which is believed to be the place where Lord Narasimha appeared to save his devotee Prahlada from his tyrant father. The Simhachalam Temple in Visakhapatnam is another important NarasimhaKshetra, with a deity believed to have been worshipped by Lord Rama himself. Other notable NarasimhaKshetras include the Narasimha Temple in Hampi, the Narasimha Temple in Mangalagiri, and the Lakshmi Narasimha Temple in Yadagirigutta. These temples attract devotees from all over India and offer a unique opportunity to connect with this powerful and revered incarnation of Lord Vishnu.

                                            </p>

                    <!-- Link to iconic-category.php with god_id=7 passed as a parameter -->

                    <div><a href="iconic-category.php" class="btn btn-primary mt-3">View more&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                                <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                            </svg></a></div>

                </div>

            </div>

        </div>



    </div>

</div>

<!-- end /. about section -->

<!-- start Mantras -->

<div class="py-5 bg-primary position-relative overflow-hidden text-white bg-primary bg-size-contain home-about js-bg-image" data-image-src="assets/images/lines.svg">

    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="row justify-content-center">

                <div class="col-sm-10 col-md-10 col-lg-8 col-xl-7">

                    <!-- start section header -->

                    <div class="section-header text-center mb-5" data-aos="fade-down">

                        <!-- start subtitle -->

                        <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize">Mantras and Stotras</div>

                        <!-- end /. subtitle -->



                        <!-- start description -->

                        <div class="sub-title fs-16">Hymns or verses in praise of deities,<span class="fw-semibold">Orecited for devotion and protection.</span></div>

                        <!-- end /. description -->

                    </div>

                    <!-- end /. section header -->

                </div>

            </div>

        </div>

        <!-- start place carousel -->

        <div class  ="row">

<?php

// Fetch all data from temples table with limit and offset

$select = "SELECT * FROM `mantras_subcategory` WHERE index_id !='0' ORDER BY order_by ASC";

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



// Check if any rows are returned (show only 4 images)

if (mysqli_num_rows($SQL_STATEMENT) > 0) {

    $count = 0;

    while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
        if ($count >= 4) {
            break;
        }
        $photos = $Row['photos'];
        $title = $Row['title'];
        $count++;
?>

        <!-- start region card -->

        <div class="col-md-3 col-sm-6">
        <div class=" region-card rounded-4 overflow-hidden position-relative text-white">

<a href="mantras-details.php?id=<?php echo $Row['index_id'];?>"  > <div class="region-card-image">

<img src="app/uploads/gods/<?php echo $photos; ?>"

    alt="<?php echo $title; ?>"

    class="img-fluid">

</div>
</a>

<div class="region-card-content d-flex flex-column h-100 position-absolute start-0 top-0 w-100">

<div class="region-card-info">

<h4 class="font-caveat mb-0"> <a href="mantras-details.php?id=<?php echo $Row['index_id'];?>"  ><?php echo $title; ?></a></h4>

<!-- <h3 class="h2">Kingston</h3>

<span>100+ listings</span> -->

</div>

<a href="mantras-details.php?id=<?php echo $Row['index_id'];?>"   class="align-items-center d-flex fw-semibold justify-content-between mt-auto region-card-link">

<div class="fs-12 region-card-link-text text-uppercase text-white">Read more</div>

<div class="align-items-center bg-blur text-white btn-icon-md d-flex end-0 justify-content-center rounded-circle">

    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right" viewbox="0 0 16 16">

        <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

    </svg>

</div>

</a>

</div>

</div>

<!-- end /. region card -->
        </div>

            <?php

                }

            } else {

                echo "<p class='text-center'>No temples found.</p>";

            }

            ?>

        </div>

        <!-- end /. place carousel -->

        <div class="row g-4 mt-5" data-aos="fade-up">

            <div class="col-md-6">

                <h2 class="mb-5">Sacred sounds or chants used in meditation to invoke <span class="">Spiritual energy and focus the mind. </span></h2>

                <img src="assets/images/mantras.jpg" alt="" class="home-about-image w-100 rounded-4 object-fit-cover">

            </div>

            <div class="col-md-6 ps-xxl-5">

                <div class="lead mb-4">Mantras and Stotras showcase the spiritual essence of Indian traditions, blending ancient wisdom with personal devotion, creating profound experiences of worship and inner peace.</div>

                <ul class="d-flex flex-column gap-4 lead mb-4">

                    <li>Mantras are typically short, powerful phrases repeated for spiritual growth.</li>

                    <li>Stotras are poetic hymns expressing reverence for gods and goddesses.</li>

                    <li>Both are central to Hindu worship and meditation practices.</li>

                    <li>Reciting them brings peace, mental clarity, and a connection to divine energy.</li>

                </ul>

                <a href="mantras.php"  class="btn btn-danger mt-4">View all <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                        <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                    </svg></a>

            </div>

        </div>

    </div>

</div>

<!-- end /. Mantras -->

<div class="py-5 position-relative overflow-hidden" style="background:linear-gradient(141.76deg, #F5D9D5 0.59%, #F5EAB4 39.43%, #1f8a4c 100%) !important;">

    <div class="container py-4">

        <div class="row">

            <div class="col-11">

                <!-- Start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- Subtitle -->

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">

                        Temples In Abroad

                    </div>

                    <!-- Description -->

                    <div class="sub-title fs-16">

                        Highlights Hindu temples worldwide, <span class="text-primary fw-semibold">Fostering global spirituality and connection.

                    </div>

                </div>

            </div>

            <div class="col-12 mb-4 col-lg-1" align="right"><br><a href="abroad.php"  class="btn btn-primary">View&nbsp;all&nbsp;<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                        <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                    </svg></a></div>

        </div>



        <!-- Carousel for temple listings -->

        <div class="listings-carousel owl-carousel owl-theme owl-nav-bottom">

            <?php

            // Fetch all data from temples table with limit and offset

            $select = "SELECT * FROM `abroad` WHERE index_id !='0' ORDER BY order_by";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

            ?>

                    <!-- Start listing card -->

                    <div class="card rounded-3 w-100 flex-fill overflow-hidden">

                        <div class="">

                            <a href="abroad.php"   class="stretched-link"></a>

                            <img src="app/uploads/abroad/<?php echo $photos; ?>" alt="<?php echo $title; ?>" class="img-fluid" style="width:100%; height:260px; max-height:300px">

                        </div>



                        <div class="d-flex flex-column h-100 position-relative p-4">

                            <!-- Icon at the top of the card -->

                            <div class="align-items-center bg-primary cat-icon d-flex justify-content-center 

                                        position-absolute rounded-circle text-white">

                                <a href="abroad.php"  class="stretched-link"></a>

                                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24">

                                    <path fill="currentColor" d="M20 11v2h-2L15 3V1h-2v2h-2.03V1h-2v2.12L6 13H4v-2H2v11h9v-5h2v5h9V11zm-4.69 0H8.69l.6-2h5.42zm-1.2-4H9.89l.6-2h3.02zM20 20h-5v-5H9v5H4v-5h3.49l.6-2h7.82l.6 2H20z" />

                                </svg>

                            </div>

                            <!-- Card title -->



                            <h4 class="fs-5 fw-semibold mb-0">

                                <a href="abroad.php"  >

                                    <?php echo $title; ?>

                                </a>

                            </h4>

                            <p><a href="abroad.php"  >Read more</a></p>

                        </div>

                    </div>

                    <!-- End listing card -->

            <?php

                }

            } else {

                echo "<p class='text-center'>No temples found.</p>";

            }

            ?>

        </div>

    </div>

</div>





<!-- start saints section -->

<div class="pt-5 bg-gradient mx-3 rounded-4">

    <div class="container pt-4">

        <div class="row justify-content-center">

            <div class="col-sm-10 col-md-10 col-lg-8 col-xl-7">

                <!-- start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- start subtitle -->

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Saints & Poets</div>

                    <!-- end /. subtitle -->

                    <!-- start description -->

                    <div class="sub-title fs-16">Renowned for their profound spiritual wisdom and timeless verses, <span class="text-primary fw-semibold">they continue to inspire devotion and introspection across generations.</span></div>

                    <!-- end /. description -->

                </div>

                <!-- end /. section header -->

            </div>

        </div>

        <div class="row g-4 justify-content-center work-process service">

            <?php

            // Fetch all data from temples table with limit and offset

            $select = "SELECT * FROM `other_page` WHERE page_id='5' ORDER BY order_by ASC LIMIT 4";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

                    $content = $Row['content'];

                    $index_id  = $Row['index_id'];



                    // Limit content to 100 characters

                    $shortContent = strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;

            ?>

                    <div class="col-sm-6 col-lg-3">

                        <div class="work-process position-relative p-3 aos-init aos-animate" data-aos="fade" data-aos-delay="300">

                            <div class="step-box position-relative d-inline-block mb-4 d-flex gap-3">

                                <img src="app/uploads/others/<?php echo $photos; ?>" alt="<?php echo $title; ?>" class="img-fluid" height="120">

                            </div>

                            <div class="step-desc">

                                <div class="fs-18 fw-semibold mb-1 mt-4"><?php echo $title; ?></div>

                                <p class="fs-15"><?php echo $shortContent; ?></p>



                                <a href="saints-details.php?id=<?php echo htmlspecialchars($Row['index_id']); ?>&page_id=<?php echo htmlspecialchars($Row['page_id']); ?>"  class="align-items-center d-flex fs-13 fw-bold gap-2 l-spacing-1 text-primary text-uppercase">

                                    view

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right" viewBox="0 0 16 16">

                                        <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

                                    </svg>

                                </a>

                            </div>

                        </div>

                    </div>

            <?php

                }

            } else {

                echo "<p class='text-center'>No temples found.</p>";

            }

            ?>

        </div>

        <a href="saints.php?id=5"  class="btn btn-primary mt-3 all" align="center">View All <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

            </svg></a>

    </div>

</div>

<!-- end /.  -->

<!-- start Mystery Temples items -->

<div class="py-5 position-relative overflow-hidden bg-light rounded-4 mx-3 mt-3">

    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="col-sm-10 col-md-10 col-lg-8 col-xl-7">

                <!-- start section header -->

                <div class="section-header text-center mb-5" data-aos="fade-down">

                    <!-- start subtitle -->

                    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Mystery Temples</div>

                    <!-- end /. subtitle -->

                    <!-- start title -->

                    <h2 class="display-5 fw-semibold mb-3 section-header__title text-capitalize">Discover Mystery Temples</h2>

                    <!-- end /. title -->

                    <!-- start description -->

                    <div class="sub-title fs-16">The temple's flag flaps against the wind,<span class="text-primary fw-semibold"> and no birds fly above it, creating an air of mystery.</span></div>

                    <!-- end /. description -->

                </div>

                <!-- end /. section header -->

            </div>

        </div>

        <div class="row g-4">

            <?php

            $mystery_section_has_temples = false;

            // Fetch all data from temples table with limit and offset

            $select = "SELECT * FROM `temples` WHERE my_stery = 1 ORDER BY index_id  DESC LIMIT 1";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                $mystery_section_has_temples = true;

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

                    // $small_description = $Row['small_description'];

            ?>

                    <div class="col-lg-6">

                        <!-- start card list -->

                        <div class="card border-0 shadow-sm overflow-hidden rounded-4 card-hover">

                            <a href="temple-details.php?id=<?php echo $Row['index_id']; ?>"   class="stretched-link"></a>

                            <div class="card-body p-0" style="height: 200px;">

                                <div class="g-0 row">

                                    <div class="bg-white col-lg-5 col-md-5 col-xl-5 position-relative">

                                        <div class="card-image-hover dark-overlay overflow-hidden position-relative" style="height: 200px;">

                                            <!-- start image -->

                                            <img src="app/uploads/temple/<?php echo $photos; ?>"

                                                alt="<?php echo $title; ?>" class="h-100 w-100 object-fit-cover">

                                            <!-- end /. image -->

                                        </div>

                                    </div>

                                    <div class="col-lg-7 col-md-7 col-xl-7 p-3 p-lg-4 p-md-3 p-sm-4">

                                        <div class="d-flex flex-column h-100">



                                            <!-- start card title -->

                                            <h4 class="fs-18 fw-semibold mb-0">

                                                <?php echo $title; ?>

                                            </h4>

                                            <p>

                                                Mystery temples in India are ancient wonders that hold many secrets waiting to be unveiled.</p>

                                            <!-- end /. card title -->

                                            <p><a href="temple-details.php "  >Read more</a></p>

                                        </div>



                                    </div>



                                </div>



                            </div>



                        </div>

                        <!-- end /. card list -->



                    </div>

            <?php

                }

            } else {

                // Don't show message here; show once at end if no section has temples

            }

            ?>

            <?php

            // Fetch all data from temples table with limit and offset

            $select = "SELECT * FROM `abroad` WHERE my_stery = 1 ORDER BY index_id  DESC LIMIT 1";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                $mystery_section_has_temples = true;

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

                    // $small_description = $Row['small_description'];

            ?>

                    <div class="col-lg-6">

                        <!-- start card list -->

                        <div class="card border-0 shadow-sm overflow-hidden rounded-4 card-hover">

                            <a href="abroad-details.php?id=<?php echo $Row['index_id']; ?>"  class="stretched-link"></a>

                            <div class="card-body p-0" style="height: 200px;">

                                <div class="g-0 row">

                                    <div class="bg-white col-lg-5 col-md-5 col-xl-5 position-relative">

                                        <div class="card-image-hover dark-overlay overflow-hidden position-relative" style="height: 200px;">

                                            <!-- start image -->

                                            <img src="app/uploads/abroad/<?php echo $photos; ?>"

                                                alt="<?php echo $title; ?>" class="h-100 w-100 object-fit-cover">

                                            <!-- end /. image -->

                                        </div>

                                    </div>

                                    <div class="col-lg-7 col-md-7 col-xl-7 p-3 p-lg-4 p-md-3 p-sm-4">

                                        <div class="d-flex flex-column h-100">



                                            <!-- start card title -->

                                            <h4 class="fs-18 fw-semibold mb-0">

                                                <?php echo $title; ?>

                                            </h4>

                                            <p>

                                                Mystery temples in Abroad are ancient wonders that hold many secrets waiting to be unveiled.</p>

                                            <!-- end /. card title -->

                                            <p><a href="abroad-details.php"  >Read more</a></p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- end /. card list -->



                    </div>

            <?php

                }

            } else {

                // Don't show message here; show once at end if no section has temples

            }

            ?>

            <?php

            // Fetch all data from temples table with limit and offset

            $select = "SELECT * FROM `iconic` WHERE my_stery = 1 ORDER BY index_id  DESC LIMIT 2";

            $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);



            // Check if any rows are returned

            if (mysqli_num_rows($SQL_STATEMENT) > 0) {

                $mystery_section_has_temples = true;

                while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {

                    $photos = $Row['photos'];

                    $title = $Row['title'];

                    // $small_description = $Row['small_description'];

            ?>

                    <div class="col-lg-6">

                        <!-- start card list -->

                        <div class="card border-0 shadow-sm overflow-hidden rounded-4 card-hover">

                            <a href="iconic-category-details.php?id=<?php echo $Row['index_id']; ?>"   class="stretched-link"></a>

                            <div class="card-body p-0" style="height: 200px;">

                                <div class="g-0 row">

                                    <div class="bg-white col-lg-5 col-md-5 col-xl-5 position-relative">

                                        <div class="card-image-hover dark-overlay overflow-hidden position-relative" style="height: 200px;">

                                            <!-- start image -->

                                            <img src="app/uploads/iconic/<?php echo $photos; ?>"

                                                alt="<?php echo $title; ?>" class="h-100 w-100 object-fit-cover">

                                            <!-- end /. image -->

                                        </div>

                                    </div>

                                    <div class="col-lg-7 col-md-7 col-xl-7 p-3 p-lg-4 p-md-3 p-sm-4">

                                        <div class="d-flex flex-column h-100">



                                            <!-- start card title -->

                                            <h4 class="fs-18 fw-semibold mb-0">

                                                <?php echo $title; ?>

                                            </h4>

                                            <p>

                                                Mystery temples in Iconic are ancient wonders that hold many secrets waiting to be unveiled.</p>

                                            <!-- end /. card title -->

                                            <p  ><a href="iconic-category-details.php"  >Read more</a></p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            </a>

                        </div>

                        <!-- end /. card list -->



                    </div>

            <?php

                }

            } else {

                // Don't show message here; show once below if no section has temples

            }

            if (!$mystery_section_has_temples) {

                echo "<p class='text-center'>No temples found.</p>";

            }

            ?>

        </div>

        <a href="mystery.php" class="btn btn-primary mt-3 all"  align="center">View All <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right mb-1" viewBox="0 0 16 16">

                <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0v-6z"></path>

            </svg></a>

    </div>

</div>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


<div class="gallery-container">

    <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary">Photo Gallery</div>
      

    <!-- Filter Buttons -->

    <div class="filter-buttons">

        <button class="filter-btn active" data-filter="all">All</button>

        <?php

        // Dynamically generate filter buttons

        if ($titleResult->num_rows > 0) {

            while ($row = $titleResult->fetch_assoc()) {

                $title = $row['title'];

                echo "<button class='filter-btn' data-filter='" . htmlspecialchars($title) . "'>" . htmlspecialchars($title) . "</button>";

            }

        }

        ?>

    </div>
<?php

$images = [];

while ($row = $galleryResult->fetch_assoc()) {
    foreach (explode(',', $row['photos']) as $photo) {
        if (!empty($photo)) {
            $images[] = 'app/uploads/gallery/' . trim($photo);
        }
    }
}

$half = ceil(count($images) / 2);
$topRow = array_slice($images, 0, $half);
$bottomRow = array_slice($images, $half);
?>
<div class="gallery-wrapper">
<div class="swiper gallery-swiper top">
    <div class="swiper-wrapper">
        <?php foreach ($topRow as $img) { ?>
            <div class="swiper-slide">
                <img src="<?= htmlspecialchars($img) ?>">
            </div>
        <?php } ?>
    </div>
</div>

<div class="swiper gallery-swiper bottom">
    <div class="swiper-wrapper">
        <?php foreach ($bottomRow as $img) { ?>
            <div class="swiper-slide">
                <img src="<?= htmlspecialchars($img) ?>">
            </div>
        <?php } ?>
    </div>
</div>
</div>



</div>

<style>
  .gallery-swiper {
    width: 100%;
    padding: 10px 0;
}

.swiper-slide {
    width: 280px !important;
}

.swiper-slide img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 8px;
}
</style>

<!-- Popup Modal -->

<div class="image-popup">

    <button class="close-popup">&times;</button>

    <button class="nav-arrow prev">&lt;</button>

    <img src="" alt="Popup Image" class="popup-img">

    <button class="nav-arrow next">&gt;</button>

</div>


<script>

    const filterButtons = document.querySelectorAll('.filter-btn');

    const galleryItems = document.querySelectorAll('.gallery-item');

    const imageLimit = 18; // Set the limit of images for the "All" filter



    // Default display behavior: Show only 9 images initially

    window.addEventListener('load', () => {

        let displayedCount = 0;



        galleryItems.forEach(item => {

            if (displayedCount < imageLimit) {

                item.style.display = 'block'; // Show image

                displayedCount++;

            } else {

                item.style.display = 'none'; // Hide extra images

            }

        });

    });



    // Filter buttons functionality

    filterButtons.forEach(button => {

        button.addEventListener('click', () => {

            const filter = button.getAttribute('data-filter');



            // Update active button

            filterButtons.forEach(btn => btn.classList.remove('active'));

            button.classList.add('active');



            let displayedCount = 0; // Counter for images displayed



            galleryItems.forEach(item => {

                if (filter === 'all') {

                    // Show only 9 images for the "All" filter

                    if (displayedCount < imageLimit) {

                        item.style.display = 'block';

                        displayedCount++;

                    } else {

                        item.style.display = 'none';

                    }

                } else {

                    // Show all images for other filters

                    if (filter === item.getAttribute('data-category')) {

                        item.style.display = 'block';

                    } else {

                        item.style.display = 'none';

                    }

                }

            });

        });

    });



    // Popup functionality

    const popup = document.querySelector('.image-popup');

    const popupImg = document.querySelector('.popup-img');

    const closePopup = document.querySelector('.close-popup');

    const prevArrow = document.querySelector('.prev');

    const nextArrow = document.querySelector('.next');



    let currentImgIndex = 0;



    // Open popup

    galleryItems.forEach((item, index) => {

        item.addEventListener('click', () => {

            currentImgIndex = index;

            showPopup(index);

        });

    });



    function showPopup(index) {

        const imgSrc = galleryItems[index].querySelector('img').src;

        popupImg.src = imgSrc;

        popup.classList.add('active');

    }



    // Close popup

    closePopup.addEventListener('click', () => {

        popup.classList.remove('active');

    });



    // Navigate images

    prevArrow.addEventListener('click', () => {

        currentImgIndex = (currentImgIndex - 1 + galleryItems.length) % galleryItems.length;

        showPopup(currentImgIndex);

    });



    nextArrow.addEventListener('click', () => {

        currentImgIndex = (currentImgIndex + 1) % galleryItems.length;

        showPopup(currentImgIndex);

    });

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">

    let debounceTimeout;



    // Debounce function to optimize performance

    function debounce(func, delay) {

        clearTimeout(debounceTimeout);

        debounceTimeout = setTimeout(func, delay);

    }



    // Handle input change on keyup

    $("#fetchCourse").on("keyup", function() {

        let searchText = $(this).val().trim();

        console.log(searchText);

        debounce(function() {

            if (searchText !== "") {

                $.ajax({

                    url: "fetch_temple.php",

                    method: "POST",

                    data: {

                        query: searchText

                    },

                    success: function(response) {

                        console.log(response);

                        $("#show-list").html(response);



                    },

                    error: function() {

                        console.error("Error fetching data.");

                    }

                });

            } else {

                $("#show-list").html("");

            }

        }, 300); // 300ms debounce time

    });



    // Set searched text into input and clear results on item click

    $(document).on("click", ".list-group-item", function() {

        $("#fetchCourse").val($(this).text());

        $("#show-list").html("");

    });

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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<?php include_once './include/live-darshan-list.php' ?>

<?php include_once './include/footer.php' ?>

<script>
new Swiper('.gallery-swiper.top', {
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

new Swiper('.gallery-swiper.bottom', {
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
      toggleButton.innerHTML = '<i class="fa fa-volume-up"></i> Play';
    };
    utterance.onerror = function() {
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-volume-up"></i> Play';
    };
    synth.speak(utterance);
    isPlaying = true;
    toggleButton.innerHTML = '<i class="fa fa-stop"></i> Stop';
  }

  toggleButton.addEventListener('click', function() {
    if (isPlaying) {
      synth.cancel();
      isPlaying = false;
      toggleButton.innerHTML = '<i class="fa fa-play"></i> Play';
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