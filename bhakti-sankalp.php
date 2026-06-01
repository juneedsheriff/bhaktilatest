<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
// Bhakti Kalpa - Spiritual Tools Section (Updated)

include_once './include/header.php';   // if you have a header include
?>


<style>
  
/* 🌸 SPIRITUAL THEME */
.sn_language_links{
  margin:0px;
}
body {
  background: linear-gradient(141.76deg, #F5D9D5 0.59%, #F5EAB4 39.43%, #1f8a4c 100%) !important;
  font-family: 'Poppins', sans-serif;
  color: #4a2800;
  margin: 0;
  padding: 0;
  background-size: 350px;
  background-attachment: fixed;
}

/* HEADER */
.header {
  text-align: center;
  padding: 25px 15px;
}

.header img {
  height: 80px;
  margin-bottom: 10px;
}

.header h2 {
  font-size: 1.9rem;
  color: #8a4f05;
  font-weight: 700;
  text-shadow: 1px 1px 2px #d8a34a;
}

/* TITLES */
.section-title {
  color: #7a3e00;
  font-weight: 700;
  margin-bottom: 15px;
  font-size: 1.35rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* TOOL CARDS ROW – proper spacing and alignment */
.tool-cards-row {
  margin-left: -12px;
  margin-right: -12px;
}
.tool-cards-row .col-md-3 {
  padding-left: 16px;
  padding-right: 16px;
  margin-bottom: 28px;
  display: flex;
}
.tool-cards-row .col-md-3 .tool-card {
  width: 100%;
}

/* TOOL CARD – DEVOTIONAL PLAQUE STYLE */
.tool-card {
  background: #fffaf0;
  border-radius: 18px;
  padding: 25px;
  margin-top: 25px;
  margin-left: 0;
  margin-right: 0;
  box-shadow: 0 4px 25px rgba(156, 111, 22, 0.25);
  border: 2px solid #d4a24f;
  position: relative;
  height: 100%;
  min-height: 420px;
  display: flex;
  flex-direction: column;
}
.tool-card .section-title,
.tool-card p,
.tool-card form,
.tool-card .saved-list,
.tool-card #mantraSavedList,
.tool-card #goalSavedList,
.tool-card #VowsSavedList,
.tool-card #journalSavedList {
  flex-shrink: 0;
}
.tool-card .saved-list,
.tool-card #mantraSavedList,
.tool-card #goalSavedList,
.tool-card #VowsSavedList,
.tool-card #journalSavedList {
  flex-grow: 1;
  min-height: 0;
}

/* Save + Print in one line at bottom of card */
.tool-card-actions {
  margin-top: auto;
  flex-shrink: 0;
  padding-top: 1rem;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}
.tool-card-actions .btn {
  margin: 0;
}

@media (max-width: 767px) {
  .tool-cards-row .col-md-3 {
    margin-bottom: 24px;
  }
  .tool-card {
    min-height: 0;
  }
}

/* Golden ornamental top border */
.tool-card:before {
  content: "ॐ नमः शिवाय";
  font-size: 0.85rem;
  color: #b17a16;
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: #fffaf0;
  padding: 2px 12px;
  border-radius: 12px;
  border: 1px solid #d4a24f;
}

/* COUNTER */
.counter-display {
  font-size: 3.2rem;
  color: #b16b00;
  font-weight: bold;
  text-shadow: 0 0 3px #ffdd8a;
}

/* BUTTONS */
.btn-blue {
  background: #b16b00;
  color: white;
  border: none;
  transition: 0.3s;
}
.btn-blue:hover {
  background: #8a5200;
}

.btn-danger {
  background: #b32600;
}
.btn-danger:hover {
  background: #881f00;
}

.btn-secondary {
  background: #8e8e8e;
}
.btn-secondary:hover {
  background: #6b6b6b;
}

/* JOURNAL & FOOTER */
textarea {
  resize: none;
}

footer {
  border-top: 1px solid #e6c88f;
  margin-top: 40px;
  padding: 15px;
  text-align: center;
  color: #8a4f05;
}

.text-blue {
  color: #8a4f05;
  text-decoration: none;
}
.text-blue:hover {
  text-decoration: underline;
}
.saved-list {
    margin-top: 20px;
    padding: 15px;
    background: #eef3ff;
    border-radius: 10px;
    border: 1px solid #c8d7ff;
}

.saved-item {
    padding: 10px 0;
}

.styled-journal {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    border: 1px solid #e6d5a3;
    background: #fff9ef;
    border-radius: 10px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(230, 155, 20, 0.15);
}

.journal-index {
     background: none;
    color: #b16b00;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    flex-shrink: 0;
}

.journal-content {
    flex-grow: 1;
}

.journal-note {
    margin: 0 0 5px;
    font-size: 15px;
    line-height: 1.5;
    color: #333;
}

.journal-date {
    color: #a67c00;
    font-size: 13px;
}

.journal-actions {
    margin-top: 8px;
}
 
</style>
</head>

<body>

<div class="container mt-4">
  <div class="header">
    <img class="logo-dark" src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa Logo">
    <h2><i class="fa-solid fa-hands-praying text-blue"></i> Bhakti Sankalp </h2>
     <p class="text-center"> Your personal devotional companion to track, record, and achieve spiritual milestones.</p>

    <?php 

?>

<div class="top-header">
    <?php if(isset($_SESSION['user_id'])): ?>
        
        <!-- User Logged In -->
        <div id="userLoggedBlock" class="text-right">
            <span class="text-success">
                Welcome, <b><?php echo htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username']); ?></b>
            </span>
            <button id="logoutBtn" class="btn btn-danger btn-sm ml-2">Logout</button>
        </div>

    <?php else: ?>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
        Login / Register
      </button>

    <?php endif; ?>
</div>
    <!-- Trigger Login Button -->

  </div>

  <!-- 🧘‍♂️ Combined Japa Mala + Tracker -->
  <div class="row tool-cards-row">
    <div class="col-md-3">
    <div class="tool-card"> 
      
<div class="" id="printArea">

  
<h4 class="section-title"><i class="fa-solid fa-book-open"></i> Mantra Note</h4>
<p><strong> Purpose:</strong> Log and track mantras offline or online.</p>
<p class="text-start"><strong> Functions:</strong> Select deity/mantra, set goal (count/days/target), choose output (downloadable book or digital), reminders, milestones, streaks, analytics.</p>
 <form id="saveMantraForm">

    <select id="deitySelect" class="form-control mb-2">
        <option value="">Select Deity</option>
        <option value="Shiva">Shiva</option>
        <option value="Vishnu">Vishnu</option>
        <option value="Hanuman">Hanuman</option>
    </select>

    <input type="text" id="mantraInput" placeholder="Enter Mantra" class="form-control mb-2">

    <select id="goalType" class="form-control mb-2">
        <option value="">Goal Type</option>
        <option value="count">Count</option>
        <option value="days">Days</option>
        <option value="target">Target</option>
    </select>

    <input type="number" id="goalValue" placeholder="Goal Value" class="form-control mb-2">

    <select id="outputType" class="form-control mb-2">
        <option value="offline">Offline</option>
        <option value="online">Online</option>
    </select>

</form>

<div id="mantraSavedList" class="saved-list"></div>

<div class="tool-card-actions">
  <button id="saveMantraBtn" class="btn btn-blue">Save Mantra</button>
</div>
  </div>
    </div>
    </div>
    <div class="col-md-3">
    <div class="tool-card"><div class="" id="printArea">
    <h4 class="section-title"><i class="fa-solid fa-bead-string"></i>  Japamala</h4>


    <p><strong> Purpose:</strong> Chant, play, and record mantras.</p>
    <p class="text-start"><strong> Functions:</strong> Select/play mantra, record once and repeat (times/minutes), play along, track repetitions, time, streaks, analytics.</p>

    <!-- Mala Counter -->
    <div class="text-center">
      <div class="counter-display" id="malaCount">0</div>
      <div class="mt-3 d-flex">
        <button class="btn btn-blue mx-2" onclick="incrementMala()"><i class="fa-solid fa-plus"></i> Count</button>
        <button class="btn btn-secondary mx-2" onclick="decrementMala()"><i class="fa-solid fa-minus"></i></button>
        <button class="btn btn-danger mx-2" onclick="resetMala()"><i class="fa-solid fa-rotate-left"></i> Reset</button>
      </div>
      <p class="mt-2 small text-muted"><i class="fa-solid fa-circle-dot"></i> Chanting count</p>
    </div>

    <hr>
    
    <form action="" id="saveGoalForm">
    <!-- Tracker -->
    <h5><i class="fa-solid fa-bullseye"></i> Goal Tracker</h5>
    <input type="date" id="goalDate" class="form-control mb-2">
    <input type="text" id="goalInput" class="form-control" placeholder="Enter your goal (e.g., Chant 108 times)">
    <p class="mt-2 small text-muted" id="goalSaveStatus"></p>
    </form>

    <div id="goalSavedList" class="saved-list"></div>

    <div class="tool-card-actions">
      <button type="submit" form="saveGoalForm" class="btn btn-blue" id="saveGoalBtn"><i class="fa-solid fa-floppy-disk"></i> Save</button>
      <button type="button" onclick="printGoalSavedList()" class="btn btn-outline-primary">Print Saved List</button>
    </div>
  </div> </div>
    </div>
    <div class="col-md-3">
    <div class="tool-card"> <form id="saveVowsForm" class="">
    <h4 class="section-title"><i class="fa-solid fa-book-open"></i> Vows</h4>
    <p><strong> Purpose:</strong> Record and track devotional promises.</p>
    <p class="text-start"><strong> Functions:</strong> Create vow (“If X happens, I will do Y”), enter date, save, track status (Fulfilled/Pending/Hurdle), reminders, dashboard.</p>
    <input type="date" id="vowsDate" class="form-control mb-2">
    <textarea id="vowsText" class="form-control" rows="4" placeholder="Vows"></textarea>
    <p class="mt-2 small text-muted" id="vowsSaveStatus"><i class="fa-regular fa-clock"></i></p>

    <div id="VowsSavedList" class="saved-list"></div>

    <div class="tool-card-actions">
      <button type="submit" form="saveVowsForm" class="btn btn-blue" id="saveVowsBtn"><i class="fa-solid fa-pen-nib"></i> Save </button>
      <button type="button" onclick="printVowsSavedList()" class="btn btn-outline-primary">Print Saved Vows</button>
    </div>
  </form></div>
    </div>
    <div class="col-md-3">
    <div class="tool-card">
       <!-- 📖 Journal -->
  <form id="saveJournalForm" class=" ">
    <h4 class="section-title"><i class="fa-solid fa-book-open"></i> ⁠Yatra Journal</h4>
    <p><strong> Purpose:</strong> Record and track pilgrimage experiences.</p>

    <p class="text-start"><strong>   Functions:</strong> Log temple/date/companions, write notes, save/manage entries, reminders, milestones, streaks, analytics.</p>
    <input type="date" id="journalDate" class="form-control mb-2">
    <textarea id="journalText" class="form-control" rows="4" placeholder="Journal of devotional activities dates time with whom visited and own note of Yatra and can set reminder"></textarea>
    <p class="mt-2 small text-muted" id="saveStatus"><i class="fa-regular fa-clock"></i></p>

    <div id="journalSavedList" class="saved-list"></div>

    <div class="tool-card-actions">
      <button type="submit" form="saveJournalForm" class="btn btn-blue" id="saveJournalBtn"><i class="fa-solid fa-pen-nib"></i> Save </button>
      <button type="button" onclick="printJournalSavedList()" class="btn btn-outline-primary">Print Journal</button>
    </div>
  </form>
 </div>
    </div>
  </div>
  

  <!-- 📖 Journal -->
  

 


<input type="hidden" id="csrf_token" value="<?php echo $csrf_token; ?>">

</div>

<div class="py-5 bg-light m-3 rounded-4">

    <div class="container py-4 d-none">

        <div class="row">

            <div class="col-6">

                <div class="text-center mt-4">

                    <a href="assets/form/ramakoti_first_page (1).docx" download="Ramakoti_Form.docx" class="btn btn-primary mb-5">

                        <i class="fas fa-file-word"></i> Download File

                    </a>

                     <button class="btn btn-success mb-5"  onclick="printPDF()">
                        <i class="fas fa-print"></i> Print
                    </button>

                </div>

                <!-- start profile card -->

                <div>

                    <img src="assets/form/ramakoti_first_page.jpg" class="img-fluid" alt="">

                </div>

                <!-- end /. profile card -->

            </div>

            <div class="col-6">

                <div class="text-center mt-4">

                    <a href="assets/form/ramakoti_second_page (1).docx" download="Ramakoti_Form.docx" class="btn btn-primary mb-5">

                        <i class="fas fa-file-word"></i> Download File

                    </a>

                     <button class="btn btn-success mb-5" onclick="printPDF2()">
                        <i class="fas fa-print"></i> Print
                    </button>

                </div>

                <!-- start profile card -->

                <div>

                    <img src="assets/form/ramakoti_second_page.jpg" class="img-fluid" alt="">

                </div>

                <!-- end /. profile card -->

            </div>

        </div>

    </div>

</div>



<?php include('./include/footer.php'); ?>
<script>
function printPDF() {
    const pdfUrl = "assets/form/Ramakoti_Form-1.pdf";
    const win = window.open(pdfUrl, "_blank");

    // Wait for the PDF to load, then trigger print
    win.onload = function () {
        win.print();
    };
}
</script>

<script>
function printPDF2() {
    const pdfUrl = "assets/form/Ramakoti_Form-2.pdf";
    const win = window.open(pdfUrl, "_blank");

    // Wait for the PDF to load, then trigger print
    win.onload = function () {
        win.print();
    };
}
</script>

<?php include('login-registration.php');?>
<script src="assets/js/user-login-signup.js"></script>
<script>
// 🔊 Read count aloud (same code, unchanged wording)
function speakNumber(num) {
  const msg = new SpeechSynthesisUtterance(num);
  msg.rate = 1;
  msg.pitch = 1;
  window.speechSynthesis.speak(msg);
}

// 🌿 Mala Counter
let malaCount = localStorage.getItem("malaCount") || 0;
document.getElementById("malaCount").innerText = malaCount;

function incrementMala() {
  malaCount++;
  updateMala();
  speakNumber(malaCount);
}
function decrementMala() {
  if (malaCount > 0) malaCount--;
  updateMala();
  speakNumber(malaCount);
}
function resetMala() {
  malaCount = 0;
  updateMala();
  speakNumber(0);
}
function updateMala() {
  document.getElementById("malaCount").innerText = malaCount;
  localStorage.setItem("malaCount", malaCount);
}

// 🌸 Goal Tracker (per-date)
function saveGoal() {
  const goal = document.getElementById("goalInput").value.trim();
  const date = document.getElementById("goalDate").value;
  const progressEl = document.getElementById("goalProgress");
  const progress = progressEl ? progressEl.value : "0";

  if (!date) return alert("Please select a date.");
  if (goal === "") return alert("Please enter your goal.");

  localStorage.setItem("goal_" + date, JSON.stringify({ goal: goal, progress: progress }));
  document.getElementById("goalSaveStatus").innerHTML =
    "<i class=\"fa-solid fa-check-circle text-success\"></i> Saved for " + date;
}

document.getElementById("goalProgress").addEventListener("input", function() {
  document.getElementById("progressValue").innerText = this.value;
});

// Load saved goal
document.getElementById("goalDate").addEventListener("change", function() {
  const date = this.value;
  const data = localStorage.getItem(`goal_${date}`);
  if (data) {
    const { goal, progress } = JSON.parse(data);
    document.getElementById("goalInput").value = goal;
    document.getElementById("goalProgress").value = progress;
    document.getElementById("progressValue").innerText = progress;
    document.getElementById("goalSaveStatus").innerHTML =
      `<i class="fa-solid fa-circle-check text-primary"></i> Loaded`;
  } else {
    document.getElementById("goalInput").value = "";
    document.getElementById("goalProgress").value = 0;
    document.getElementById("progressValue").innerText = 0;
    document.getElementById("goalSaveStatus").innerText = "No saved goal.";
  }
});

// 🖨 Print Section
function printSection() {
  const printContents = document.getElementById("printArea").innerHTML;
  const original = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = original;
}



document.getElementById("journalDate").addEventListener("change", function() {
  const date = this.value;
  const text = localStorage.getItem(`journal_${date}`);
  if (text) {
    document.getElementById("journalText").value = text;
    document.getElementById("saveStatus").innerHTML =
      `<i class="fa-solid fa-circle-check text-primary"></i> Loaded`;
  } else {
    document.getElementById("journalText").value = "";
    document.getElementById("saveStatus").innerText = "No entry for this date.";
  }
});


// ---------------------------------------------
// Save Japa Mala Count + Goal and Show List
// ---------------------------------------------

function saveMalaGoal() {
    const malaCount = document.getElementById("malaCount").innerText;
    const malaGoal = document.getElementById("goalInput").value.trim();

    if (!malaGoal) {
        alert("Please enter a goal before saving.");
        return;
    }

    // Build save object
    const savedItem = {
        count: malaCount,
        goal: malaGoal,
        time: new Date().toLocaleString()
    };

    // Get existing
    let savedList = JSON.parse(localStorage.getItem("malaSavedItems")) || [];

    // Add new
    savedList.push(savedItem);

    // Save back
    localStorage.setItem("malaSavedItems", JSON.stringify(savedList));

    // Refresh UI list
    renderMalaSavedList();

    alert("Saved successfully!");
}


// ---------------------------------------------
// Render Mala Saved List
// ---------------------------------------------
function renderMalaSavedList() {
    const container = document.getElementById("goalSavedList");
    if (!container) return;
    container.innerHTML = "";

    const savedList = JSON.parse(localStorage.getItem("malaSavedItems")) || [];

    if (savedList.length === 0) {
        container.innerHTML = "<p class=\"small text-muted\">No saved items yet.</p>";
        return;
    }

    savedList.forEach(function(item, index) {
        container.innerHTML += "<div class=\"saved-item\">" +
            "<p><strong>#" + (index + 1) + "</strong></p>" +
            "<p><strong>Count:</strong> " + (item.count || "0") + "</p>" +
            "<p><strong>Goal:</strong> " + (item.goal || "").replace(/</g, "&lt;") + "</p>" +
            "<p><strong>Time:</strong> " + (item.time || "") + "</p><hr></div>";
    });
}



// Call on page load
renderMalaSavedList();


// ---------------------------------------------
// Save Bhakti Journal and Show List
// ---------------------------------------------

function saveJournal() {
    const note = document.getElementById("journalText").value.trim();

    if (!note) {
        alert("Please enter something before saving.");
        return;
    }

    const savedEntry = {
        text: note,
        time: new Date().toLocaleString()
    };

    let entries = JSON.parse(localStorage.getItem("journalEntries")) || [];
    entries.push(savedEntry);

    localStorage.setItem("journalEntries", JSON.stringify(entries));

    renderJournalSavedList();

    alert("Saved successfully!");
}


// ---------------------------------------------
// Render Journal Saved List
// ---------------------------------------------
function renderJournalSavedList() {
    const container = document.getElementById("journalSavedList");

    const entries = JSON.parse(localStorage.getItem("journalEntries")) || [];

    if (entries.length === 0) {
        container.innerHTML += "<p>No notes saved yet.</p>";
        return;
    }

    entries.forEach((item, index) => {
        container.innerHTML += `
            <div class="saved-item">
                <p><strong>#${index + 1}</strong></p>
                <p>${item.text}</p>
                <p><strong>Time:</strong> ${item.time}</p>
                <hr>
            </div>
        `;
    });
}


// Call on load
renderJournalSavedList();

// ---------------------------------------------
// Save Vows and Show List
// ---------------------------------------------
function saveVows() {
    const date = document.getElementById("vowsDate").value;
    const text = document.getElementById("vowsText").value.trim();

    if (!text) {
        alert("Please enter your vow before saving.");
        return false;
    }

    const savedEntry = {
        date: date || new Date().toISOString().slice(0, 10),
        text: text,
        time: new Date().toLocaleString()
    };

    let entries = JSON.parse(localStorage.getItem("vowsEntries")) || [];
    entries.push(savedEntry);
    localStorage.setItem("vowsEntries", JSON.stringify(entries));

    renderVowsSavedList();
    document.getElementById("vowsText").value = "";
    var statusEl = document.getElementById("vowsSaveStatus");
    if (statusEl) {
        statusEl.innerHTML = "<i class=\"fa-solid fa-circle-check text-success\"></i> Saved successfully!";
        setTimeout(function() { statusEl.innerHTML = "<i class=\"fa-regular fa-clock\"></i>"; }, 2000);
    }
    alert("Saved successfully!");
    return false;
}

function renderVowsSavedList() {
    const container = document.getElementById("VowsSavedList");
    if (!container) return;
    container.innerHTML = "";

    const entries = JSON.parse(localStorage.getItem("vowsEntries")) || [];
    if (entries.length === 0) {
        container.innerHTML = "<p class=\"small text-muted\">No vows saved yet.</p>";
        return;
    }

    entries.forEach(function(item, index) {
        container.innerHTML += "<div class=\"saved-item\">" +
            "<p><strong>#" + (index + 1) + "</strong></p>" +
            (item.date ? "<p class=\"small\"><strong>Date:</strong> " + item.date + "</p>" : "") +
            "<p>" + (item.text || "").replace(/</g, "&lt;").replace(/>/g, "&gt;") + "</p>" +
            "<p class=\"small text-muted\"><strong>Time:</strong> " + (item.time || "") + "</p>" +
            "<hr></div>";
    });
}
renderVowsSavedList();

// Prevent form submit and call save handlers (same pattern as Japamala)
function initFormHandlers() {
    var vowsForm = document.getElementById("saveVowsForm");
    var vowsBtn = document.getElementById("saveVowsBtn");
    if (vowsForm) {
        vowsForm.addEventListener("submit", function(e) {
            e.preventDefault();
            saveVows();
        });
    }
    if (vowsBtn) {
        vowsBtn.addEventListener("click", function(e) {
            e.preventDefault();
            saveVows();
        });
    }
    var journalForm = document.getElementById("saveJournalForm");
    if (journalForm) {
        journalForm.addEventListener("submit", function(e) {
            e.preventDefault();
            saveJournal();
        });
    }
    var goalForm = document.getElementById("saveGoalForm");
    if (goalForm) {
        goalForm.addEventListener("submit", function(e) {
            e.preventDefault();
            saveMalaGoal();
        });
    }
}
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initFormHandlers);
} else {
    initFormHandlers();
}

// ---------------------------------------------
// PRINT: Japa Mala Saved List
// ---------------------------------------------
function printGoalSavedList() {
    const content = document.getElementById("goalSavedList").innerHTML;

    const printWindow = window.open("", "", "width=900,height=700");

    printWindow.document.write(`
        <html>
        <head>
            <title>My Bhakti Goals</title>

            <style>
                body {
                    font-family: 'Georgia', serif;
                    background: #f9f5ef;
                    padding: 30px;
                    text-align: center;
                }

                .header-logo {
                    text-align: center;
                    margin-bottom: 20px;
                }

                .header-logo img {
                    width: 120px;
                }

                .title {
                    font-size: 26px;
                    font-weight: bold;
                    margin-bottom: 25px;
                    color: #5a381f;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .journal-item {
                    text-align: left;
                    background: #fff;
                    border: 1px solid #d8c7af;
                    padding: 18px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    box-shadow: 0 0 6px rgba(0,0,0,0.1);
                }

                .date {
                    font-size: 14px;
                    color: #6b4c2a;
                    font-weight: bold;
                    margin-bottom: 8px;
                }

                .note {
                    white-space: pre-wrap;
                    line-height: 1.6;
                    font-size: 17px;
                }
                .deleteGoalBtn{
                  display:none;
                }

                @media print {
                    .journal-item { 
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>

        <body>

            <div class="header-logo">
                <img src="assets/images/logo/bakthi-logo.png" alt="Bhakti Logo">
            </div>

            <div class="title">My Bhakti Journal</div>

            ${content}

        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 400);
}


// ---------------------------------------------
// PRINT: Bhakti Journal Saved Notes
// ---------------------------------------------
function printJournalSavedList() {
    const content = document.getElementById("journalSavedList").innerHTML;

    const printWindow = window.open("", "", "width=900,height=700");

    printWindow.document.write(`
        <html>
        <head>
            <title>My Bhakti Journal</title>

            <style>
                body {
                    font-family: 'Georgia', serif;
                    background: #f9f5ef;
                    padding: 30px;
                    text-align: center;
                }

                .header-logo {
                    text-align: center;
                    margin-bottom: 20px;
                }

                .header-logo img {
                    width: 120px;
                }

                .title {
                    font-size: 26px;
                    font-weight: bold;
                    margin-bottom: 25px;
                    color: #5a381f;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                .journal-item {
                    text-align: left;
                    background: #fff;
                    border: 1px solid #d8c7af;
                    padding: 18px;
                    border-radius: 10px;
                    margin-bottom: 20px;
                    box-shadow: 0 0 6px rgba(0,0,0,0.1);
                }

                .date {
                    font-size: 14px;
                    color: #6b4c2a;
                    font-weight: bold;
                    margin-bottom: 8px;
                }

                .note {
                    white-space: pre-wrap;
                    line-height: 1.6;
                    font-size: 17px;
                }
                .deleteJournalBtn{
                  display:none;
                }

                @media print {
                    .journal-item { 
                        page-break-inside: avoid;
                    }
                }
            </style>
        </head>

        <body>

            <div class="header-logo">
                <img src="assets/images/logo/bakthi-logo.png" alt="Bhakti Logo">
            </div>

            <div class="title">My Bhakti Journal</div>

            ${content}

        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 400);
}

// ---------------------------------------------
// PRINT: Vows Saved List
// ---------------------------------------------
function printVowsSavedList() {
    const content = document.getElementById("VowsSavedList");
    const html = content ? content.innerHTML : "<p>No vows saved yet.</p>";

    const printWindow = window.open("", "", "width=900,height=700");
    printWindow.document.write(
        "<html><head><title>My Vows</title>" +
        "<style>body{font-family:'Georgia',serif;background:#f9f5ef;padding:30px;}" +
        ".title{font-size:26px;font-weight:bold;margin-bottom:25px;color:#5a381f;}" +
        ".saved-item{text-align:left;background:#fff;border:1px solid #d8c7af;padding:18px;border-radius:10px;margin-bottom:20px;}" +
        "</style></head><body>" +
        "<div class=\"title\">My Vows</div>" + html +
        "</body></html>"
    );
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 400);
}

</script>

</body>
</html>
