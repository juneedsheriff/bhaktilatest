<?php
// Bhakti Kalpa - Spiritual Tools Section (Blue Theme with Date Fields)
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bhakti Kalpa - Spiritual Tools</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
  background: linear-gradient(to bottom, #f5f9ff, #edf3ff);
  font-family: 'Poppins', sans-serif;
  color: #1f2c47;
  margin: 0;
  padding: 0;
}

.header {
  text-align: center;
  padding: 25px 15px;
}

.header img {
  height: 70px;
  margin-bottom: 10px;
}

.header h2 {
  font-size: 1.8rem;
  color: #19378a;
  font-weight: 700;
}

.section-title {
  color: #19378a;
  font-weight: 600;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1.25rem;
}

.tool-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 25px;
  margin-top: 25px;
  box-shadow: 0 4px 15px rgba(35, 95, 200, 0.1);
  border-left: 4px solid #2c4da5;
  transition: transform 0.2s ease-in-out, box-shadow 0.2s;
}

.tool-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(35, 95, 200, 0.15);
}

.counter-display {
  font-size: 3rem;
  color: #2c4da5;
  font-weight: bold;
}

.btn-blue {
  background: #2c4da5;
  color: white;
  border: none;
  transition: 0.3s;
}

.btn-blue:hover {
  background: #1b3b8a;
  color: #fff;
}

textarea {
  resize: none;
}

footer {
  border-top: 1px solid #dce7ff;
  margin-top: 40px;
  padding: 15px;
  text-align: center;
  color: #555;
}

.text-blue {
  color: #19378a;
  text-decoration: none;
}

.text-blue:hover {
  text-decoration: underline;
}
</style>
</head>
<body>

<div class="container mt-4">
  <div class="header">
    <img class="logo-dark" src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa Logo">
    <h2><i class="fa-solid fa-hands-praying text-blue"></i> Spiritual Tools</h2>
  </div>

  <!-- 🧘‍♂️ Japa Mala Counter -->
  <div class="tool-card text-center">
    <h4 class="section-title"><i class="fa-solid fa-bead-string"></i> Digital Japa Mala</h4>
    <div class="counter-display" id="malaCount">0</div>
    <div class="mt-3">
      <button class="btn btn-blue mx-2" onclick="incrementMala()"><i class="fa-solid fa-plus"></i> Count</button>
      <button class="btn btn-secondary mx-2" onclick="decrementMala()"><i class="fa-solid fa-minus"></i></button>
      <button class="btn btn-danger mx-2" onclick="resetMala()"><i class="fa-solid fa-rotate-left"></i> Reset</button>
    </div>
    <p class="mt-2 small text-muted"><i class="fa-solid fa-circle-dot"></i> Keep your daily chanting count</p>
  </div>

  <!-- 🌺 Bhakti Goals Tracker -->
  <div class="tool-card">
    <h4 class="section-title"><i class="fa-solid fa-bullseye"></i> Bhakti Goals Tracker</h4>
    <input type="date" id="goalDate" class="form-control mb-2">
    <input type="text" id="goalInput" class="form-control" placeholder="Enter your daily goal (e.g., Chant 108 times)">
    <div class="mt-3">
      <label class="form-label"><i class="fa-solid fa-chart-line"></i> Progress:</label>
      <input type="range" class="form-range" id="goalProgress" min="0" max="100" value="0">
      <p><span id="progressValue">0</span>% completed</p>
    </div>
    <button class="btn btn-blue" onclick="saveGoal()"><i class="fa-solid fa-floppy-disk"></i> Save Goal</button>
    <p class="mt-2 small text-muted" id="goalSaveStatus"></p>
  </div>

  <!-- 📖 Daily Journal -->
  <div class="tool-card">
    <h4 class="section-title"><i class="fa-solid fa-book-open"></i> Daily Bhakti Journal</h4>
    <input type="date" id="journalDate" class="form-control mb-2">
    <textarea id="journalText" class="form-control" rows="4" placeholder="Write your daily reflection..."></textarea>
    <button class="btn btn-blue mt-3" onclick="saveJournal()"><i class="fa-solid fa-pen-nib"></i> Save Entry</button>
    <p class="mt-2 small text-muted" id="saveStatus"><i class="fa-regular fa-clock"></i></p>
  </div>

  <footer>
    © 2025 Bhakti Kalpa | <a href="#" class="text-blue">www.bhaktikalpa.in</a>
  </footer>
</div>

<script>
// 🌿 Japa Mala Counter
let malaCount = localStorage.getItem("malaCount") || 0;
document.getElementById("malaCount").innerText = malaCount;

function incrementMala() {
  malaCount++;
  updateMala();
}
function decrementMala() {
  if (malaCount > 0) malaCount--;
  updateMala();
}
function resetMala() {
  malaCount = 0;
  updateMala();
}
function updateMala() {
  document.getElementById("malaCount").innerText = malaCount;
  localStorage.setItem("malaCount", malaCount);
}

// 🌸 Bhakti Goals Tracker
function saveGoal() {
  const goal = document.getElementById("goalInput").value.trim();
  const progress = document.getElementById("goalProgress").value;
  const date = document.getElementById("goalDate").value;

  if (!date) return alert("Please select a date for your goal.");
  if (goal === "") return alert("Please enter your daily goal.");

  localStorage.setItem(`goal_${date}`, JSON.stringify({ goal, progress }));
  document.getElementById("goalSaveStatus").innerHTML =
    `<i class="fa-solid fa-check-circle text-success"></i> Goal saved for ${date}`;
}

document.getElementById("goalProgress").addEventListener("input", function() {
  document.getElementById("progressValue").innerText = this.value;
});

// Load goal if date selected
document.getElementById("goalDate").addEventListener("change", function() {
  const date = this.value;
  const data = localStorage.getItem(`goal_${date}`);
  if (data) {
    const { goal, progress } = JSON.parse(data);
    document.getElementById("goalInput").value = goal;
    document.getElementById("goalProgress").value = progress;
    document.getElementById("progressValue").innerText = progress;
    document.getElementById("goalSaveStatus").innerHTML = `<i class="fa-solid fa-circle-check text-primary"></i> Loaded goal for ${date}`;
  } else {
    document.getElementById("goalInput").value = "";
    document.getElementById("goalProgress").value = 0;
    document.getElementById("progressValue").innerText = 0;
    document.getElementById("goalSaveStatus").innerText = "No goal saved for this date.";
  }
});

// 🕉️ Daily Journal
function saveJournal() {
  const date = document.getElementById("journalDate").value;
  const text = document.getElementById("journalText").value.trim();

  if (!date) return alert("Please select a date for your journal entry.");
  if (text === "") return alert("Please write something first.");

  localStorage.setItem(`journal_${date}`, text);
  document.getElementById("saveStatus").innerHTML =
    `<i class="fa-solid fa-check-circle text-success"></i> Journal saved for ${date}`;
  document.getElementById("journalText").value = "";
}

// Load journal if date selected
document.getElementById("journalDate").addEventListener("change", function() {
  const date = this.value;
  const text = localStorage.getItem(`journal_${date}`);
  if (text) {
    document.getElementById("journalText").value = text;
    document.getElementById("saveStatus").innerHTML = `<i class="fa-solid fa-circle-check text-primary"></i> Loaded journal for ${date}`;
  } else {
    document.getElementById("journalText").value = "";
    document.getElementById("saveStatus").innerText = "No entry saved for this date.";
  }
});
</script>
</body>
</html>
