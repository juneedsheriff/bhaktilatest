<?php
    error_reporting(1);
    include('./include/header.php');
    include_once './include/saints_media.php';

    // Include required classes
    include_once './app/class/XssClean.php';
    include_once './app/class/databaseConn.php';
    include_once './app/lib/requestHandler.php';

    $DatabaseCo = new DatabaseConn();
    $xssClean = new xssClean();
    $id = $xssClean->clean_input($_REQUEST['id']);
    $page_id = $xssClean->clean_input($_REQUEST['page_id']);

    // Fetch temple details for the provided id
    $select = "SELECT * FROM `other_page` WHERE index_id='$id'";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    // Check if the query returns a result
    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        $Row = mysqli_fetch_object($SQL_STATEMENT);
    } else {
        echo "<p>Temple not found.</p>";
        exit;
    }

    $detailTitle = $Row->title;
    $detailPageId = $Row->page_id;
    $bannerSrc = saints_banner_src($Row->banner ?? '', $detailPageId, $DatabaseCo->dbLink, $detailTitle);
    if ($bannerSrc === SAINTS_DEFAULT_IMAGE && trim((string) ($Row->photos ?? '')) !== '') {
        $bannerSrc = saints_photo_src($Row->photos, $detailPageId, $DatabaseCo->dbLink, $detailTitle);
    }
    $isPoetDetail = saints_uses_poet_uploads($detailPageId, $DatabaseCo->dbLink);
    ?>
 <style>
     /* Highlight animation */
     .card.highlight {
         background-color: #f0f8ff;
     }

     .tab-container {
         display: flex;
         gap: 10px;
         margin-bottom: 20px;
     }

     .tab-container button {
         padding: 10px 20px;
         cursor: pointer;
         border: none;
         background-color: #ddd;
         border-radius: 5px;
     }

     .tab-container button:hover {
         background-color: #ccc;
     }

     .align {
         margin-left: 5%;
     }

     .next-gods-container {
         display: flex;
         gap: 15px;
         justify-content: center;
     }
     .next-gods-container .card {
        max-width: 345px;
        margin-bottom: 5%;
    }
    .card-img-wrap {
    height: 280px; /* Set a fixed height for all images */
    width: 100%; /* Ensure full-width scaling */
    overflow: hidden; /* Hide any overflow */
    margin: 10px 10px 10px 10px ;
}

.card-img {
    width: 100%; /* Ensure the image covers the full width */
    height: 100%; /* Match the fixed height */
    object-fit: cover; /* Crop the image while preserving aspect ratio */
    display: block; /* Remove inline-block gap issues */
    
}

/* Optional: Adjust card image height for mobile and tablet */
@media (max-width: 767px) {
    .card-img-wrap img {
        height: 320px; /* Standard height for tablets and desktops */
    }
}

@media (min-width: 768px) {
    .card-img-wrap img {
        height: 320px; /* Standard height for tablets and desktops */
    }
}

 </style>
<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">
    <div class="overflow-hidden position-relative  banner-over-container">
        <img class="w-100 banner-h-420" src="<?php echo htmlspecialchars($bannerSrc); ?>" alt="<?php echo htmlspecialchars($detailTitle); ?>" onerror="this.onerror=null;this.src='<?php echo SAINTS_DEFAULT_IMAGE; ?>';" <?php if ($isPoetDetail) { ?>style="object-fit: contain; background: #ff87764f;"<?php } else { ?>style="object-position: top;"<?php } ?>>
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo htmlspecialchars($detailTitle); ?></h1>
    </div>
</div>


</div>
 <!-- Main content area to print, copy, or share -->
 <div id="printable-content">
     <div style="position: relative;">
         <div class="title">
             <h2 class="text-center mt-5"></h2>
         </div>
     </div>
     <div class="py-5">
         <div class="container">
             <div class="row">
                 <!-- Main Content Section -->
                 <div class="col-12">
                     <!-- Content Cards -->
                     <div id="Sthalam" class="card shadow mb-5 bg-body rounded p-4 mb-4 sth-text text-dark" style="min-height: 300px;">
                         <p class="text-dark sth-text"><?php echo $Row->content; ?></p>
                     </div>
                     <?php
// Fetch the next three records after the current ID
$saintsApprovedSql = saints_public_listing_status_sql();
$nextThreeQuery = "
    SELECT * FROM other_page 
    WHERE page_id = '$page_id' AND index_id NOT IN($id) $saintsApprovedSql
    ORDER BY RAND()
    LIMIT 3";
$nextThreeResult = mysqli_query($DatabaseCo->dbLink, $nextThreeQuery);

// Check if there are any records to display
if (mysqli_num_rows($nextThreeResult) > 0) {
    echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mt-4 ">'; // Responsive columns
    while ($nextRow = mysqli_fetch_assoc($nextThreeResult)) {
        $nextTitle = $nextRow['title'];
        $nextImgSrc = saints_banner_src($nextRow['banner'] ?? '', $nextRow['page_id'], $DatabaseCo->dbLink, $nextTitle);
        if ($nextImgSrc === SAINTS_DEFAULT_IMAGE && trim((string) ($nextRow['photos'] ?? '')) !== '') {
            $nextImgSrc = saints_photo_src($nextRow['photos'], $nextRow['page_id'], $DatabaseCo->dbLink, $nextTitle);
        }
        // Display each card
        echo '
        <div class="col mb-3">
            <div class="card rounded-3 w-100 flex-fill overflow-hidden border-0 dark-overlay card-hover  ">
                <!-- start card link -->
                <a href="saints-details.php?id=' . htmlspecialchars($nextRow['index_id']) . '&page_id=' . htmlspecialchars($nextRow['page_id']) . '" class="stretched-link z-2" ></a>

                <!-- start card image wrap -->
                <div class="card-img-wrap card-image-hover overflow-hidden">
                <img src="' . htmlspecialchars($nextImgSrc) . '" alt="' . htmlspecialchars($nextTitle) . '" class="card-img img-fluid" onerror="this.onerror=null;this.src=\'' . SAINTS_DEFAULT_IMAGE . '\';">
                </div>
                <!-- end card image wrap -->
                
                <div class="bottom-0 d-flex flex-column p-4 position-absolute position-relative text-white w-100 z-1">
                    <!-- start card title -->
                    <h4 class="fs-18 mb-0">
                        ' . htmlspecialchars($nextRow['title']) . '
                    </h4>
                    <!-- end card title -->
                </div>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo "<p>No records found</p>";
}
?>
                 </div>
             </div>
         </div>
     </div>
 </div>

 <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
 <script>
     // Print only the specific content without social icons and other sections
     function printContent() {
         const printContents = document.getElementById("printable-content").innerHTML;
         const originalContents = document.body.innerHTML;

         document.body.innerHTML = printContents;
         window.print();
         document.body.innerHTML = originalContents;
     }

     // Copy content to WhatsApp
     function shareToWhatsApp() {
         const content = document.getElementById("printable-content").innerText;
         const url = `https://wa.me/?text=${encodeURIComponent(content)}`;
         window.open(url, '_blank');
     }

     // Download content as PDF
     function downloadPDF() {
         const {
             jsPDF
         } = window.jspdf;
         const pdf = new jsPDF();
         const content = document.getElementById("printable-content").innerText;

         pdf.text(content, 10, 10);
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
 <?php include('./include/footer.php'); ?>