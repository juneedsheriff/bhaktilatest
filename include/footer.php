<?php
$select = "SELECT * FROM `private_ads` WHERE is_active = 1 ORDER BY sort_order ASC";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

$mantras_footer_cats = [];
if (!empty($DatabaseCo->dbLink)) {
    $mq = @mysqli_query($DatabaseCo->dbLink, "SELECT index_id, title FROM mantras_subcategory WHERE index_id != '0' ORDER BY order_by ASC, index_id ASC");
    if ($mq && mysqli_num_rows($mq) > 0) {
        while ($mr = mysqli_fetch_assoc($mq)) {
            $mantras_footer_cats[] = $mr;
        }
    }
}
$mantras_cols = 6;
$mantras_chunks = !empty($mantras_footer_cats) ? array_chunk($mantras_footer_cats, (int) ceil(count($mantras_footer_cats) / $mantras_cols)) : [];
?>
<section class="ad-section">
    <div class="container">
        <!-- Slider Container -->
        <div class="owl-carousel ad-slider">
           <?php if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                    while ($Row = mysqli_fetch_assoc($SQL_STATEMENT)) {
                        $id       = $Row['id'];
                        $title    = $Row['title'];
                        $image    = !empty($Row['image_path']) ? $Row['image_path'] : "";
                        $url      = !empty($Row['external_url']) ? $Row['external_url'] : "#";
                        if($image){
                        ?>
                        
                        <div class="item" >
                            <a href="<?php echo htmlspecialchars($url); ?>" data-id="<?php echo $id; ?>"  class="ad-click">
                                <img src="app/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                            </a>
                        </div>

                        <?php
                        }
                    }
                } ?>
        </div>
    </div>
</section>

<style>
    .ad-section{
        padding:40px 0;
    }
     /* Center dots */
    .ad-slider .owl-dots {
      text-align: center;
      margin-top: 10px;
    }

    .ad-slider .owl-dot {
      display: inline-block;
      margin: 0 5px;
    }

    .ad-slider .owl-dot span {
      width: 12px;
      height: 12px;
      background: #bbb;
      display: block;
      border-radius: 50%;
      transition: background 0.3s ease;
    }

    .ad-slider .owl-dot.active span {
      background: #333;
    }
    .ad-slider .item{
    padding: 10px;
    background: #ccc;
    display: block;
    width: 100%;
    border-radius: 8px;
    
}
.iti__country, .iti__selected-dial-code{
    color: #000 !important;
}
.iti--allow-dropdown{
    width: 100%
}
.iti--separate-dial-code .iti__selected-flag{
    background: none !important;
}
.any-corrections-section {
    text-align: center;
    padding: 2rem 1rem 1.5rem;
    margin: 0;
}
.anycorrections {
    font-size: 1.5rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.95);
    margin: 0;
    letter-spacing: 0.02em;
    text-decoration: underline;
    text-transform: uppercase;
}
.footer-mantras-menu {
    padding: 2.5rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.footer-mantras-menu .mantras-menu-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: rgba(255,255,255,0.95);
    text-align: center;
    margin-bottom: 1.5rem;
    letter-spacing: 0.02em;
}
.footer-mantras-menu .mantras-menu-col {
    font-size: 0.95rem;
    line-height: 1.9;
}
.footer-mantras-menu .mantras-menu-col a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    display: block;
}
.footer-mantras-menu .mantras-menu-col a:hover {
    color: #fff;
    text-decoration: underline;
}
</style>

<footer class="footer-dark main-footer overflow-hidden position-relative pt-5">

    <?php if (!empty($mantras_chunks)): ?>
    <div class="footer-mantras-menu">
        <div class="container">
            <h2 class="mantras-menu-title">Mantras & Stotras</h2>
            <div class="row g-4">
                <?php foreach ($mantras_chunks as $col_items): ?>
                <div class="col-6 col-md-4 col-lg-2 mantras-menu-col">
                    <?php foreach ($col_items as $cat): ?>
                    <a href="mantras-details.php?id=<?php echo (int)$cat['index_id']; ?>"><?php echo htmlspecialchars($cat['title']); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container pt-4">

        <div class="bg-primary rounded-4 p-4">

            <div class="container">

                <div class="row align-items-center">

                    <div class="col-md-2 text-center">

                        <img class="img-fluid img-responsive" src="assets/images/foo-temple.png" alt="Bhaktikalpa">

                    </div>

                    <div class="col-md-10 text-white text-center text-md-start mt-3 mt-md-0">

                        <h4>Subscribe Now</h4>

                        <p class="mb-0">

                            Stay connected with Bhaktikalpa – subscribe now for inspiring insights, events, and spiritual updates delivered right to you!

                        </p>
                        <div class="col-md-12">

<div class="row">

    <!-- Email Input -->

    <div class="col-12 col-md-4">

        <div class="newsletter position-relative mt-3">

            <input type="email" id="emailInput" class="form-control" placeholder="Your Email..." required>

        </div>

    </div>



    <!-- Phone Input with Country Code Mask -->

    <div class="col-12 col-md-4">

        <div class="newsletter position-relative mt-3">

        
        <input id="phone" type="tel" class="form-control" required >
            <div id="merrmsg" class="small text-danger mt-1" role="alert" aria-live="polite"></div>

        </div>

    </div>



    <!-- Submit Button -->

    <div class="col-12 col-md-4">

        <div class="newsletter position-relative mt-4">

            <button type="button" id="submitForm" class="btn btn-secondary">

                Submit

                <i class="fa-solid fa-angle-right ms-5"></i>

            </button>

        </div>

    </div>

</div>



</div>
                    </div>

             

                </div>

            </div>

        </div>

<div class="any-corrections-section">
            <!-- <h2 class="anycorrections"> Any Corrections</h2> -->
            <a class="anycorrections"   href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#correctionModal">Send your corrections</a>
          
        </div>
     

        <div class="border-top py-5">

            <?php
            $footerIconicLinks = [
                ['label' => 'Abhimana Kshetras', 'href' => '#'],
                ['label' => 'Abodes Of Murugan', 'href' => 'iconic-category-details.php?id=26'],
                ['label' => 'Aditya Temples', 'href' => 'iconic-category-details.php?id=54'],
                ['label' => 'Ashta Veeratta Temples', 'href' => 'iconic-category-details.php?id=14'],
                ['label' => 'Ashta Vinayaka Temples', 'href' => 'iconic-category-details.php?id=13'],
                ['label' => 'Athara Sthalams', 'href' => 'iconic-category-details.php?id=25'],
                ['label' => 'Char Dham', 'href' => 'iconic-category-details.php?id=35'],
                ['label' => 'Divya Desams', 'href' => 'iconic-category-details.php?id=36'],
                ['label' => 'Durga Aalayams', 'href' => 'iconic-category-details.php?id=57'],
                ['label' => 'Jyorthirlinga Temples', 'href' => 'iconic-category-details.php?id=1'],
                ['label' => 'Muktishetras', 'href' => 'iconic-category-details.php?id=50'],
                ['label' => 'Naga Devatas Temples', 'href' => 'iconic-category-details.php?id=15'],
                ['label' => 'Narasimha Skhetras', 'href' => 'iconic-category-details.php?id=4'],
                ['label' => 'Nava Dwaraka Temples', 'href' => 'iconic-category-details.php?id=41'],
                ['label' => 'Nava Puliyur Temples', 'href' => 'iconic-category-details.php?id=19'],
                ['label' => 'Nava Tirupati Temples', 'href' => 'iconic-category-details.php?id=38'],
                ['label' => 'Navagraha Parihara Temples', 'href' => 'iconic-category-details.php?id=17'],
                ['label' => 'Paadal Petra Sthalams', 'href' => 'iconic-category-details.php?id=28'],
                ['label' => 'Pancha Bootha Sthalams', 'href' => 'iconic-category-details.php?id=2'],
                ['label' => 'Pancha Kannan Temples', 'href' => 'iconic-category-details.php?id=30'],
                ['label' => 'Pancha Kedar Temples', 'href' => 'iconic-category-details.php?id=33'],
                ['label' => 'Pancha Pandava Skhetras', 'href' => 'iconic-category-details.php?id=31'],
                ['label' => 'Pancha Rama Kshetras', 'href' => 'iconic-category-details.php?id=3'],
                ['label' => 'Pancha Ranga Kshetras', 'href' => 'iconic-category-details.php?id=29'],
                ['label' => 'Pancha Sabhai Thalangal', 'href' => 'iconic-category-details.php?id=32'],
                ['label' => 'Saptha Mangai Stalangal', 'href' => 'iconic-category-details.php?id=44'],
                ['label' => 'Saptha Stana Temples', 'href' => 'iconic-category-details.php?id=45'],
                ['label' => 'Saptha Vidangam', 'href' => 'iconic-category-details.php?id=42'],
                ['label' => 'Sapthavigraha Moorthis', 'href' => 'iconic-category-details.php?id=46'],
                ['label' => 'Sastha Aalayams', 'href' => 'iconic-category-details.php?id=47'],
                ['label' => 'Shaktipeetams', 'href' => 'iconic-category-details.php?id=43'],
                ['label' => 'Shivalayams', 'href' => 'iconic-category-details.php?id=48'],
                ['label' => 'Shiridi Sai Temples', 'href' => 'https://saikalpa.com/'],
                ['label' => 'Swayambhu Temples', 'href' => 'iconic-category-details.php?id=49'],
                ['label' => 'Village Dieties', 'href' => 'iconic-category-details.php?id=51'],
                ['label' => 'Vishnumaya Temples', 'href' => 'iconic-category-details.php?id=52'],
            ];
            usort($footerIconicLinks, static function ($a, $b) {
                return strcasecmp($a['label'], $b['label']);
            });
            $footerIconicColumnCount = 4;
            $footerIconicPerColumn = (int) ceil(count($footerIconicLinks) / $footerIconicColumnCount);
            $footerIconicColumns = array_chunk($footerIconicLinks, $footerIconicPerColumn);
            ?>
            <div class="footer-row row gy-5 g-sm-5 gx-xxl-6">
                <?php foreach ($footerIconicColumns as $footerIconicColumn) : ?>
                <div class="border-end col-lg-3 col-md-4 col-6">
                    <?php foreach ($footerIconicColumn as $footerIconicLink) :
                        $footerLinkHref = htmlspecialchars((string) $footerIconicLink['href'], ENT_QUOTES, 'UTF-8');
                        $footerLinkLabel = htmlspecialchars((string) $footerIconicLink['label'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <h5 class="fw-bold mb-4"><a href="<?php echo $footerLinkHref; ?>"><?php echo $footerLinkLabel; ?></a></h5>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>

    <div class="container border-top">

        <div class="align-items-center g-3 py-4 row">

            <div class="col-lg-auto">

                <div>

                    <ul class="d-flex flex-wrap gap-2 list-unstyled mb-0 social-icon">

                        <li>

                            <a  href="#" class="rounded-circle align-items-center d-flex fs-19 icon-wrap justify-content-center rounded-2 text-white fb">

                                <i class="fab fa-facebook-f"></i>

                            </a>

                        </li>

                        <li>

                            <a  href="#" class="rounded-circle align-items-center d-flex fs-19 icon-wrap justify-content-center rounded-2 text-white inst">

                                <i class="fab fa-instagram"></i>

                            </a>

                        </li>

                        <li>

                            <a  href="#" class="rounded-circle align-items-center d-flex fs-19 icon-wrap justify-content-center rounded-2 text-white whatsapp">

                                <i class="fa-brands fa-whatsapp"></i>

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

            <div class="col-lg order-md-first">

                <div class="align-items-center row">

                    <!-- start footer logo -->

                    <a href="index.php" class="col-sm-auto footer-logo mb-2 mb-sm-0">

                        <img src="assets/images/logo/bakthi-logo.png" alt="">

                    </a>

                    <!-- end /. footer logo -->

                    <!-- start text -->

                    <div class="col-sm-auto copy">&copy; <?php echo date("Y"); ?> Bhaktikalpa - All Rights Reserved.</div>

                    <!-- end /. text -->

                </div>

            </div>

        </div>

    </div>

</footer>

<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <img id="modalImage" src="" alt="Gallery Image" class="img-fluid rounded">

            </div>

        </div>

    </div>

</div>


<div id="ContentPlaceHolder1_soc" class="social-buttons">
   <a href="#" class="social-button social-button--facebook" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
   <!-- <a href="#" class="social-button social-button--linkedin" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a>
   <a href="#" class="social-button social-button--twitter" aria-label="twitter"><i class="fa fa-twitter"></i></a> -->
   <a href="#" class="social-button social-button--instagram" aria-label="instagram"><i class="fa fa-instagram"></i></a>
   <a href="#" class="social-button social-button--whatsapp" aria-label="instagram"><i class="fa fa-whatsapp"></i></a>

 

 
</div>

<style>
    .social-buttons {
       display: list;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    position: fixed;
    margin-left: 50px;
    margin-top: 30px;
    top: 50%;
    z-index: 99999;
    top: 50%;
    transform: translateY(-50%);
}
    .social-button {
    position: relative;
    display: flex
;
    justify-content: center;
    align-items: center;
    outline: none;
    width: 36px;
    height: 36px;
    text-decoration: none;
    border-radius: 100%;
    background: #fff;
    text-align: center;
    background-color: #000;
    margin-bottom: 10px;
    color:#fff;
}
.social-button::after {
    content: "";
    position: absolute;
    top: -1px;
    left: 50%;
    display: block;
    width: 0;
    height: 0;
    border-radius: 100%;
    transition: 0.3s;
}

.social-button--linkedin::after {
    background: #0077b5;
}
.social-button--facebook::after {
    background: #3b5999;
}
.social-button--twitter::after {
    background: #55acee;
}
.social-button--instagram::after {
    background: #e4405f;
}
.social-button:focus::after, .social-button:hover::after {
    width: calc(100% + 2px);
    height: calc(100% + 2px);
    margin-left: calc(-50% - 1px);
}
.social-button i, .social-button svg {
    position: relative;
    z-index: 1;
    transition: 0.3s;
}
@media(max-width:768px){
    .social-buttons{
        display:none;
    }
}
.sticky-download-btn,
.sticky-sankalp-btn {
    position: fixed;
    right: 0;
    transform: translateY(-50%);
    writing-mode: vertical-rl;
    text-orientation: mixed;
    color: #fff;
    padding: 14px 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 8px 0 0 8px;
    box-shadow: -2px 2px 12px rgba(0, 0, 0, 0.25);
    z-index: 9999;
    transition: background 0.3s ease, padding 0.3s ease;
    white-space: nowrap;
    letter-spacing: 0.02em;
}

.sticky-sankalp-btn {
    top: 40%;
    background: #B8860B;
}

.sticky-download-btn {
    top: 60%;
    background: #fb523a;
}

.sticky-sankalp-btn:hover {
    background: #9A7209;
    color: #fff;
    padding: 18px 10px;
}

.sticky-download-btn:hover {
    background: #e55d00;
    color: #fff;
    padding: 18px 10px;
}

@media (max-width: 768px) {
    .sticky-download-btn,
    .sticky-sankalp-btn {
        padding: 7px 5px;
        font-size: 12px;
    }

    .sticky-download-btn:hover,
    .sticky-sankalp-btn:hover {
        padding: 7px 5px;
    }
}
</style>

<a href="bhakti-sankalp.php" class="sticky-sankalp-btn">
    Bhakti Sankalp
</a>

<a href="mantra-book-creation-v2.php" class="sticky-download-btn">
    Download Mantras
</a>

<!-- end /. footer -->

<!-- Optional JavaScript -->

<script src="assets/plugins/jQuery/jquery.min.js"></script>

<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="assets/plugins/aos/aos.min.js"></script>

<script src="assets/plugins/macy/macy.js"></script>

<script src="assets/plugins/simple-parallax/simpleParallax.min.js"></script>

<script src="assets/plugins/OwlCarousel2/owl.carousel.min.js"></script>

<script src="assets/plugins/theia-sticky-sidebar/ResizeSensor.min.js"></script>

<script src="assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.min.js"></script>

<script src="assets/plugins/waypoints/jquery.waypoints.min.js"></script>

<script src="assets/plugins/counter-up/jquery.counterup.min.js"></script>

<script src="assets/plugins/jquery-fancyfileuploader/fancy-file-uploader/jquery.ui.widget.js"></script>

<script src="assets/plugins/jquery-fancyfileuploader/fancy-file-uploader/jquery.fileupload.js"></script>

<script src="assets/plugins/jquery-fancyfileuploader/fancy-file-uploader/jquery.iframe-transport.js"></script>

<script src="assets/plugins/jquery-fancyfileuploader/fancy-file-uploader/jquery.fancy-fileupload.js"></script>

<script src="assets/plugins/ion.rangeSlider/ion.rangeSlider.min.js"></script>

<script src="assets/plugins/magnific-popup/jquery.magnific-popup.min.js"></script>

<script src="assets/plugins/select2/select2.min.js"></script>

<script src="assets/js/script.js"></script>

<script src="assets/js/listing-map.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>

    function openNav() {

        document.getElementById("mySidenav").style.width = "250px";

    }



    function closeNav() {

        document.getElementById("mySidenav").style.width = "0";

    }

</script>



<script>

    // Function to filter gallery based on category

    function filterGallery(category) {

        let items = document.getElementsByClassName('gallery-item');

        for (let i = 0; i < items.length; i++) {

            items[i].style.display = category === 'all' || items[i].classList.contains(category) ? 'block' : 'none';

        }

    }

</script>

<script>

    function togglePlayPause() {

        const audio = document.getElementById("audioPlayer");

        const icon = document.getElementById("playPauseIcon");

        if (!audio || !icon) return;



        if (audio.paused) {

            audio.play();

            icon.classList.remove("fa-play");

            icon.classList.add("fa-pause");

        } else {

            audio.pause();

            icon.classList.remove("fa-pause");

            icon.classList.add("fa-play");

        }

    }



    // Reset icon when audio ends

    (function () {

        const audio = document.getElementById("audioPlayer");

        const icon = document.getElementById("playPauseIcon");

        if (!audio || !icon) return;

        audio.addEventListener("ended", () => {

            icon.classList.remove("fa-pause");

            icon.classList.add("fa-play");

        });

    })();

</script>



<script>

    // Function to copy the current page link

    // function copyLink() {

    //     navigator.clipboard.writeText(window.location.href).then(() => {

    //         alert("Link copied to clipboard!");

    //     }).catch(err => {

    //         console.error('Failed to copy: ', err);

    //     });

    // }

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<script>

    // Copy content to WhatsApp

    function shareToWhatsApp() {

        //const content = document.getElementById("printable-content").innerText;

        const url = `https://wa.me/?text=` + document.URL;

        window.open(url, '_blank');

    }



    // Download content as PDF

    function downloadPDF() {

        const {

            jsPDF

        } = window.jspdf;

        const pdf = new jsPDF({

            orientation: 'p', // Portrait mode

            unit: 'mm', // Units in millimeters

            format: 'a4' // A4 paper size

        });



        const content = document.getElementById("printable-content").innerText;



        // Define page margins and line height

        const marginLeft = 20;

        const marginTop = 20;

        const pageWidth = pdf.internal.pageSize.getWidth() - marginLeft * 2; // Width excluding margins

        const lineHeight = 10; // Space between lines

        const textHeight = pdf.splitTextToSize(content, pageWidth); // Automatically split long text into multiple lines



        // Print text and handle multi-page content

        let cursorY = marginTop;



        textHeight.forEach((line) => {

            if (cursorY + lineHeight > pdf.internal.pageSize.getHeight() - marginTop) {

                pdf.addPage(); // Add a new page if the content exceeds the current page height

                cursorY = marginTop;

            }

            pdf.text(line, marginLeft, cursorY);

            cursorY += lineHeight;

        });



        // Save the generated PDF

        pdf.save("temple-details.pdf");

    }





    // Copy content to clipboard

    function copyContent() {

        const content = document.getElementById("printable-content").innerText;

        navigator.clipboard.writeText(content).then(() => {

            alert("Content copied to clipboard!");

        }).catch(err => {

            console.error("Failed to copy: ", err);

        });

    }

</script>

<script type="text/javascript">

    // function googleTranslateElementInit() {

    //     new google.translate.TranslateElement({
    //         pageLanguage: 'en',
    //         includedLanguages: 'en,hi,kn,ml,bn,ta,te'
    //     }, 'google_translate_element');

    // }



    // function toggleTranslate() {

    //     const translateElement = document.getElementById('google_translate_element');

    //     translateElement.style.display = (translateElement.style.display === 'none' || translateElement.style.display === '') ? 'block' : 'none';

    // }


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

<script>

    // Print only the specific content without social icons and other sections

    function printContent() {

        const printContents = document.getElementById("printable-content").innerHTML;

        const originalContents = document.body.innerHTML;



        document.body.innerHTML = printContents;

        window.print();

        document.body.innerHTML = originalContents;

    }

   <?php if(isset($id)){?>  

    $('#submit-comment').submit(function(event){

        event.preventDefault(); 

        var name = $('#name').val();

        var comment = $('#comment').val();

        var type = $('#type').val();

        var id = <?php echo $id;?>;

        $.ajax({

            url: 'ajax.php',

            type: 'POST',

            data: { name: name, comment: comment, ty: type, id: id },

            success: function(response) {

                console.log(response);

                $('#success-message').removeClass('d-none');

                $('#name').val('');

                $('#comment').val('');

            }

        });

    });

    <?php }?>

$(".ad-slider").owlCarousel({
        loop: true,
        margin: 10,
        nav: false,
        autoplay: true,
        autoHeight:true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        responsive:{
          0:{ items:1 },
          600:{ items:2 },
          1000:{ items:4 }
        }
      });

</script>

<script>
$(document).ready(function(){

    // Capture Views (when ads are displayed)
    $(".ad-card").each(function(e){
       
        e.preventDefault();
        let adId = $(this).data("id");

        $.post("ajax_private_ads.php", { id: adId,action:'UpdateViews' }, function(response){
            console.log("View updated for Ad ID:", adId, response);
        });
    });

    // Capture Clicks (when ad is clicked)
    $("body").on("click", '.ad-click', function(e) {
        e.preventDefault();

        let adId = $(this).closest(".ad-card").data("id");  // get ad ID
        let targetUrl = $(this).attr("href");               // get the ad URL

        $.post("ajax_private_ads.php", { id: adId, action: 'UpdateClicks' }, function(response) {
            console.log("Click updated for Ad ID:", adId, response);

            // Open in new tab/window after updating clicks
            window.open(targetUrl, "_blank");
        });
    });

});
</script>

<script>
$(function(){
    var correctionModal = document.getElementById("correctionModal");
    if (correctionModal) {
        correctionModal.addEventListener("show.bs.modal", function(){
            $("#correctionResponse").html("");
            var $pageUrl = $("#page_url");
            if ($pageUrl.length) {
                $pageUrl.val(window.location.href);
            }
        });
    }
    $(document).on("submit", "#correctionForm", function(e){
        e.preventDefault();
        var $form = $(this);
        var $response = $("#correctionResponse");
        var actionUrl = $form.attr("data-action") || "correction_submit.php";
        $response.html("");

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: $form.serialize(),
            dataType: "json",
            success: function(res){
                if(res && res.status === "success"){
                    $response.html("<span style='color:green;'>" + (res.message || "Submitted successfully.") + "</span>");
                    $form[0].reset();
                    $("#page_url").val(window.location.href);
                    if (correctionModal) {
                        var modal = bootstrap.Modal.getInstance(correctionModal);
                        if (modal) setTimeout(function(){ modal.hide(); }, 1500);
                    }
                } else {
                    $response.html("<span style='color:red;'>" + (res && res.message ? res.message : "Something went wrong.") + "</span>");
                }
            },
            error: function(xhr, status, err){
                $response.html("<span style='color:red;'>Request failed. Please try again or check your connection.</span>");
                console.error("Correction form error:", status, err, xhr.responseText);
            }
        });
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"></script>

<script>
$(document).ready(function() {
  var phoneInput = document.querySelector("#phone");
  var iti = null;
  if (phoneInput && window.intlTelInput) {
    iti = window.intlTelInput(phoneInput, {
      initialCountry: "in",
      separateDialCode: true,
      preferredCountries: ["in", "us", "gb", "ae", "au", "sa"],
      utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
    });
  }

  $('#submitForm').on('click', function(e) {
    e.preventDefault();

    var email = $('#emailInput').val().trim();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    $('#merrmsg').html('').hide();

    // Email validation
    if (!email) {
      toastr.error('Please enter your email.');
      $('#emailInput').focus();
      return false;
    }
    if (!emailPattern.test(email)) {
      toastr.error('Please enter a valid email address.');
      $('#emailInput').focus();
      return false;
    }

    // Phone validation (use intl-tel-input if available, else fallback)
    var fullPhone = '';
    if (iti) {
      try {
        if (!iti.isValidNumber()) {
          $('#merrmsg').html('Please enter a valid phone number with country code.').show();
          toastr.error('Please enter a valid phone number.');
          if (phoneInput) phoneInput.focus();
          return false;
        }
        fullPhone = iti.getNumber().replace(/\D/g, '');
      } catch (err) {
        fullPhone = (phoneInput && phoneInput.value) ? phoneInput.value.replace(/\D/g, '') : '';
      }
    } else {
      var raw = (phoneInput && phoneInput.value) ? phoneInput.value.replace(/\D/g, '') : '';
      if (raw.length < 8 || raw.length > 15) {
        $('#merrmsg').html('Please enter a valid phone number (8–15 digits).').show();
        toastr.error('Please enter a valid phone number.');
        if (phoneInput) phoneInput.focus();
        return false;
      }
      fullPhone = raw;
    }

    if (!fullPhone || fullPhone.length < 10) {
      $('#merrmsg').html('Please enter a valid phone number.').show();
      toastr.error('Please enter a valid phone number.');
      return false;
    }

    $.ajax({
      url: 'submit_email.php',
      type: 'POST',
      dataType: 'json',
      data: { email: email, phone: fullPhone },
      success: function(res) {
        if (res.status === 'exists' && res.email_exists) {
          toastr.warning(res.message || 'This email is already subscribed.');
          return;
        }
        if (res.status === 'success') {
          $('#emailInput').val('');
          if (iti) iti.setNumber('');
          $('#merrmsg').html('').hide();
          toastr.success(res.message || 'Subscription successful.');
          return;
        }
        toastr.error(res.message || 'Something went wrong.');
      },
      error: function() {
        toastr.error('Subscription failed. Please try again.');
      }
    });
    return false;
  });
});
</script>
</body>



</html>