<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bhakti Kalpa - Reviews & Writings by Priests</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body {
  background: linear-gradient(to bottom, #f5f9ff, #edf3ff);
  font-family: 'Poppins', sans-serif;
  color: #1f2c47;
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

/* Card styling */
.guide-card {
  background: #ffffff;
  border-radius: 18px;
  padding: 20px;
  margin-bottom: 25px;
  box-shadow: 0 4px 18px rgba(35, 95, 200, 0.1);
  border-left: 5px solid #2c4da5;
  transition: all 0.3s ease;
}
.guide-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 22px rgba(35, 95, 200, 0.15);
}
.guide-card .author {
  display: flex;
  align-items: center;
  gap: 10px;
}
.guide-card .author img {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #2c4da5;
}
.guide-card .author span {
  font-weight: 600;
  color: #2c4da5;
}
.guide-card .content {
  margin-top: 10px;
  color: #333;
}
.btn-blue {
  background: #2c4da5;
  color: white;
  border: none;
}
.btn-blue:hover {
  background: #1b3b8a;
}

/* Comments Section */
.comment-box {
  margin-top: 15px;
  border-top: 1px solid #e2e8ff;
  padding-top: 10px;
}
.comment-box textarea {
  resize: none;
}
.comment-list {
  margin-top: 10px;
  font-size: 0.95rem;
}
.comment {
  background: #f5f8ff;
  padding: 8px 12px;
  border-radius: 10px;
  margin-bottom: 6px;
}
.comment strong {
  color: #2c4da5;
}
.filter-bar {
  background: #fff;
  border-radius: 16px;
  padding: 15px;
  box-shadow: 0 3px 12px rgba(35,95,200,0.08);
  margin-bottom: 25px;
}
</style>
</head>
<body>

<div class="container mt-4">
  <div class="header">
    <img src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa Logo">
    <h2><i class="fa-solid fa-feather"></i> Reviews & Writings by Priests</h2>
  </div>

  <!-- 🔍 Filter Bar -->
  <div class="filter-bar">
    <div class="row g-3 align-items-center">
      <div class="col-md-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search writings...">
      </div>
      <div class="col-md-4">
        <select id="filterPriest" class="form-select">
          <option value="">All Priests</option>
          <option>Swami Narayan Das</option>
          <option>Pandit Vishnu Sharma</option>
          <option>Acharya Rajesh</option>
        </select>
      </div>
      <div class="col-md-4">
        <select id="filterCategory" class="form-select">
          <option value="">All Categories</option>
          <option>Devotion</option>
          <option>Spiritual Growth</option>
          <option>Temple Rituals</option>
        </select>
      </div>
    </div>
  </div>

  <!-- ✍️ Writings List -->
  <div id="writingList">
    <div class="guide-card" data-priest="Swami Narayan Das" data-category="Devotion">
      <div class="author">
        <img src="assets/images/logo/bakthi-logo.png" alt="Swami Narayan Das">
        <span>Swami Narayan Das</span>
      </div>
      <div class="content mt-2">
        <h5 class="text-blue">Path of Bhakti in Modern Times</h5>
        <p>In today's world, devotion takes many forms. The true essence of Bhakti lies in surrender and love towards the divine...</p>
      </div>

      <div class="comment-box">
        <textarea class="form-control" rows="2" placeholder="Add your comment..."></textarea>
        <button class="btn btn-blue btn-sm mt-2 add-comment"><i class="fa-solid fa-comment"></i> Post Comment</button>
        <div class="comment-list mt-2"></div>
      </div>
    </div>

    <div class="guide-card" data-priest="Pandit Vishnu Sharma" data-category="Temple Rituals">
      <div class="author">
        <img src="assets/images/logo/bakthi-logo.png" alt="Pandit Vishnu Sharma">
        <span>Pandit Vishnu Sharma</span>
      </div>
      <div class="content mt-2">
        <h5 class="text-blue">Meaning of Daily Temple Rituals</h5>
        <p>Each ritual performed in temples has a spiritual significance — connecting body, mind, and soul with the supreme...</p>
      </div>

      <div class="comment-box">
        <textarea class="form-control" rows="2" placeholder="Add your comment..."></textarea>
        <button class="btn btn-blue btn-sm mt-2 add-comment"><i class="fa-solid fa-comment"></i> Post Comment</button>
        <div class="comment-list mt-2"></div>
      </div>
    </div>

    <div class="guide-card" data-priest="Acharya Rajesh" data-category="Spiritual Growth">
      <div class="author">
        <img src="assets/images/logo/bakthi-logo.png" alt="Acharya Rajesh">
        <span>Acharya Rajesh</span>
      </div>
      <div class="content mt-2">
        <h5 class="text-blue">Meditation and the Mind</h5>
        <p>Meditation purifies thought, strengthens discipline, and helps the devotee experience the divine within...</p>
      </div>

      <div class="comment-box">
        <textarea class="form-control" rows="2" placeholder="Add your comment..."></textarea>
        <button class="btn btn-blue btn-sm mt-2 add-comment"><i class="fa-solid fa-comment"></i> Post Comment</button>
        <div class="comment-list mt-2"></div>
      </div>
    </div>
  </div>

  <footer class="text-center mt-4 mb-3 text-muted">
    © 2025 Bhakti Kalpa | <a href="#" class="text-blue">www.bhaktikalpa.in</a>
  </footer>
</div>

<script>
// 💬 Add Comment Function
document.querySelectorAll('.add-comment').forEach(btn => {
  btn.addEventListener('click', () => {
    const card = btn.closest('.guide-card');
    const textarea = card.querySelector('textarea');
    const commentList = card.querySelector('.comment-list');
    const text = textarea.value.trim();
    if (text !== "") {
      const div = document.createElement('div');
      div.className = 'comment';
      div.innerHTML = `<strong>You:</strong> ${text}`;
      commentList.appendChild(div);
      textarea.value = "";
    }
  });
});

// 🔍 Filter Function
const searchInput = document.getElementById('searchInput');
const filterPriest = document.getElementById('filterPriest');
const filterCategory = document.getElementById('filterCategory');
const writingList = document.getElementById('writingList');

function filterWritings() {
  const search = searchInput.value.toLowerCase();
  const priest = filterPriest.value;
  const category = filterCategory.value;
  const cards = writingList.querySelectorAll('.guide-card');

  cards.forEach(card => {
    const text = card.innerText.toLowerCase();
    const matchSearch = text.includes(search);
    const matchPriest = priest === "" || card.dataset.priest === priest;
    const matchCategory = category === "" || card.dataset.category === category;

    card.style.display = (matchSearch && matchPriest && matchCategory) ? "" : "none";
  });
}

[searchInput, filterPriest, filterCategory].forEach(el => el.addEventListener('input', filterWritings));
</script>
</body>
</html>
