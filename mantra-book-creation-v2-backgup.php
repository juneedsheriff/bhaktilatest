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
  margin-top:18px;
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
</style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<section class="section-mantra-download">


<div class="container">

  <div class="header">
    <h2><i class="fa-solid fa-hands-praying"></i> Mantra Selection</h2>
    <p class="muted">Select God → Select Mantras → View Details</p>
  </div>

  <div class="row">

    <!-- LEFT COLUMN: GODS -->
    <!-- LEFT COLUMN WITH TABS -->
<div class="side-bar col-md-3">
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
                <input type="checkbox" class="godCheck" value="all" data-title="<?php echo htmlspecialchars($g['title']); ?>">
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


    <!-- MIDDLE COLUMN: MANTRAS -->
    <div class="main-sec col-md-9">
      <div class="section-box section-box-god">
        <h4 class="section-title"><i class="fa-solid fa-gopuram"></i> Gods</h4>
        <div id="mantraList" class="scroll-box muted row">
          <div class="col-md-12">Select god</div>
        </div>
      </div>
              

      <!-- RIGHT COLUMN: DETAILS -->
      <div class="section">
        <div class="section-box">
          <h4 class="section-title"><i class="fa-solid fa-scroll"></i> Mantra Details </h4>

          <div class="meaning-select-box" style="margin: 10px 0 15px; padding: 10px; background: #f7f7fb; border-radius: 6px; border: 1px solid #e3e3ef;">
    
            <label style="margin-right: 20px; font-weight: 600;">
                <input type="radio" name="meaningOption" class="meaningOption" value="with" checked>
                With Meaning
            </label>

            <label style="font-weight: 600;">
                <input type="radio" name="meaningOption" class="meaningOption" value="without">
                Without Meaning
            </label>

        </div>
                    
            <div class="dwnld" style="margin-bottom:20px;">
              <div class="d-flex gap-2" style="margin-top:12px">
              <input id="bookTitle" type="text" class="form-control" placeholder="My Mantra Book" value="My Mantra Book" />
              <button id="downloadPdf" class="btn btn-blue btn-sm" onclick="downloadPDF()"><i class="fa-solid fa-file-pdf"></i> Download PDF</button>
            </div>
          </div>
          <div id="selectedMantraDetails" class="scroll-box muted pdf-page">Select mantra</div>
        </div>
      </div>

     </div>

  </div>

</div>
</section>
<input type="hidden" id="filtertype" value="gods">
<!-- jQuery -->

<?php include_once './include/footer.php'; ?>

<script>
// ---------------------- STATIC DATA ---------------------- //


// ---------------------- LOAD MANTRAS ---------------------- //
// When selecting any God checkbox
$(document).on("change", ".godCheck", function () {
    $("#filtertype").val("gods");
    let selectedGods = [];

    $(".godCheck:checked").each(function () {
        selectedGods.push($(this).val());
    });

     $(".section-box-god").show();
    $("#selectedMantraDetails").html("");

    // If no gods selected → clear list
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
                <label class="item-box col-md-4">
                  <input type="checkbox" class="mantraCheck" value="${item.index_id}" data-god="${item.god_id}">
                  <span>${item.title_clean}</span>
                </label>
            
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
    $("#filtertype").val("mantra");
    // Abort previous request if still running
    if (mantraRequest != null) {
        mantraRequest.abort();
    }
    $("#selectedMantraDetails").html("");
    // Disable all checkboxes during loading
    $(".mantraCheck").prop("disabled", true);

    $(".section-box-god").hide();
     let meaningOption = $('input[name="meaningOption"]:checked').val();
    let showMeaning = (meaningOption === "with");
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

            if (!response.status) {
                $("#selectedMantraDetails").html("<div class='muted'>No details found.</div>");
                return;
            }

            let html = "";

            response.data.forEach(function (m) {
                
                html += `
                    <div class="mantra-detail-box p-3 mb-3 border rounded">
                        <h4 class="fw-bold">${m.title}</h4>

                        <p><b>Mantra:</b><br>${m.content}</p>

                        ${ showMeaning && m.meaning ? `
                            <p><b>Meaning:</b><br>${m.meaning}</p>
                        ` : "" }

                      

                        ${m.audio ? `
                            <audio controls class="mt-2">
                                <source src="uploads/${m.audio}">
                            </audio>
                        ` : ""}
                    </div>
                `;
            });

            $("#selectedMantraDetails").html(html);

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


// ---------------------- SHOW DETAILS ---------------------- //
$(document).on("change", ".mantraCheck", function () {
  $("#filtertype").val("gods");
    $("#selectedMantraDetails").html("");
    let selected = $(".mantraCheck:checked");

    // If nothing selected
    if (selected.length === 0) {
        $("#selectedMantraDetails").html("<div class='muted'>Select mantra</div>");
        return;
    }

    let mantraIDs = [];

    selected.each(function () {
        mantraIDs.push($(this).val());
    });

    let meaningOption = $('input[name="meaningOption"]:checked').val();
    let showMeaning = (meaningOption === "with");

    // 🔥 AJAX to fetch all selected mantras
    $.ajax({
        url: "module/mantras_api.php",
        type: "POST",
        data: {
            action: "get_mantras_details",
            mantra_ids: mantraIDs,
            meaningOption:meaningOption
        },
        dataType: "json",

        success: function (response) {

            if (!response.status) {
                $("#selectedMantraDetails").html("<div class='muted'>No details found.</div>");
                return;
            }

            let html = "";

            response.data.forEach(function (m) {
                
                html += `
                    <div class="mantra-detail-box p-3 mb-3 border rounded">
                        <h4 class="fw-bold">${m.title}</h4>

                        <p><b>Mantra:</b><br>${m.content}</p>

                        ${ showMeaning && m.meaning ? `
                            <p><b>Meaning:</b><br>${m.meaning}</p>
                        ` : "" }

                      

                        ${m.audio ? `
                            <audio controls class="mt-2">
                                <source src="uploads/${m.audio}">
                            </audio>
                        ` : ""}
                    </div>
                `;
            });

            $("#selectedMantraDetails").html(html);
        },

        error: function () {
            $("#selectedMantraDetails").html("<div class='muted'>Server error. Try again.</div>");
        }
    });
});


$(document).on("change", ".meaningOption", function () {
    let filtertype = $("#filtertype").val();
    $("#selectedMantraDetails").html("");
    if(filtertype=="gods"){
        let selected = $(".mantraCheck:checked");

        // If nothing selected
        if (selected.length === 0) {
            $("#selectedMantraDetails").html("<div class='muted'>Select mantra</div>");
            return;
        }

        let mantraIDs = [];

        selected.each(function () {
            mantraIDs.push($(this).val());
        });

        let meaningOption = $('input[name="meaningOption"]:checked').val();
        let showMeaning = (meaningOption === "with");

        // 🔥 AJAX to fetch all selected mantras
        $.ajax({
            url: "module/mantras_api.php",
            type: "POST",
            data: {
                action: "get_mantras_details",
                mantra_ids: mantraIDs,
                meaningOption:meaningOption
            },
            dataType: "json",

            success: function (response) {

                if (!response.status) {
                    $("#selectedMantraDetails").html("<div class='muted'>No details found.</div>");
                    return;
                }

                let html = "";

                response.data.forEach(function (m) {
                    
                    html += `
                        <div class="mantra-detail-box p-3 mb-3 border rounded">
                            <h4 class="fw-bold">${m.title}</h4>

                            <p><b>Mantra:</b><br>${m.content}</p>

                            ${ showMeaning && m.meaning ? `
                                <p><b>Meaning:</b><br>${m.meaning}</p>
                            ` : "" }

                          

                            ${m.audio ? `
                                <audio controls class="mt-2">
                                    <source src="uploads/${m.audio}">
                                </audio>
                            ` : ""}
                        </div>
                    `;
                });

                $("#selectedMantraDetails").html(html);
            },

            error: function () {
                $("#selectedMantraDetails").html("<div class='muted'>Server error. Try again.</div>");
            }
        });
    }else{
         // Abort previous request if still running
    if (mantraRequest != null) {
        mantraRequest.abort();
    }
    $("#selectedMantraDetails").html("");
    // Disable all checkboxes during loading
    $(".mantraCheck").prop("disabled", true);

    $(".section-box-god").hide();
     let meaningOption = $('input[name="meaningOption"]:checked').val();
    let showMeaning = (meaningOption === "with");
    // Make new request
    mantraRequest = $.ajax({
        url: "module/mantras_api.php",
        type: "POST",
        data: {
            action: "get_mantras_list_by_title",
            title: $('input[type="radio"].mantras-lst:checked').val(),
            meaningOption:meaningOption
        },
        dataType: "json",

        success: function (response) {

            if (!response.status) {
                $("#selectedMantraDetails").html("<div class='muted'>No details found.</div>");
                return;
            }

            let html = "";

            response.data.forEach(function (m) {
                
                html += `
                    <div class="mantra-detail-box p-3 mb-3 border rounded">
                        <h4 class="fw-bold">${m.title}</h4>

                        <p><b>Mantra:</b><br>${m.content}</p>

                        ${ showMeaning && m.meaning ? `
                            <p><b>Meaning:</b><br>${m.meaning}</p>
                        ` : "" }

                      

                        ${m.audio ? `
                            <audio controls class="mt-2">
                                <source src="uploads/${m.audio}">
                            </audio>
                        ` : ""}
                    </div>
                `;
            });

            $("#selectedMantraDetails").html(html);

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
    }
});
const bookTitleEl = document.getElementById('bookTitle');
function downloadPDF() {
   
    const bookTitle = bookTitleEl.value.trim() || 'My Mantra Book';
    const htmlContent = document.getElementById("selectedMantraDetails").innerHTML;

    const form = new FormData();
    form.append("html", htmlContent);
    form.append("title", bookTitle);

    fetch("module/download-pdf.php", {
        method: "POST",
        body: form
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = bookTitle.replace(/\s+/g, '_') + ".pdf";
        a.click();
    });
}

</script>
