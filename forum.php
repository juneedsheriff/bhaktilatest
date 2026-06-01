<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// forum.php
include_once './include/header.php';   // if you have a header include
// ensure DatabaseConn is available for server-side checks (e.g., logged in username display)
include_once './app/class/databaseConn.php';
$DatabaseCo = new DatabaseConn();

// ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

$username = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Devotee Forum — Bhakti Kalpa</title>

  <!-- Bootstrap CSS (CDN) -->


  <style>
    /* Devotional custom styles */
    body { background: linear-gradient(to bottom,#fffefc,#f6f8ff); color:#10233a; font-family: "Poppins", sans-serif; }
   
    .forum-category { background: #fff; border-radius: 12px; padding: 16px; box-shadow: 0 6px 18px rgba(44,77,165,0.06); }
    .topic-row { background: #fff; border-radius: 10px; padding: 12px; margin-bottom: 10px; border-left: 4px solid #f0f6ff; }
    .topic-title { color:#163a6b; font-weight:600; }
    .muted { color:#6b7280; }
    .devotional-header { background: linear-gradient(90deg,#ffffff,#f0f6ff); border-radius: 12px; padding: 14px; margin-bottom: 16px; border-left: 6px solid #2c4da5; }
    .btn-accent { background: #2c4da5; color: #fff; border: none; }
    .btn-accent:hover { background: #19378a; }
    .journal-index { float:left; width:36px; height:36px; border-radius:50%; background:#f0f6ff; color:#2c4da5; display:flex; align-items:center; justify-content:center; font-weight:600; margin-right:10px; }
    .post { background:#fff; padding:12px; border-radius:8px; margin-bottom:12px; box-shadow: 0 3px 12px rgba(17,24,39,0.03); }
    .avatar { width:44px; height:44px; border-radius:50%; background:#eef6ff; display:inline-block; text-align:center; line-height:44px; color:#2c4da5; font-weight:700; }
  </style>
  <?php $isLoggedIn = isset($_SESSION['user_id']) ? 1 : 0; ?>
<script>
    const IS_LOGGED_IN = <?= $isLoggedIn ?>;
</script>

</head>
<body>
<div class="container my-4">
  <div class="devotional-header d-flex justify-content-between align-items-center">
    <div>
      <div class="site-brand"><i class="fa-solid fa-hands-praying"></i> Bhakti Kalpa — Devotee Forum</div>
      <div class="muted">Share experiences, ask seva-related questions, discuss temple details.</div>
    </div>
    <div>
      <?php if ($username): ?>
        <div class="d-flex align-items-center gap-2">
          <div class="text-end me-2">
            <div class="small muted">Welcome</div>
            <div><strong><?= htmlspecialchars($username) ?></strong></div>
          </div>
          <button id="logoutBtn" class="btn btn-outline-danger btn-sm">Logout</button>
        </div>
      <?php else: ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
        Login / Register
      </button>
      <?php endif; ?>
    </div>
  </div>

  <div class="row">
  
        <!-- Categories and New Topic Section -->
<div class="col-lg-4 mb-3">
  <div class="forum-category">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Categories</h5>
      <button class="btn btn-sm btn-outline-secondary" id="refreshCats">Refresh</button>
    </div>
    <!-- This will be populated by JS -->
    <select id="topic_category" class="form-select">
      <option>Loading categories...</option>
    </select>
  </div>

  <div class="mt-3 forum-category">
    <h6>Start a new discussion</h6>
    <p class="muted small">Choose a category and create a topic.</p>
    <div class="d-grid">
      <button class="btn btn-accent" id="openNewTopicBtn">New Topic</button>
    </div>
  </div>
</div>

<!-- Topics / Topic View Section -->
<div class="col-lg-8">
  <div id="topicsArea">
    <div id="topicList" class="mb-3">
      <div class="text-muted">Select a category to view topics.</div>
    </div>
    <nav>
      <ul id="pagination" class="pagination"></ul>
    </nav>
  </div>

  <div id="topicViewArea" style="display:none;">
    <div class="mb-2">
      <button id="backToTopics" class="btn btn-sm btn-ghost">← Back to topics</button>
    </div>
    <div id="topicHeader" class="mb-3"></div>
    <div id="postList"></div>

    <?php if ($user_id): ?>
    <div class="mt-3">
      <h6>Reply to this topic</h6>
      <textarea id="replyContent" rows="4" class="form-control" placeholder="Write your reply..."></textarea>
      <div class="mt-2 d-flex gap-2">
        <button class="btn btn-accent" id="addReplyBtn">Post Reply</button>
        <button class="btn btn-secondary" id="previewReplyBtn">Preview</button>
      </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning mt-3">Please login to reply.</div>
    <?php endif; ?>
  </div>
</div>



  </div>
</div>

<!-- NEW TOPIC Modal -->
<div class="modal fade" id="newTopicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
      <form id="newTopicForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="modal-header">
          <h5 class="modal-title">Create New Topic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-2">
            <label>Category</label>
            <select name="category_id" id="newTopicCategory" class="form-select" required></select>
          </div>
          <div class="mb-2">
            <label>Title</label>
            <input name="title" id="newTopicTitle" type="text" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Content</label>
            <textarea name="content" id="newTopicContent" class="form-control" rows="6" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="addTopicForm" type="submit" class="btn btn-accent">Create Topic</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Login/Register Modal (reuse existing auth modal if available) -->
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="authModalLabel" class="modal-title">Login</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Login -->
        <form id="loginSection">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <div class="mb-2"><input name="username" class="form-control" placeholder="Username or email" required></div>
          <div class="mb-2"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
          <div class="d-grid"><button class="btn btn-accent" id="loginNowBtn" type="button">Login</button></div>
        </form>

        <!-- Register -->
        <form id="registerSection" style="display:none;">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <div class="mb-2"><input id="reg_username" name="username" class="form-control" placeholder="Username" required></div>
          <div class="mb-2"><input id="reg_email" name="email" type="email" class="form-control" placeholder="Email" required></div>
          <div class="mb-2"><input id="reg_password" name="password" type="password" class="form-control" placeholder="Password" required></div>
          <div class="d-grid"><button class="btn btn-accent" id="registerNowBtn" type="button">Register</button></div>
        </form>

        <div class="mt-2 text-center">
          <a href="#" id="goRegister">Register</a> · <a href="#" id="goLogin">Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once './include/footer.php'; ?>
<?php include('login-registration.php');?>
<!-- Bootstrap JS + dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/forum.js"></script>
<script src="assets/js/user-login-signup.js"></script>
<script>

</script>
</body>
</html>
