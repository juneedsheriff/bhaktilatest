<?php
error_reporting(0);
include('./include/header.php');

// Include required classes
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './app/lib/mantrasVrathamImages.php';
include_once './app/lib/mantrasTitleImport.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$godname = trim((string) ($xssClean->clean_input($_REQUEST['godname'] ?? '')));
$mantras_id = $xssClean->clean_input($_REQUEST['id'] ?? '');
$title_id = $xssClean->clean_input($_REQUEST['title_id'] ?? '');

if ($godname !== '') {
    $godnameEsc = mysqli_real_escape_string($DatabaseCo->dbLink, $godname);
    $select = "SELECT * FROM `mantras_subcategory` WHERE TRIM(title) = '$godnameEsc' LIMIT 1";
} elseif ($mantras_id !== '') {
    $select = "SELECT * FROM `mantras_subcategory` WHERE index_id='$mantras_id'";
} else {
    echo "<p>No Records.</p>";
    exit;
}

$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $GodRow = mysqli_fetch_object($SQL_STATEMENT);
    $mantras_id = $GodRow->index_id;
    $godBannerSrc = trim((string) ($GodRow->banner ?? '')) !== ''
        ? 'app/uploads/gods/banner/' . $GodRow->banner
        : getMantraSubcategoryPhotoSrc([
            'index_id' => $GodRow->index_id,
            'title' => $GodRow->title,
            'photos' => $GodRow->photos ?? '',
            'banner' => $GodRow->banner ?? '',
        ]);
} else {
    echo "<p>No Records.</p>";
    exit;
}

$godCategoryFilters = getMantraGodCategoryFilters();
$totalGodCount = getMantraActiveGodCountFromCsv();
$mantraTitleFilters = getMantrasTitleFilterList($DatabaseCo->dbLink);
$mantrasFilterActiveGodKey = normalizeMantraTitleKey($GodRow->title);
$mantrasFilterVisibleTabs = 'both';
$mantrasFilterDefaultTab = 'gods';
$mantrasFilterMode = 'detail';

$mantraItemsData = [];
$mantrasAllQuery = "SELECT index_id, title FROM mantras_stotras WHERE status = 'approved' ORDER BY index_id ASC";
$mantrasAllResult = mysqli_query($DatabaseCo->dbLink, $mantrasAllQuery);
if ($mantrasAllResult) {
    while ($mantraRow = mysqli_fetch_assoc($mantrasAllResult)) {
        $mantraItemsData[] = [
            'index_id' => (int) $mantraRow['index_id'],
            'title' => $mantraRow['title'],
            'title_clean' => htmlspecialchars($mantraRow['title'], ENT_QUOTES, 'UTF-8'),
        ];
    }
}

$mantraKeywordsMap = [];
foreach ($mantraTitleFilters as $mantraFilter) {
    $mantraKeywordsMap[(int) $mantraFilter['index_id']] = $mantraFilter['title'];
}

$breadcrumbItems = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Mantras & Stotras', 'url' => 'mantras-new.php'],
    ['label' => $GodRow->title],
];
?>
<?php include_once './include/mantras-filter-styles.php'; ?>
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
        width: 200px;
    }

    .social-media {
        display: flex;
        justify-content: center;
        margin-top: -92px;
        margin-bottom: 34px;
    }

    .a1 {
        display: flex;
        background: #e3edf7;
        height: 55px;
        width: 55px;
        margin: 0 15px;
        border-radius: 8px;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 6px 6px 10px -1px rgba(0, 0, 0, 0.15),
            -6px -6px 10px -1px rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0);
        transition: transform 0.5s;
    }

    .a1 i {
        font-size: 25px;
        color: #777;
        transition: transform 0.5s;
    }

    .a1:hover {
        box-shadow: inset 4px 4px 6px -1px rgba(0, 0, 0, 0.2),
            inset -4px -4px 6px -1px rgba(255, 255, 255, 0.7),
            -0.5px -0.5px 0px rgba(255, 255, 255, 1),
            0.5px 0.5px 0px rgba(0, 0, 0, 0.15), 0px 12px 10px -10px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.01);
        transform: translateY(2px);
    }

    @media (max-width: 768px) {
        .social-media {
            display: flex;
            justify-content: center;
            margin-top: -18px;
            margin-bottom: 34px;
        }
    }

    .line {
        border: 1px solid black;
    }

    /* Container styling */
    .audio-container {
        display: inline-block;
        padding: 10px 40px;
        background-color: #f3f4f6;
        border-radius: 30px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    /* Audio player default styles */
    audio {
        outline: none;
        width: 20%;
    }
    @media(max-width: 768px) {
        audio {
            outline: none;
            width: 100%;
        }

    }
    /* Additional styling for play/pause button, volume, and timeline */
    audio::-webkit-media-controls-play-button,
    audio::-webkit-media-controls-volume-slider,
    audio::-webkit-media-controls-timeline {
        background-color: #f3f4f6;
        border-radius: 5px;
    }
    /* Hide the download button for a cleaner look */
    audio::-webkit-media-controls-download-button {
        display: none;
    }
    .hover-content {
        display: flex;
        align-items: center;
        justify-content: flex-start;
       
        border-radius: 5px;
        padding:5px;
        background-color: #f9f9f9;
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .hover-content:hover {
        background-color: #e0f7fa;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .hover-content i {
        font-size: 1.5rem;
        /* Adjust icon size */
        color: #ff8776 !important;
        /* Icon color */
    }

    .hover-content span {
        font-size: 1rem;
        /* Adjust text size */
        font-weight: 500;
        /* Adjust text weight */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .row {
        margin-bottom: 15px;
        /* Add spacing between rows */
    }

    .hover-content {
        display: flex;
        align-items: center;
        /* Center the content vertically */
        justify-content: flex-start;
        /* Align content to the left */
        background-color: transparent;
        transition: background-color 0.3s ease, padding 0.3s ease;
        cursor: pointer;
        border-radius: 5px;
        /* Optional for rounded corners */
        width: 100%;
    }

    /* On hover, change background color and add padding */
    .hover-content:hover {
        background-color: #ff8776 !important;
        /* Change to desired background color */
        padding: 10px;
        /* Increase padding on hover */
    }

    /* Add margin to the icon for spacing */
    .fas.fa-bahai {
        margin-right: 8px;
        /* Adjust space between icon and text */
    }

    /* Responsive Design: Adjust padding on smaller screens */
    @media (max-width: 767px) {
        .hover-content {
            padding: 10px;
        }
    }

    @media (max-width: 575px) {
        .hover-content {
            padding: 8px;
        }
    }
</style>
<?php render_breadcrumbs($breadcrumbItems); ?>
<div id="godBannerSection" class="container-fluid m-0 p-0 text-center bg-gradient text-center">
    <div class="overflow-hidden position-relative  banner-over-container">
                    <img class="w-100 banner-h-420 img-fluid" src="<?php echo htmlspecialchars($godBannerSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($GodRow->title, ENT_QUOTES, 'UTF-8'); ?>" style="object-position: top; object-fit: cover;">
        <h1 class="banner-over-title fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo htmlspecialchars($GodRow->title, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</div>

<div class="container py-3">
  <div class="row g-3 mantras-page-row">
    <?php include_once './include/mantras-filter-sidebar.php'; ?>
    <div class="col-lg-9 col-md-8 mantras-content">

      <div id="godDetailView">
        <div class="container-fluid px-0" id="first-section">
    <?php
    // Fetch all data from temples table
    $select = "SELECT * FROM `mantras_stotras` WHERE sub_category='$mantras_id' AND status='approved' ORDER BY title ASC";
    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

    // Check if any rows are returned
    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
        $counter = 0; // Initialize counter to track items per row
        $isFirst = true; // Flag to identify the first iteration
        echo '<div class="row ">';

        while ($MantraRow = mysqli_fetch_assoc($SQL_STATEMENT)) {
            $title = htmlspecialchars($MantraRow['title']);
            $index_id = $MantraRow['index_id'];

            echo '<div class="col-md-3 col-sm-6 mb-1 mantra-filter-item" data-mantra-id="' . htmlspecialchars($index_id) . '" data-mantra-title="' . $title . '">';
            echo '<span class="hover-content p-0 d-flex align-items-center" style="cursor: pointer;" ';
            echo 'onclick="showContent(' . htmlspecialchars($index_id) . ', true)"';

            if ($isFirst) {
                echo ' id="defaultContent">';
                echo '<script>document.addEventListener("DOMContentLoaded", function() { showContent(' . htmlspecialchars($index_id) . '); });</script>';
                $isFirst = false;
            } else {
                echo '>';
            }

            echo '<i class="fas fa-gopuram me-2" style="font-size: 1.2rem;"></i>';
            echo '<span class="text-truncate">' . $title . '</span>';
            echo '</span>';
            echo '</div>';

            $counter++;

            if ($counter % 4 === 0) {
                echo '</div><div class="row">';
            }
        }

        echo '</div>';
    } else {
        echo "<p class='text-center'>No Song found.</p>";
    }
    ?>
        </div>

        <div class="justify-content-center d-flex">
            <img src="./assets/images/imges1.png" alt="" class="img-fluid">
        </div>

        <div class="container px-0" id="second-section">
            <div id="content-container" class="container">
                <div class="row">
                    <div id="mantras_content">
                    <?php
                    $select = "SELECT * FROM `mantras_stotras` WHERE sub_category='$mantras_id' AND status='approved' ORDER BY title ASC";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    mysqli_data_seek($SQL_STATEMENT, 0);
                    while ($MantraRow = mysqli_fetch_assoc($SQL_STATEMENT)) {
                        $audio = htmlspecialchars($MantraRow['audio']);
                        $title = htmlspecialchars($MantraRow['title']);
                        $content = $MantraRow['content'];
                        $index_id = htmlspecialchars($MantraRow['index_id']);

                        echo '<div id="content-' . $index_id . '" class="content-section text-center" style="display: none;">';

                        if (!empty($audio)) {
                            echo '<div style="width: 300px; height: 50px; display: inline-block; text-align: center; padding: 10px;">';
                            echo '<audio controls style="width: 100%;">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($audio, ENT_QUOTES, 'UTF-8') . '" type="audio/ogg">';
                            echo '<source src="app/uploads/mantras_audio/' . htmlspecialchars($audio, ENT_QUOTES, 'UTF-8') . '" type="audio/mpeg">';
                            echo 'Your browser does not support the audio element.';
                            echo '</audio>';
                            echo '</div>';
                        }

                        echo '<h3 id="content-title-' . $index_id . '" class="text-center font-caveat page-header-title fw-semibold m-2 pb-3 text-primary fw-semibold mb-0">' . $title . '</h3>';
                        echo '<p class="mt-3 col-4 text-dark text-center" align="center">' . $content . '</p>';
                        echo '</div>';
                    }
                    ?>
                    </div>
                </div>
            </div>

            <div class="container my-5 you_may" id="you_may">
                <h3 class="text-center fw-bold fs-5 card-title page-header-title">You May Also Like</h3>
                <div class="row justify-content-center g-4">
                    <?php
                    $select = "SELECT * FROM `mantras_subcategory` WHERE index_id !='0' ORDER BY index_id DESC LIMIT 2";
                    $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

                    if (mysqli_num_rows($SQL_STATEMENT) > 0) {
                        while ($RelatedRow = mysqli_fetch_assoc($SQL_STATEMENT)) {
                            $relatedPhotoSrc = getMantraSubcategoryPhotoSrc($RelatedRow);
                            $title = $RelatedRow['title'];
                    ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="card shadow-sm h-100">
                                    <a href="<?php echo getMantraDetailsUrl($title); ?>">
                                        <img src="<?php echo htmlspecialchars($relatedPhotoSrc, ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top img-fluid" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" style="height: 300px; object-fit: cover;">
                                    </a>
                                    <div class="card-body">
                                        <a href="<?php echo getMantraDetailsUrl($title); ?>" class="text-decoration-none">
                                            <h5 class="card-title text-dark fw-bold" style="font-size: 18px;"><?php echo $title; ?></h5>
                                        </a>
                                        <p class="card-text text-dark mb-0">
                                            <a href="<?php echo getMantraDetailsUrl($title); ?>" class="btn p-0">Read more</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p class='text-center text-muted'>No temples found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
      </div>

      <div id="mantraListView" class="d-none">
        <div id="mantraList" class="row"></div>
      </div>

    </div>
  </div>
</div>
<script>
    window.showContent = function showContent(indexId, scrollToContent) {
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        const selectedContent = document.getElementById('content-' + indexId);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }

        if (scrollToContent) {
            const titleEl = document.getElementById('content-title-' + indexId);
            const scrollTarget = titleEl || document.getElementById('second-section');
            if (scrollTarget) {
                scrollTarget.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    }

    function filterContent() {
        // Get all checkboxes
        const checkboxes = document.querySelectorAll('.mantras');
        let found = false;

        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show content for checked boxes
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const indexId = checkbox.value;
                const content = document.getElementById(`content-${indexId}`);
                if (content) {
                    content.style.display = 'block';
                    found = true;
                }
            }
        });

        // If no checkbox is checked, you can show a default message or do nothing
        if (!found) {
            console.log("No content to display.");
        }
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
    function downloadAllContentAsPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF();

        let yPosition = 10; // Initial vertical position in the PDF

        // Fetch all content sections
        document.querySelectorAll('.content-section').forEach(section => {
            const title = section.getAttribute('data-title') || 'Untitled';
            let content = section.getAttribute('data-content') || 'No content available';

            // Strip HTML tags from content using a temporary DOM element
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            content = tempDiv.textContent || tempDiv.innerText || 'No content available';

            // Add title to the PDF
            doc.setFont('Helvetica', 'bold');
            doc.text(title, 10, yPosition);
            yPosition += 10;

            // Add content to the PDF
            doc.setFont('Helvetica', 'normal');
            const contentLines = doc.splitTextToSize(content, 180); // Ensure text fits within page width
            doc.text(contentLines, 10, yPosition);
            yPosition += contentLines.length * 10;

            // Add new page if content exceeds page limit
            if (yPosition > 270) {
                doc.addPage();
                yPosition = 10; // Reset vertical position for the new page
            }
        });

        // Save the PDF
        doc.save('GodContent.pdf');
    }
</script>

<?php include('./include/footer.php'); ?>
<?php
$mantrasFilterMantraItemsData = $mantraItemsData;
$mantrasFilterMantraKeywordsMap = $mantraKeywordsMap;
include_once './include/mantras-filter-script.php'; ?>