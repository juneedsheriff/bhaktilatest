<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bhakti Kalpa — Devotee Forum</title>

  <!-- Bootstrap & Font Awesome (to match site look) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    :root{
      --bg-start:#f5f9ff; --bg-end:#edf3ff;
      --accent:#2c4da5; --accent-dark:#19378a; --muted:#6b7280;
      --card-radius:14px;
    }
    body{font-family:Inter, system-ui, Arial, sans-serif; background: linear-gradient(to bottom, var(--bg-start), var(--bg-end)); color:#102030; margin:0; padding:28px 0;}
    .container-main{max-width:1100px;margin:0 auto;padding:0 16px;}
    .header{text-align:center;margin-bottom:18px}
    .header img{height:64px}
    .header h1{color:var(--accent-dark);margin-top:6px;font-size:1.6rem}
    .top-bar{display:flex;gap:8px;align-items:center;justify-content:space-between;margin-bottom:12px}
    .card-forum{background:#fff;border-radius:var(--card-radius);padding:18px;box-shadow:0 6px 18px rgba(35,95,200,0.06);border-left:4px solid var(--accent)}

    /* left column (threads) */
    .thread-card{border:1px solid #eef6ff;border-radius:12px;padding:14px;margin-bottom:12px;background:linear-gradient(#fff,#fcfdff)}
    .thread-meta{font-size:13px;color:var(--muted)}
    .tag{background:#eef6ff;color:var(--accent);padding:4px 8px;border-radius:10px;font-size:12px;margin-right:6px}

    .controls{display:flex;gap:8px;flex-wrap:wrap}
    .btn-accent{background:var(--accent);color:#fff;border:none}
    .btn-accent:hover{background:var(--accent-dark)}

    .reply-box{margin-top:10px;padding:10px;background:#fbfdff;border-radius:10px;border:1px solid #eef6ff}

    /* mobile */
    @media (max-width:980px){ .layout{grid-template-columns:1fr} }

    /* visual helper */
    .muted{color:var(--muted)}
    .small{font-size:13px}
    .like-count{font-weight:600;color:var(--accent-dark)}
  </style>
</head>
<body>
  <div class="container-main">
    <div class="header">
      <img src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa Logo" onerror="this.style.display='none'">
      <h1><i class="fa-solid fa-comments" style="color:var(--accent)"></i> Devotee Forum</h1>
      <p class="muted">Ask questions, share experiences, and discuss devotional practices with fellow devotees.</p>
    </div>

    <div class="card-forum">
      <div class="top-bar">
        <div style="display:flex;gap:8px;align-items:center">
          <input id="searchInput" class="form-control form-control-sm" style="width:260px" placeholder="Search threads or tags" />
          <select id="tagFilter" class="form-select form-select-sm" style="width:auto">
            <option value="">All tags</option>
          </select>
          <select id="sortSelect" class="form-select form-select-sm" style="width:auto">
            <option value="new">Newest</option>
            <option value="top">Most liked</option>
          </select>
        </div>

        <div style="display:flex;gap:8px;align-items:center">
          <div id="userArea" class="small muted"></div>
          <button id="loginBtn" class="btn btn-outline-primary btn-sm">Log in</button>
          <button id="logoutBtn" class="btn btn-ghost btn-sm" style="display:none">Logout</button>
          <button id="openPostBtn" class="btn btn-accent btn-sm"><i class="fa-solid fa-plus"></i> New Thread</button>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 340px;gap:18px" class="layout">
        <!-- LEFT: thread list -->
        <div>
          <div id="threadsContainer">
            <!-- JS renders threads -->
          </div>

          <div class="d-flex justify-content-center mt-3">
            <button id="loadMoreBtn" class="btn btn-sm btn-outline-secondary">Load more</button>
          </div>
        </div>

        <!-- RIGHT: sidebar (create, recent, tags) -->
        <div>
          <div class="thread-card">
            <h5 style="margin-top:0">Create Quick Post</h5>
            <input id="quickTitle" class="form-control form-control-sm mb-2" placeholder="Short title (e.g., How to chant Gayatri?)">
            <textarea id="quickBody" class="form-control form-control-sm mb-2" rows="3" placeholder="Write your question or note..."></textarea>
            <input id="quickTags" class="form-control form-control-sm mb-2" placeholder="tags: e.g., mantra,ritual" />
            <div style="display:flex;gap:6px">
              <button id="quickPostBtn" class="btn btn-accent btn-sm">Post</button>
              <button id="clearQuickBtn" class="btn btn-outline-secondary btn-sm">Clear</button>
            </div>
          </div>

          <div class="thread-card mt-3">
            <h6 style="margin-top:0">Popular Tags</h6>
            <div id="tagsCloud" style="display:flex;flex-wrap:wrap;gap:6px"></div>
          </div>

          <div class="thread-card mt-3">
            <h6 style="margin-top:0">Recent Activity</h6>
            <ul id="recentList" class="list-unstyled small muted" style="padding-left:0;margin:0"></ul>
          </div>
        </div>
      </div>
    </div>

    <footer style="text-align:center;margin-top:20px;color:var(--muted);font-size:13px">
      © <span id="yearSpan"></span> Bhakti Kalpa
    </footer>
  </div>

  <!-- Login / New Thread / Reply modals -->
  <div class="modal" tabindex="-1" id="authModal">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Sign In (Demo)</h5></div>
        <div class="modal-body">
          <input id="loginName" class="form-control mb-2" placeholder="Display name (visible)" />
          <input id="loginEmail" class="form-control mb-2" placeholder="Email (demo only)" />
          <small class="muted">This demo stores a simple user object in localStorage. Replace with real auth API later.</small>
        </div>
        <div class="modal-footer">
          <button id="authCancel" class="btn btn-outline-secondary btn-sm">Cancel</button>
          <button id="authSave" class="btn btn-accent btn-sm">Sign in</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" id="newThreadModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Create New Thread</h5></div>
        <div class="modal-body">
          <input id="threadTitle" class="form-control mb-2" placeholder="Thread title" />
          <textarea id="threadBody" class="form-control mb-2" rows="6" placeholder="Write your question or share your experience..."></textarea>
          <input id="threadTags" class="form-control mb-2" placeholder="tags (comma separated)" />
          <small class="muted">You must be signed in to post. Posts are stored in localStorage for this demo.</small>
        </div>
        <div class="modal-footer">
          <button id="threadCancel" class="btn btn-outline-secondary btn-sm">Cancel</button>
          <button id="threadPost" class="btn btn-accent btn-sm">Post Thread</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Reply modal -->
  <div class="modal" tabindex="-1" id="replyModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Reply</h5></div>
        <div class="modal-body">
          <textarea id="replyBody" class="form-control mb-2" rows="4" placeholder="Write your reply..."></textarea>
          <small class="muted">You must be signed in to reply.</small>
        </div>
        <div class="modal-footer">
          <button id="replyCancel" class="btn btn-outline-secondary btn-sm">Cancel</button>
          <button id="replyPost" class="btn btn-accent btn-sm">Post Reply</button>
        </div>
      </div>
    </div>
  </div>

<script>
/* ===========================
  Devotee Forum (client-side demo)
  - Local persistence: localStorage keys: 'bk_forum_threads' and 'bk_forum_user'
  - Replace storage functions with API calls for production.
===========================*/

// Helpers & initial state
const THREADS_KEY = 'bk_forum_threads_v1';
const USER_KEY = 'bk_forum_user_v1';
const RECENT_MAX = 8;

let threads = loadThreads();
let currentUser = loadUser();
let replyTarget = null; // thread id for reply or { threadId, replyId } for editing
let pageSize = 8;
let pageIndex = 0;

// DOM refs
const threadsContainer = document.getElementById('threadsContainer');
const tagsCloud = document.getElementById('tagsCloud');
const recentList = document.getElementById('recentList');
const tagFilter = document.getElementById('tagFilter');
const searchInput = document.getElementById('searchInput');
const sortSelect = document.getElementById('sortSelect');
const userArea = document.getElementById('userArea');
const loginBtn = document.getElementById('loginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const openPostBtn = document.getElementById('openPostBtn');
const newThreadModal = new bootstrap.Modal(document.getElementById('newThreadModal'));
const authModal = new bootstrap.Modal(document.getElementById('authModal'));
const replyModal = new bootstrap.Modal(document.getElementById('replyModal'));

// Set year
document.getElementById('yearSpan').innerText = new Date().getFullYear();

// Wire controls
document.getElementById('authSave').addEventListener('click', signIn);
document.getElementById('authCancel').addEventListener('click', ()=>authModal.hide());
loginBtn.addEventListener('click', ()=>authModal.show());
document.getElementById('authSave').addEventListener('click', signIn);
logoutBtn.addEventListener('click', signOut);

document.getElementById('openPostBtn').addEventListener('click', ()=>{
  if(!currentUser){ authModal.show(); return; }
  document.getElementById('threadTitle').value=''; document.getElementById('threadBody').value=''; document.getElementById('threadTags').value='';
  newThreadModal.show();
});
document.getElementById('threadCancel').addEventListener('click', ()=>newThreadModal.hide());
document.getElementById('threadPost').addEventListener('click', postThread);

document.getElementById('quickPostBtn').addEventListener('click', quickPost);
document.getElementById('clearQuickBtn').addEventListener('click', ()=>{ document.getElementById('quickTitle').value=''; document.getElementById('quickBody').value=''; document.getElementById('quickTags').value=''; });

document.getElementById('importSample').addEventListener('click', ()=>{ importSamples(); render(); });

document.getElementById('previewBtn')?.addEventListener('click', ()=>{}); // noop if exists elsewhere

document.getElementById('searchInput').addEventListener('input', ()=> { pageIndex = 0; render(); });
document.getElementById('tagFilter').addEventListener('change', ()=> { pageIndex = 0; render(); });
document.getElementById('sortSelect').addEventListener('change', ()=> { pageIndex = 0; render(); });

document.getElementById('loadMoreBtn').addEventListener('click', ()=> { pageIndex++; render(); });

// Reply modal actions
document.getElementById('replyCancel').addEventListener('click', ()=>{ replyTarget = null; replyModal.hide(); });
document.getElementById('replyPost').addEventListener('click', postReply);

// quick helpers
function uid(prefix='id') { return prefix + '_' + Date.now().toString(36) + Math.floor(Math.random()*999).toString(36); }

function loadThreads(){
  try{ const raw = localStorage.getItem(THREADS_KEY); return raw ? JSON.parse(raw) : seedThreads(); } catch(e){ return seedThreads(); }
}
function saveThreads(){ localStorage.setItem(THREADS_KEY, JSON.stringify(threads)); }
function loadUser(){ try{ const raw=localStorage.getItem(USER_KEY); return raw?JSON.parse(raw):null; }catch(e){return null;} }
function saveUser(){ localStorage.setItem(USER_KEY, JSON.stringify(currentUser)); }
function signOut(){ currentUser=null; localStorage.removeItem(USER_KEY); updateUserArea(); }
function signIn(){
  const name = document.getElementById('loginName').value.trim();
  const email = document.getElementById('loginEmail').value.trim();
  if(!name || !email) { alert('Enter name and email (demo).'); return; }
  currentUser = { id: uid('u'), name, email, joined: new Date().toISOString() };
  saveUser();
  updateUserArea();
  authModal.hide();
}

// update user display
function updateUserArea(){
  if(currentUser){
    userArea.innerHTML = `<strong>${escapeHtml(currentUser.name)}</strong>`;
    loginBtn.style.display='none'; logoutBtn.style.display='inline-block';
  } else {
    userArea.innerHTML = '<span class="muted small">You are browsing as Guest</span>';
    loginBtn.style.display='inline-block'; logoutBtn.style.display='none';
  }
}

// render the whole forum
function render(){
  updateUserArea();
  buildTagCloud();
  renderThreads();
  renderRecent();
}

// filter + sort + paginate threads and render
function renderThreads(){
  const q = (searchInput.value || '').trim().toLowerCase();
  const tag = tagFilter.value;
  const sort = sortSelect.value;

  let list = threads.slice();

  if(q){
    list = list.filter(t => (t.title + ' ' + t.body + ' ' + (t.tags||'')).toLowerCase().includes(q));
  }
  if(tag){
    list = list.filter(t => (t.tags||'').split(',').map(x=>x.trim()).includes(tag));
  }
  if(sort === 'top'){
    list.sort((a,b)=> (b.likes||0) - (a.likes||0));
  } else {
    list.sort((a,b)=> new Date(b.created_at) - new Date(a.created_at));
  }

  // pagination
  const start = pageIndex * pageSize;
  const page = list.slice(0, start + pageSize);
  const hasMore = list.length > (start + pageSize);

  // render page items
  threadsContainer.innerHTML = '';
  if(page.length === 0){
    threadsContainer.innerHTML = '<div class="thread-card muted">No threads found. Be the first to ask!</div>';
    document.getElementById('loadMoreBtn').style.display = 'none';
    return;
  }

  page.forEach(t => threadsContainer.appendChild(threadNode(t)));
  document.getElementById('loadMoreBtn').style.display = hasMore ? 'inline-block' : 'none';
}

// create DOM for a thread
function threadNode(t){
  const div = document.createElement('div');
  div.className = 'thread-card';

  const tagsHtml = (t.tags||'').split(',').filter(Boolean).map(tag => `<span class="tag">${escapeHtml(tag.trim())}</span>`).join(' ');

  div.innerHTML = `
    <div style="display:flex;gap:8px;align-items:flex-start">
      <div style="flex:1">
        <div style="display:flex;justify-content:space-between;align-items:start;gap:8px">
          <div>
            <div style="font-weight:700;font-size:1rem">${escapeHtml(t.title)}</div>
            <div class="thread-meta small">${tagsHtml} • Posted by <strong>${escapeHtml(t.author.name)}</strong> • ${timeAgo(new Date(t.created_at))}</div>
          </div>
          <div style="text-align:right">
            <div><span class="like-count">${t.likes||0}</span> <button class="btn btn-sm btn-outline-primary like-btn" data-id="${t.id}" title="Like"><i class="fa-regular fa-thumbs-up"></i></button></div>
            <div style="margin-top:6px"><button class="btn btn-sm btn-outline-secondary reply-btn" data-id="${t.id}">Reply</button></div>
          </div>
        </div>
        <div style="margin-top:10px;white-space:pre-wrap">${escapeHtml(t.body)}</div>
      </div>
    </div>
    <div id="replies_${t.id}" style="margin-top:12px"></div>
    <div style="margin-top:8px;display:flex;gap:6px;align-items:center">
      ${t.author && currentUser && t.author.id === currentUser.id ? `<button class="btn btn-sm btn-outline-danger delete-thread" data-id="${t.id}">Delete</button>
      <button class="btn btn-sm btn-outline-secondary edit-thread" data-id="${t.id}">Edit</button>` : ''}
    </div>
  `;

  // render replies
  const repliesContainer = div.querySelector(`#replies_${t.id}`);
  (t.replies||[]).forEach(r => {
    const rDiv = document.createElement('div');
    rDiv.style.marginTop = '8px';
    rDiv.className = 'reply-box';
    rDiv.innerHTML = `<div style="display:flex;justify-content:space-between"><div><strong>${escapeHtml(r.author.name)}</strong> <span class="muted small">• ${timeAgo(new Date(r.created_at))}</span></div>
                      <div>
                        <span class="like-count">${r.likes||0}</span>
                        <button class="btn btn-sm btn-outline-primary like-reply" data-id="${r.id}" data-tid="${t.id}" title="Like"><i class="fa-regular fa-thumbs-up"></i></button>
                        ${currentUser && r.author.id === currentUser.id ? `<button class="btn btn-sm btn-outline-secondary edit-reply" data-id="${r.id}" data-tid="${t.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-danger del-reply" data-id="${r.id}" data-tid="${t.id}">Delete</button>` : ''}
                      </div></div>
                      <div style="margin-top:6px;white-space:pre-wrap">${escapeHtml(r.body)}</div>`;
    repliesContainer.appendChild(rDiv);
  });

  // attach operations
  div.querySelectorAll('.like-btn').forEach(b => b.addEventListener('click', ()=> toggleLikeThread(t.id)));
  div.querySelectorAll('.reply-btn').forEach(b => b.addEventListener('click', ()=> showReplyModal(t.id)));
  div.querySelectorAll('.delete-thread').forEach(b => b.addEventListener('click', ()=> deleteThread(b.dataset.id)));
  div.querySelectorAll('.edit-thread').forEach(b => b.addEventListener('click', ()=> editThread(b.dataset.id)));

  div.querySelectorAll('.like-reply').forEach(b => b.addEventListener('click', ()=> toggleLikeReply(b.dataset.tid, b.dataset.id)));
  div.querySelectorAll('.del-reply').forEach(b => b.addEventListener('click', ()=> deleteReply(b.dataset.tid, b.dataset.id)));
  div.querySelectorAll('.edit-reply').forEach(b => b.addEventListener('click', ()=> editReply(b.dataset.tid, b.dataset.id)));

  return div;
}

// toggles
function toggleLikeThread(tid){
  const t = threads.find(x=>x.id===tid); if(!t) return;
  t.likes = (t.likes||0) + 1;
  t.last_activity = new Date().toISOString();
  saveThreads(); render();
}
function toggleLikeReply(tid, rid){
  const t = threads.find(x=>x.id===tid); if(!t) return;
  const r = (t.replies||[]).find(x=>x.id===rid); if(!r) return;
  r.likes = (r.likes||0) + 1;
  t.last_activity = new Date().toISOString();
  saveThreads(); render();
}

// show reply modal
function showReplyModal(threadId, replyId=null){
  if(!currentUser){ authModal.show(); return; }
  replyTarget = { threadId, replyId };
  document.getElementById('replyBody').value = '';
  replyModal.show();
}

// post reply / edit reply
function postReply(){
  const body = document.getElementById('replyBody').value.trim();
  if(!body) return alert('Please write a reply.');
  const { threadId, replyId } = replyTarget || {};
  const t = threads.find(x=>x.id===threadId); if(!t) return alert('Thread not found.');
  if(replyId){
    // edit existing reply
    const r = t.replies.find(x=>x.id===replyId);
    if(!r) return alert('Reply not found.');
    if(r.author.id !== currentUser.id) return alert('You can only edit your replies.');
    r.body = body; r.edited = true; r.updated_at = new Date().toISOString();
  } else {
    // new reply
    const r = { id: uid('r'), body, author: currentUser, likes:0, created_at: new Date().toISOString() };
    t.replies = t.replies || []; t.replies.push(r); t.last_activity = new Date().toISOString();
  }
  saveThreads(); replyModal.hide(); replyTarget=null; render();
}

// thread CRUD
function postThread(){
  if(!currentUser){ authModal.show(); return; }
  const title = document.getElementById('threadTitle').value.trim();
  const body = document.getElementById('threadBody').value.trim();
  const tags = document.getElementById('threadTags').value.trim();
  if(!title || !body) return alert('Please provide title and body.');

  const thr = { id: uid('t'), title, body, tags, likes:0, replies:[], author: currentUser, created_at: new Date().toISOString(), last_activity: new Date().toISOString() };
  threads.unshift(thr);
  saveThreads();
  newThreadModal.hide();
  render();
}

// delete/edit thread
function deleteThread(id){
  if(!confirm('Delete this thread?')) return;
  const idx = threads.findIndex(x=>x.id===id); if(idx>=0) threads.splice(idx,1);
  saveThreads(); render();
}
function editThread(id){
  const t = threads.find(x=>x.id===id); if(!t) return;
  if(!currentUser || t.author.id !== currentUser.id) return alert('You can only edit your threads.');
  document.getElementById('threadTitle').value = t.title;
  document.getElementById('threadBody').value = t.body;
  document.getElementById('threadTags').value = t.tags || '';
  newThreadModal.show();
  // on post, we will currently create new; for production you'd handle update route. (Simpler demo)
  // To implement edit-in-place, add flag to indicate editing an existing id.
}

// reply delete/edit
function deleteReply(tid, rid){
  if(!confirm('Delete reply?')) return;
  const t = threads.find(x=>x.id===tid); if(!t) return;
  const idx = (t.replies||[]).findIndex(x=>x.id===rid); if(idx>=0) t.replies.splice(idx,1);
  saveThreads(); render();
}
function editReply(tid, rid){
  const t = threads.find(x=>x.id===tid); if(!t) return;
  const r = (t.replies||[]).find(x=>x.id===rid); if(!r) return;
  if(!currentUser || r.author.id !== currentUser.id) return alert('You can only edit your replies.');
  replyTarget = { threadId: tid, replyId: rid };
  document.getElementById('replyBody').value = r.body;
  replyModal.show();
}

// quick post
function quickPost(){
  if(!currentUser){ authModal.show(); return; }
  const title = document.getElementById('quickTitle').value.trim();
  const body = document.getElementById('quickBody').value.trim();
  const tags = document.getElementById('quickTags').value.trim();
  if(!title || !body) return alert('Fill title & body');
  const thr = { id: uid('t'), title, body, tags, likes:0, replies:[], author: currentUser, created_at: new Date().toISOString(), last_activity: new Date().toISOString() };
  threads.unshift(thr);
  saveThreads(); render();
  document.getElementById('quickTitle').value=''; document.getElementById('quickBody').value=''; document.getElementById('quickTags').value='';
}

// utilities: tags, recent
function buildTagCloud(){
  const counts = {};
  threads.forEach(t=>{
    (t.tags||'').split(',').map(x=>x.trim()).filter(Boolean).forEach(tag => counts[tag] = (counts[tag]||0)+1);
  });
  tagsCloud.innerHTML=''; tagFilter.innerHTML = '<option value="">All tags</option>';
  Object.keys(counts).sort().forEach(tag => {
    const btn = document.createElement('button'); btn.className='btn btn-sm btn-ghost'; btn.textContent = tag; btn.style.marginRight='6px';
    btn.addEventListener('click', ()=>{ tagFilter.value = tag; render(); });
    tagsCloud.appendChild(btn);
    const opt = document.createElement('option'); opt.value = tag; opt.textContent = `${tag} (${counts[tag]})`; tagFilter.appendChild(opt);
  });
}

function renderRecent(){
  const recent = threads.slice().sort((a,b)=> new Date(b.last_activity) - new Date(a.last_activity)).slice(0, RECENT_MAX);
  recentList.innerHTML = '';
  recent.forEach(r => {
    const li = document.createElement('li');
    li.innerHTML = `<div><strong>${escapeHtml(r.title)}</strong><div class="muted small">${timeAgo(new Date(r.last_activity))} • by ${escapeHtml(r.author.name)}</div></div>`;
    recentList.appendChild(li);
  });
}

// seed data for demo
function seedThreads(){
  const sample = [
    { id: uid('t'), title: 'How to chant Gayatri mantra properly?', body: 'I am new to chanting — any tips on pronunciation and daily routine?', tags: 'mantra,practice', likes:3, replies:[{ id: uid('r'), body:'Start with slow pronunciation and follow transliteration.', author:{id:'u_demo',name:'Priya'}, likes:1, created_at:new Date().toISOString() }], author:{id:'u_demo',name:'Arjun'}, created_at: new Date().toISOString(), last_activity:new Date().toISOString() },
    { id: uid('t'), title: 'Which mala is best for japa?', body: 'Wooden vs Rudraksha — what do you prefer?', tags: 'japa,mala', likes:5, replies:[], author:{id:'u_demo',name:'Smita'}, created_at: new Date(Date.now()-86400000).toISOString(), last_activity:new Date(Date.now()-86400000).toISOString() },
    { id: uid('t'), title: 'Devotional music recommendations', body: 'Share your favourite bhajans and kirtan channels.', tags: 'music,bhajan', likes:2, replies:[], author:{id:'u_demo',name:'Ravi'}, created_at: new Date(Date.now()-3600*1000*5).toISOString(), last_activity:new Date(Date.now()-3600*1000*5).toISOString() }
  ];
  localStorage.setItem(THREADS_KEY, JSON.stringify(sample));
  return sample;
}

// time ago helper
function timeAgo(date){
  const diff = Math.floor((Date.now() - date.getTime())/1000);
  if(diff < 60) return diff + 's';
  if(diff < 3600) return Math.floor(diff/60) + 'm';
  if(diff < 86400) return Math.floor(diff/3600) + 'h';
  return Math.floor(diff/86400) + 'd';
}

// escape
function escapeHtml(s){ if(s==null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// init UI
updateUserArea();
render();

// Expose import function to console if needed
window._forum = { threads, loadThreads, saveThreads };

// End of script
</script>

<!-- bootstrap bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
