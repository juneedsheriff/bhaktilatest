<?php
error_reporting(0);
include('./include/header.php');

// Include required classes
include_once './app/class/XssClean.php';
include_once './app/class/databaseConn.php';
include_once './app/lib/requestHandler.php';
include_once './app/lib/mantrasVrathamImages.php';

$DatabaseCo = new DatabaseConn();
$xssClean = new xssClean();
$id = $xssClean->clean_input($_REQUEST['id']);

// Fetch temple details for the provided id
$select = "SELECT * FROM `mantras_stotras` WHERE index_id='$id'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row = mysqli_fetch_object($SQL_STATEMENT);
} else {
    echo "<p>No Records.</p>";
    exit;
}


$select = "SELECT * FROM `mantras_subcategory` WHERE index_id='$Row->sub_category'";
$SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

// Check if the query returns a result
if (mysqli_num_rows($SQL_STATEMENT) > 0) {
    $Row1 = mysqli_fetch_object($SQL_STATEMENT);
} else {
    echo "<p>No Records.</p>";
    exit;
}

$breadcrumbItems = [
    ['label' => 'Home', 'url' => 'index.php'],
    ['label' => 'Mantras & Stotras', 'url' => 'mantras-new.php'],
    ['label' => $Row1->title, 'url' => getMantraDetailsUrl($Row1->title)],
    ['label' => $Row->title],
];
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
</style>
<?php render_breadcrumbs($breadcrumbItems); ?>
<div class="container-fluid m-0 p-0 text-center bg-gradient text-center">
    <!-- <h1 class="h2 page-header-title fw-semibold m-2 pb-3  text-primary"><?php echo $Row->title; ?></h1> -->
    <div class=" overflow-hidden position-relative">
        <div class="row">
            <div class="col-md-12">
                <a class="d-block position-relative" href="#">
                    <img class="w-100" src="app/uploads/gods/banner/<?php echo $Row1->banner; ?>" class="img-fluid" alt="Temple Image">
                </a>
            </div>
        </div>
    </div>
</div>
<div class="container py-4" id="second-section">
    <div id="content-container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-12">
                <div class="card border-0 p-4 text-center">
                    <h3 class="fw-bold text-primary font-caveat page-header-title mb-3">
                        <?php echo htmlspecialchars($Row->title, ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <?php if (!empty($Row->audio)) { ?>
                        <div style="width: 300px; height: 50px; display: inline-block; text-align: center; padding: 10px;">
                            <audio controls style="width: 100%;">
                                <source src="app/uploads/mantras_audio/<?php echo htmlspecialchars($Row->audio, ENT_QUOTES, 'UTF-8'); ?>" type="audio/ogg">
                                <source src="app/uploads/mantras_audio/<?php echo htmlspecialchars($Row->audio, ENT_QUOTES, 'UTF-8'); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    <?php } ?>
                    <div class="sth-text text-dark"><?php echo $Row->content; ?></div>
                </div>
                <div class="justify-content-center d-flex">
                    <img src="./assets/images/imges1.png" alt="" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h3 class="text-center fw-bold  page-header-title " style="font-size: 24px;">You May Also Like</h3>
    <div class="row justify-content-center g-4">
        <?php
        // Fetch all data from temples table with limit and offset
        $select = "SELECT * FROM `mantras_subcategory` WHERE index_id !='0' ORDER BY index_id DESC LIMIT 3";
        $SQL_STATEMENT = mysqli_query($DatabaseCo->dbLink, $select);

        // Check if any rows are returned
        if (mysqli_num_rows($SQL_STATEMENT) > 0) {
            while ($RelatedRow = mysqli_fetch_assoc($SQL_STATEMENT)) {
                $photos = $RelatedRow['photos'];
                $title = $RelatedRow['title'];
                $index_id = $RelatedRow['index_id'];
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card shadow-sm h-100">
                <a href="mantras-details.php?id=<?php echo $index_id; ?>" >
                    <img src="app/uploads/gods/<?php echo $photos; ?>" class="card-img-top img-fluid" alt="<?php echo $title; ?>" style="height: 300px; object-fit: cover;">
                </a>
                <div class="card-body">
                    <a href="mantras-details.php?id=<?php echo $index_id; ?>"  class="text-decoration-none">
                        <h5 class="card-title text-dark fw-bold" style="font-size: 18px;"><?php echo $title; ?></h5>
                    </a>
                    <p class="card-text text-dark mb-0">
                        <a href="mantras-details.php?id=<?php echo $index_id; ?>"  class="btn  p-0">Read more</a>
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

<?php include('./include/footer.php'); ?>