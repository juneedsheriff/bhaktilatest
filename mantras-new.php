<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// forum.php
include_once './include/header.php';   // if you have a header include
// ensure DatabaseConn is available for server-side checks (e.g., logged in username display)
include_once './app/class/databaseConn.php';
include_once './app/lib/mantrasVrathamImages.php';
include_once './app/lib/mantrasTitleImport.php';
$DatabaseCo = new DatabaseConn();
$conn = $DatabaseCo->dbLink;
// ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

$username = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

$godCategoryFilters = getMantraGodCategoryFilters();
$totalGodCount = getMantraActiveGodCountFromCsv();
$godCategoryKeysMap = [];
foreach ($godCategoryFilters as $categoryIndex => $category) {
    $godCategoryKeysMap[$categoryIndex] = array_column($category['gods'], 'title_key');
}

$mantraTitleFilters = getMantrasTitleFilterList($DatabaseCo->dbLink);

$godsPageData = [];
$godsQuery = "
    SELECT banner, index_id, categories_id, title, photos, description, order_by, status
    FROM mantras_subcategory
    WHERE status = 'approved'
    ORDER BY index_id ASC
";
$godsResult = mysqli_query($DatabaseCo->dbLink, $godsQuery);
if ($godsResult) {
    while ($godRow = mysqli_fetch_assoc($godsResult)) {
        $godsPageData[] = [
            'index_id' => (int) $godRow['index_id'],
            'categories_id' => (int) $godRow['categories_id'],
            'title' => $godRow['title'],
            'title_key' => normalizeMantraTitleKey($godRow['title']),
            'title_clean' => htmlspecialchars($godRow['title'], ENT_QUOTES, 'UTF-8'),
            'details_url' => getMantraDetailsUrl($godRow['title']),
            'photo_src' => getMantraSubcategoryPhotoSrc($godRow),
        ];
    }
}

$mantraItemsData = [];
$mantrasQuery = "SELECT index_id, title FROM mantras_stotras WHERE status = 'approved' ORDER BY index_id ASC";
$mantrasResult = mysqli_query($DatabaseCo->dbLink, $mantrasQuery);
if ($mantrasResult) {
    while ($mantraRow = mysqli_fetch_assoc($mantrasResult)) {
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
?>
<style>
/* Blue theme matching japa-mala.php */
:root{
  --bg-start: #f5f9ff;
  --bg-end: #edf3ff;
  --accent: #2c4da5;
  --accent-dark: #19378a;
  --card-radius: 16px;
  --muted: #6b7280;
}

.section-mantra-download {
  background: linear-gradient(141.76deg, #F5D9D5 0.59%, #F5EAB4 39.43%, #1f8a4c 100%) !important;
  font-family: 'Poppins', sans-serif;
  color: #1f2c47;
  margin: 0;
  padding: 50px 0px;
}

.container-main {
  max-width: 1100px;
  margin: 0 auto;
  padding: 0 16px;
}
.header {
  text-align: center;
  padding: 22px 8px;
}

.header img { height: 70px; margin-bottom: 8px; }
.header h2 { font-size: 1.8rem; color: var(--accent-dark); font-weight: 700; margin: 0; }

/* Card box */
.section-box {
  background:#ffffff;
  border-radius:var(--card-radius);
  padding:22px;
  margin-top:10px;
  box-shadow:0 4px 15px rgba(35,95,200,0.08);
  border-left:4px solid var(--accent);
  transition:transform .18s ease, box-shadow .18s ease;
}

.section-box:hover {
  transform: translateY(-3px);
  box-shadow:0 8px 28px rgba(35,95,200,0.10);
}

.section-title { 
  color: var(--accent-dark); 
  font-weight: 600; 
  margin-bottom: 12px; 
  display:flex; align-items:center; gap:8px; 
  font-size:1.1rem; 
}

.muted { color: var(--muted); font-size: 0.92rem; }

/* Items */
.side-bar .item-box {
     padding: 5px 0;
    border-radius: 0;
    border: none;
    background: #fff;
    margin-bottom: 0px;
    display: flex;
    gap: 12px;
    align-items: center;
    font-weight: 500;
    font-size: 14px;
    border-bottom: 1px solid #f2f2f2;
}
.main-sec .item-box {
   padding: 5px 0;
    border-radius: 0;
    border: none;
    background: #fff;
    margin-bottom: 0px;
    display: flex;
    gap: 12px;
    align-items: center;
    font-weight: 600;
    font-size: 14px;
  font-weight:600;
}
/* Details */
.mantra-detail-box {
  padding:18px;
  background:linear-gradient(180deg,#fff,#f7fbff);
  border-radius:12px;
  border:1px solid #e1e8ff;
  margin-bottom:18px;
}

.mantra-detail-box h5 {
  font-weight:600;
  color:#06243a;
}

/* Scroll areas */
.scroll-box {
  max-height:700px;
  overflow-y:auto;
  padding-right:6px;
}

/* Buttons */
.btn-blue { background: var(--accent); color:#fff; border:none; }
.btn-blue:hover { background:var(--accent-dark); }

.btn-ghost { background:transparent; color:var(--accent); border:1px solid rgba(44,77,165,0.2); }

footer { text-align:center; color:#444; margin-top:25px; }
#downloadPdf{
  width:170px;
}
    .product-item1:hover {
        box-shadow: 0 1px 5px 1px rgba(0, 0, 0, 0.2);
    }

    .iconic-featured-card-image {
        width: 100%;
        height: 415px;
        object-fit: cover;
        display: block;
        flex-shrink: 0;
    }

    .iconic-featured-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        margin: 0;
    }

    .iconic-featured-row > .iconic-featured-col {
        padding: 5px;
        display: flex;
    }

    .product-item1 {
        border: 10px solid rgba(246, 222, 22, 0.7);
        height: 100%;
        background-color: #fff;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-item1 > a {
        display: flex;
        flex-direction: column;
        height: 100%;
        flex: 1;
        color: inherit;
        text-decoration: none;
    }

    .iconic-featured-card-title {
        text-align: center;
        padding: 10px;
        margin: 0;
        color: #000;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1.35;
    }

.mantra-item {
    padding: 10px;
}

.mantra-card {
    display: block;
    background: #fff;
    border: 7px solid rgba(246, 222, 22, 0.7);
    border-radius: 12px;
    padding: 18px 20px;
    text-align: center;
    text-decoration: none;
    transition: 0.25s ease;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.mantra-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    border-color: rgba(246, 222, 22, 1);
    background: #fffaf1;
}

.mantra-title {
    font-size: 17px;
    color: #4b341a;
    font-weight: 600;
    font-family: "georgia";
}

</style>
<?php include_once './include/mantras-filter-styles.php'; ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<section class="pt-2">

<div class="py-3 py-xl-5 bg-gradient">
<div class="container">
<!-- 
  <div class="header">
    <h2><div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Mantras &amp; Stotras</div></h2>
  </div> -->

  <div class="row g-3 mantras-page-row">
    <?php include_once './include/mantras-filter-sidebar.php'; ?>

    <div class="main-sec col-lg-9 col-md-8 mantras-content">
        <div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3 text-dark">Mantras &amp; Stotras</div>

        <div id="mantraList" class="row iconic-featured-row">
                <?php foreach ($godsPageData as $godCard) { ?>
                    <div class="col-lg-4 col-sm-6 iconic-featured-col">
                        <div class="product-item1">
                            <a href="<?php echo htmlspecialchars($godCard['details_url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <img class="iconic-featured-card-image" src="<?php echo htmlspecialchars($godCard['photo_src'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $godCard['title_clean']; ?>">
                                <div class="iconic-featured-card-title">
                                    <span class="shiny" style="margin: 0">
                                        <span style="margin: 0"><?php echo $godCard['title_clean']; ?></span>
                                    </span>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
        </div>
              

 

     </div>

  </div>

</div>
</div>
</section>
<!-- jQuery -->

<?php include_once './include/footer.php'; ?>
<?php
$mantrasFilterMode = 'listing';
$mantrasFilterGodCategoryKeysMap = $godCategoryKeysMap;
$mantrasFilterGodsPageData = $godsPageData;
$mantrasFilterMantraItemsData = $mantraItemsData;
$mantrasFilterMantraKeywordsMap = $mantraKeywordsMap;
include_once './include/mantras-filter-script.php';
?>
