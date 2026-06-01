<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// forum.php
include_once './include/header.php';   // if you have a header include
// ensure DatabaseConn is available for server-side checks (e.g., logged in username display)
include_once './app/class/databaseConn.php';
$DatabaseCo = new DatabaseConn();
$conn = $DatabaseCo->dbLink;
// ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

$username = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Fetch God Names from mantras_category table
$godList = [];
$sql = "SELECT index_id, title FROM mantras_category ORDER BY title ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $godList[] = $row;
    }
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
#filterOffcanvas{
   width: 320px;
   z-index: 9999999;
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
.list-wrap{
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding:15px;
}

.mantra-item {
    padding: 10px;
}

.mantra-card {
    display: block;
    background: #fff;
    border: 1px solid #e8dccb;
    border-radius: 12px;
    padding: 18px 20px;
    text-align: center;
    text-decoration: none;
    transition: 0.25s ease;
    min-height:110px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.mantra-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    border-color: #caa563; /* Soft golden */
    background: #fffaf1;
}

.mantra-title {
    font-size: 20px;
    color: #4b341a;
    font-weight: 600;
    font-family: "Tiro Devanagari Hindi", serif;
}

</style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<section class="pt-2">


<div class="container">
<!-- 
  <div class="header">
    <h2><div class="fs-1 font-caveat page-header-title fw-semibold m-2 pb-3  text-primary">Mantras &amp; Stotras</div></h2>
  </div> -->

  <div class="row">
  <div class="mb-3">
  <button class="btn btn-blue"
        data-bs-toggle="offcanvas"
        data-bs-target="#filterOffcanvas">
   <i class="fa-solid fa-filter"></i> Filters
</button>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas">

  <div class="offcanvas-header">
      <h5 class="offcanvas-title">
          <i class="fa-solid fa-filter"></i> Filters
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>

  <div class="offcanvas-body">

      <!-- YOUR EXISTING SIDEBAR -->
      <div class="side-bar">
      <div class="section-box">

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" id="leftTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="gods-tab" data-bs-toggle="tab" data-bs-target="#gods" type="button">
      <i class="fa-solid fa-gopuram"></i> Gods
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button class="nav-link" id="mantra-tab" data-bs-toggle="tab" data-bs-target="#allmantras" type="button">
      <i class="fa-solid fa-book"></i> Mantras
    </button>
  </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">

  <!-- GODS TAB -->
  <div class="tab-pane fade show active" id="gods" role="tabpanel">
    <h4 class="section-title"><i class="fa-solid fa-gopuram"></i> Gods</h4>
    <div id="godList" class="scroll-box">
      <?php if(!empty($godList)){ ?>
        <label class="item-box">
            <input type="checkbox" class="godCheck" value="all" data-title="all">
            <span>All</span>
          </label>
        <?php foreach($godList as $g){ ?>
          <label class="item-box">
            <input type="checkbox" class="godCheck" value="<?php echo $g['index_id']; ?>" data-title="<?php echo htmlspecialchars($g['title']); ?>">
            <span><?php echo $g['title']; ?></span>
          </label>
        <?php } ?>
      <?php } else { ?>
        <div class="muted">No gods found.</div>
      <?php } ?>
    </div>
  </div>

  <!-- ALL MANTRAS TAB -->
  <div class="tab-pane fade" id="allmantras" role="tabpanel">
    <h4 class="section-title"><i class="fa-solid fa-book"></i> All Mantras</h4>
    <div id="allMantraList" class="scroll-box muted">
      <?php

                // Fetch subcategories based on the current category

                $select_subcategory = "SELECT * FROM mantras_title ORDER BY title ASC";

                $SQL_STATEMENT_subcategory = mysqli_query($DatabaseCo->dbLink, $select_subcategory);



                // Check if any subcategories are returned

                if (mysqli_num_rows($SQL_STATEMENT_subcategory) > 0) {

                    while ($Row_subcategory = mysqli_fetch_assoc($SQL_STATEMENT_subcategory)) {

                        $title2 = htmlspecialchars($Row_subcategory['title'], ENT_QUOTES, 'UTF-8'); // Secure output

                        $god_id2 = $Row_subcategory['index_id'];

                ?>

                        <!-- Displaying each subcategory with checkbox -->

                        <div class="accordion-body">

                            <div class="form-check">

                                <input class="form-check-input mantras-lst" type="radio" name="mantraTitl" value="<?php echo $god_id2; ?>" id="mantras<?php echo $god_id2; ?>">

                                <label class="form-check-label" for="mantras<?php echo $god_id2; ?>"><?php echo $title2; ?></label>

                            </div>

                        </div>

                <?php

                    }

                } else {

                    echo "<p class='text-center'>No Mantras or Stotras found in this category.</p>";

                }

                ?>
    </div>
  </div>

</div>

</div>
      </div>

  </div>

</div>

    <!-- LEFT COLUMN: GODS -->
    <div id="filterPanel" style="display:none;">
   <!-- EXISTING SIDEBAR CONTENT -->
   <div class="side-bar col-md-3">
 
</div>
</div>
    <!-- LEFT COLUMN WITH TABS -->

     

<?php 
$all_gods = "
            SELECT 
                banner,
                index_id,
                categories_id,
                title,
                photos,
                description,
                order_by,
                status
            FROM mantras_subcategory
            WHERE status = 'approved'
            ORDER BY order_by ASC, title ASC
        ";

        $SQL_STATEMENT_GODS = mysqli_query($DatabaseCo->dbLink, $all_gods);
?>

    <!-- MIDDLE COLUMN: MANTRAS -->
    <div class="main-sec col-md-12">
      <div class="section-box2">

        <div id="mantraList" class="row">
                <?php
                if (mysqli_num_rows($SQL_STATEMENT_GODS) > 0) {

                        while ($Row_gods = mysqli_fetch_assoc($SQL_STATEMENT_GODS)) {

                            $title2 = htmlspecialchars($Row_gods['title'], ENT_QUOTES, 'UTF-8'); // Secure output

                            $id = $Row_gods['index_id'];

                    ?>

                           <div class="listing col-md-4 mb-3">
                                <div class="list-wrap">
                                <a href="mantras-details.php?id=<?php echo $id;?>" >
                                    <img src="app/uploads/gods/<?php echo $Row_gods['photos'];?>" alt="Dattatreya" class="img-fluid" onerror="this.onerror=null; this.src='assets/images/default-image.png';">
                                </a>

                                <div class="listing-details">
                                    <a href="mantras-details.php?id=<?php echo $id;?>" >
                                        <div class="listing-title"><?php echo $title2;?></div>
                                    </a>

                                    <div class="listing-rating text-dark">
                                        <a href="mantras-details.php?id=<?php echo $id;?>" >
                                            Read more
                                        </a>
                                    </div>
                                </div>
                                </div>
                            </div>

                    <?php

                        }

                    } else {

                        echo "<p class='text-center'>No Mantras or Stotras found in this category.</p>";

                    }
                ?>
        </div>
      </div>
              

 

     </div>

  </div>

</div>
</section>
<!-- jQuery -->

<?php include_once './include/footer.php'; ?>

<script>
// ---------------------- STATIC DATA ---------------------- //



// ---------------------- LOAD MANTRAS ---------------------- //
// When selecting any God checkbox
$(document).on("change", ".godCheck", function () {

    let selectedGods = [];

    $(".godCheck:checked").each(function () {
        selectedGods.push($(this).val());
    });

    // If no gods selected → show message
    if (selectedGods.length === 0) {
        $("#mantraList").html('<div class="muted">Select a god to load subcategories.</div>');
        return;
    }

    // AJAX to fetch subcategories
    $.ajax({
        url: "module/mantras_api.php",
        type: "POST",
        data: {
            action: "get_sub_categories",
            god_ids: selectedGods
        },
        dataType: "json",

        success: function (response) {

            if (!response.status || response.count === 0) {
                $("#mantraList").html('<div class="muted">No Mantras available.</div>');
                return;
            }

            let html = "";

            response.data.forEach(function (item) {

                html += `
                    <div class="listing col-md-4 mb-3">
                        <div class="list-wrap">
                        <a href="mantras-details.php?id=${item.index_id}" >
                            <img src="${item.photos || 'assets/images/default-image.png'}" 
                                 alt="${item.title_clean}" 
                                 class="img-fluid"
                                 onerror="this.onerror=null; this.src='assets/images/default-image.png';">
                        </a>

                        <div class="listing-details">
                            <a href="mantras-details.php?id=${item.index_id}" >
                                <div class="listing-title">${item.title_clean}</div>
                            </a>

                            <div class="listing-rating text-dark">
                                <a href="mantras-details.php?id=${item.index_id}" >
                                    Read more
                                </a>
                            </div>
                        </div>
                        </div>
                    </div>
                `;
            });

            $("#mantraList").html(html);
        },

        error: function () {
            $("#mantraList").html('<div class="muted">Server error. Try again.</div>');
        }
    });
});


// ---------------------- LOAD MANTRAS ---------------------- //
// When selecting any God checkbox
let mantraRequest = null; // store the current AJAX request

$(document).on("change", ".mantras-lst", function () {

    // Abort previous request if still running
    if (mantraRequest != null) {
        mantraRequest.abort();
    }

    // Disable all checkboxes during loading
    $(".mantraCheck").prop("disabled", true);

    $("#mantraList").html('<div class="muted">Loading...</div>');

    // Make new request
    mantraRequest = $.ajax({
        url: "module/mantras_api.php",
        type: "POST",
        data: {
            action: "get_mantras_list_by_title",
            title: $(this).val()
        },
        dataType: "json",

        success: function (response) {

            let html = "";

            if (!response.status || response.count === 0) {
                html = '<div class="muted">No Mantras available.</div>';
                $("#mantraList").html(html);
                return;
            }

            response.data.forEach(function (item) {
                html += `
                    <div class="mantra-item col-md-4">
                        <a href="mantras_title_details.php?id=${item.index_id}" class="mantra-card">
                            <div class="mantra-title">${item.title_clean}</div>
                        </a>
                    </div>
                `;
            });

            $("#mantraList").html(html);

            // Re-enable checkboxes after load
            $(".mantraCheck").prop("disabled", false);
        },

        error: function (xhr) {
            if (xhr.statusText === "abort") {
                // request was cancelled, do nothing
                return;
            }

            $("#mantraList").html('<div class="muted">Server error. Try again.</div>');
        },

        complete: function () {
            // Ensure re-enable if request completes
            $(".mantraCheck").prop("disabled", false);
        }
    });
});




</script>
<script>
$(document).on("change", ".godCheck, .mantras-lst", function () {
   let canvas = bootstrap.Offcanvas.getInstance(
      document.getElementById("filterOffcanvas")
   );
   if(canvas){
      canvas.hide();
   }
});
</script>
