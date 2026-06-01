<?php
// mantra-book.php
// Bhakti Kalpa - Mantra Selection & Book Creator (Standalone Blue Theme)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Bhakti Kalpa — Mantra Book Creator</title>

  <!-- Bootstrap + Font Awesome (same as japa-mala) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


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
    body {
      background: linear-gradient(141.76deg, #F5D9D5 0.59%, #F5EAB4 39.43%, #1f8a4c 100%) !important;
      font-family: 'Poppins', sans-serif;
      color: #1f2c47;
      margin: 0;
      padding: 20px 0 60px;
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

    .tool-card {
      background: #ffffff;
      border-radius: var(--card-radius);
      padding: 22px;
      margin-top: 18px;
      box-shadow: 0 4px 15px rgba(35,95,200,0.08);
      border-left: 4px solid var(--accent);
      transition: transform .18s ease, box-shadow .18s ease;
    }
    .tool-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(35,95,200,0.10); }

    .section-title { color: var(--accent-dark); font-weight: 600; margin-bottom: 12px; display:flex; align-items:center; gap:8px; font-size:1.05rem; }
    .muted { color: var(--muted); font-size: 0.92rem; }

    input[type="text"], textarea, select {
      width:100%; padding:10px; border:1px solid #e6eef8; border-radius:10px; outline:none;
    }

    /* Layout */
    .layout { display: grid; grid-template-columns: 1fr 420px; gap:18px; align-items:start; }
    @media (max-width: 980px){ .layout { grid-template-columns: 1fr; } }

    /* Library list */
    .mantra-list { max-height: 520px; overflow:auto; margin-top:12px; display:flex; flex-direction:column; gap:10px; padding-right:6px; }
    .mantra-row { display:flex; gap:12px; align-items:flex-start; padding:14px; border-radius:12px; border:1px solid #f0f6ff; background:linear-gradient(180deg,#fff,#fcfdff); }
    .mantra-meta { flex:1; }
    .mantra-title { font-weight:700; color:#06243a; }
    .mantra-text { margin-top:8px; white-space:pre-wrap; color:#123; }

    /* Selection */
    .selection { display:flex; flex-direction:column; gap:10px; margin-top:12px; min-height:120px; }
    .sel-item { display:flex; gap:10px; align-items:center; padding:10px; border-radius:10px; border:1px dashed #e8f1ff; background:#fbfdff; }
    .sel-actions { margin-left:auto; display:flex; gap:8px; }

    /* Buttons */
    .btn-blue { background: var(--accent); color: #fff; border: none; }
    .btn-blue:hover { background: var(--accent-dark); color: #fff; }
    .btn-ghost { background: transparent; color: var(--accent); border:1px solid rgba(44,77,165,0.12); }

    /* Preview */
    .preview { margin-top:12px; border-radius:12px; padding:14px; border:1px solid #eef6ff; background:linear-gradient(180deg,#fff,#f7fbff); min-height:160px; overflow:auto; }
    .book-mantra { margin:14px 0; padding-bottom:10px; border-bottom:1px solid #f0f6ff; }

    footer { margin-top:26px; text-align:center; color: #555; padding-top:10px; border-top:1px solid #eaf0ff; }
    #downloadPdf{
        width: 220px;;
    }
     #saveToAccount{
        width: 140px;;
    }
  </style>
</head>
<body>
  <div class="container-main">
    <div class="header">
      <img class="logo-dark" src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa Logo" onerror="this.style.display='none'">
      <h2><i class="fa-solid fa-hands-praying" style="color:var(--accent)"></i> Mantra Selection & Book Creator</h2>
      <p class="muted" style="margin-top:6px">Select your favourite mantras and compile a personalised mantra book (PDF). Save-to-account option available for future integration.</p>
    </div>

    <div class="tool-card">
      <div class="layout">
        <!-- LEFT: Mantra Library -->
        <div>
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="section-title"><i class="fa-solid fa-book"></i> Public Mantra Library</h4>
              <div class="muted">Curated collection — pick multiple mantras to create your book</div>
            </div>
            <div>
              <button id="importSample" class="btn btn-ghost btn-sm">Load Sample</button>
            </div>
          </div>

          <div class="d-flex gap-2" style="margin-top:12px">
            <input id="search" type="text" placeholder="Search mantras by title or text..." />
            <button id="clearSearch" class="btn btn-ghost btn-sm">Clear</button>
          </div>

          <div class="mantra-list" id="mantraList" aria-live="polite">
            <!-- populated via JS -->
          </div>

          <div class="mt-3 d-flex gap-2">
            <button id="addSelectedBtn" class="btn btn-blue btn-sm">Add Selected</button>
            <button id="addAllBtn" class="btn btn-ghost btn-sm">Add All</button>
          </div>
        </div>

        <!-- RIGHT: Selection & Preview -->
        <div>
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="section-title"><i class="fa-solid fa-book-open"></i> Your Mantra Book</h4>
              <div class="muted">Arrange selected mantras and create a PDF book</div>
            </div>
            <div>
              <!-- Save placeholder -->
              <button id="saveToAccount" class="btn btn-ghost btn-sm" title="Save to account (placeholder)">Save to Account</button>
            </div>
          </div>

          <div class="d-flex gap-2" style="margin-top:12px">
            <input id="bookTitle" type="text" placeholder="My Mantra Book" value="My Mantra Book" />
            <button id="downloadPdf" class="btn btn-blue btn-sm"><i class="fa-solid fa-file-pdf"></i> Download PDF</button>
          </div>

          <div class="selection" id="selectionList" style="margin-top:12px">
            <div class="muted">No mantras selected yet. Use the library to add.</div>
          </div>

          <div class="d-flex gap-2" style="margin-top:12px">
            <button id="previewBtn" class="btn btn-ghost btn-sm">Preview</button>
            <button id="clearSel" class="btn btn-ghost btn-sm">Clear Selection</button>
          </div>

        </div>
      </div>
    </div>

    <!-- Mantra Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      
      <div class="modal-header">
        <h5 class="modal-title" id="previewTitle">Mantra Book Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="previewContentModal" 
             style="font-size:20px; line-height:1.6; font-family:'Tiro Devanagari Sanskrit', serif;">
             <div class="preview" id="bookPreview">
              <div class="muted">Preview not generated yet. Click <b>Preview</b>.</div>
            </div>
          <!-- Dynamic Preview -->
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" onclick="downloadPDF()">Download PDF</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

    <footer>
      © <?php echo date("Y"); ?> Bhakti Kalpa | <a href="#" style="color:var(--accent-dark); text-decoration:none;">www.bhaktikalpa.in</a>
    </footer>
  </div>

  <!-- html2pdf (client-side PDF generation) -->
  <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.9.3/dist/html2pdf.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  <script>
  /* ============================
     Mantra Book Creator — Blue Theme
     Client-side demo. Replace storage hooks with your API later.
     ============================ */

  // Sample mantras (you can replace with API data)
  const SAMPLE_MANTRAS = [
    { id: 'm1', title: 'Gayatri Mantra', language: 'Sanskrit', text: 'ॐ भूर्भुवः स्वः\nतत् सवितुर्वरेण्यं\nभर्गो देवस्य धीमहि\nधियो यो नः प्रचोदयात्' },
    { id: 'm2', title: 'Maha Mrityunjaya Mantra', language: 'Sanskrit', text: 'ॐ त्र्यम्बकं यजामहे सुगन्धिं पुष्टिवर्धनम्\nउर्वारुकमिव बन्धनान्मृत्योर्मुक्षीय माऽमृतात्' },
    { id: 'm3', title: 'Om Namah Shivaya', language: 'Sanskrit', text: 'ॐ नमः शिवाय' },
    { id: 'm4', title: 'Shri Ram Jai Ram', language: 'Hindi', text: 'श्रीराम जया राम जया जया राम' },
    { id: 'm5', title: 'Hanuman Chalisa (Opening)', language: 'Hindi', text: 'श्रीगुरु चरन सरोज रज, निज मनु मुकुरु सुधारि' },
    { id: 'm6', title: 'Sarab Shakti Mantra', language: 'Sanskrit', text: 'ॐ गं गणपतये नमः' }
  ];

  // DOM refs
  const mantraListEl = document.getElementById('mantraList');
  const selectionListEl = document.getElementById('selectionList');
  const searchEl = document.getElementById('search');
  const addSelectedBtn = document.getElementById('addSelectedBtn');
  const addAllBtn = document.getElementById('addAllBtn');
  const importSampleBtn = document.getElementById('importSample');
  const clearSearchBtn = document.getElementById('clearSearch');
  const previewBtn = document.getElementById('previewBtn');
  const downloadPdfBtn = document.getElementById('downloadPdf');
  const clearSelBtn = document.getElementById('clearSel');
  const bookPreviewEl = document.getElementById('bookPreview');
  const bookTitleEl = document.getElementById('bookTitle');
  const saveToAccountBtn = document.getElementById('saveToAccount');

  // state
  let mantras = [];
  let selected = [];

  // init
  function init(){
    mantras = SAMPLE_MANTRAS.slice();
    renderLibrary();
    bindEvents();
  }

  function bindEvents(){
    importSampleBtn.addEventListener('click', ()=> { mantras = SAMPLE_MANTRAS.slice(); renderLibrary(); });
    searchEl.addEventListener('input', ()=> renderLibrary(searchEl.value.trim()));
    clearSearchBtn.addEventListener('click', ()=> { searchEl.value=''; renderLibrary(); });
    addSelectedBtn.addEventListener('click', addCheckedToSelection);
    addAllBtn.addEventListener('click', ()=> { mantras.forEach(m=> addToSelection(m.id)); });
    previewBtn.addEventListener('click', renderPreview);
    downloadPdfBtn.addEventListener('click', downloadPDF);
    clearSelBtn.addEventListener('click', ()=> { if(confirm('Clear selected mantras?')){ selected=[]; renderSelection(); bookPreviewEl.innerHTML = '<div class=\"muted\">Preview not generated yet. Click <b>Preview</b>.</div>'; } });
    saveToAccountBtn.addEventListener('click', saveToAccountPlaceholder);
  }

  function renderLibrary(filter=''){
    mantraListEl.innerHTML = '';
    const q = filter.toLowerCase();
    const list = mantras.filter(m => !q || (m.title + ' ' + m.text + ' ' + (m.language||'')).toLowerCase().includes(q));
    if(!list.length){ mantraListEl.innerHTML = '<div class=\"muted\">No mantras found.</div>'; return; }
    list.forEach(m => {
      const row = document.createElement('div');
      row.className = 'mantra-row';
      row.innerHTML = `
        <div style="width:36px"><input type="checkbox" class="chk" data-id="${m.id}" aria-label="Select ${escapeHtml(m.title)}"></div>
        <div class="mantra-meta">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="mantra-title">${escapeHtml(m.title)}</div>
            <div class="muted" style="font-size:13px">${escapeHtml(m.language)}</div>
          </div>
          <div class="mantra-text" style="margin-top:8px">${escapeHtml(m.text)}</div>
        </div>
      `;
      mantraListEl.appendChild(row);
    });
  }

  function addCheckedToSelection(){
    const checks = Array.from(document.querySelectorAll('.chk')).filter(c => c.checked);
    if(!checks.length) return alert('Select at least one mantra from the library.');
    for(const c of checks){
      const id = c.dataset.id;
      const m = mantras.find(x=>x.id === id);
      if(m && !selected.some(s=>s.id === m.id)) selected.push({...m});
      c.checked = false;
    }
    renderSelection();
  }

  function addToSelection(id){
    const m = mantras.find(x=>x.id === id);
    if(!m) return;
    if(selected.some(s=>s.id === m.id)) return;
    selected.push({...m});
    renderSelection();
  }

  function renderSelection(){
    selectionListEl.innerHTML = '';
    if(selected.length === 0){
      selectionListEl.innerHTML = '<div class="muted">No mantras selected yet. Use the library to add.</div>';
      return;
    }
    selected.forEach((s, idx) => {
      const el = document.createElement('div');
      el.className = 'sel-item';
      el.dataset.index = idx;
      el.innerHTML = `
        <div style="flex:1">
          <div style="font-weight:600">${escapeHtml(s.title)}</div>
          <div class="muted" style="font-size:13px">${escapeHtml(s.language)}</div>
        </div>
        <div class="sel-actions">
          <button class="btn btn-ghost btn-sm up">↑</button>
          <button class="btn btn-ghost btn-sm down">↓</button>
          <button class="btn btn-ghost btn-sm remove">Remove</button>
        </div>
      `;
      // handlers
      el.querySelector('.up').addEventListener('click', ()=> {
        if(idx === 0) return;
        [selected[idx-1], selected[idx]] = [selected[idx], selected[idx-1]];
        renderSelection();
      });
      el.querySelector('.down').addEventListener('click', ()=> {
        if(idx === selected.length - 1) return;
        [selected[idx+1], selected[idx]] = [selected[idx], selected[idx+1]];
        renderSelection();
      });
      el.querySelector('.remove').addEventListener('click', ()=> {
        if(!confirm('Remove this mantra from the book?')) return;
        selected.splice(idx, 1); renderSelection();
      });
      selectionListEl.appendChild(el);
    });
  }

  function renderPreview(){
      
    if(selected.length === 0) return alert('No mantras selected');
    const title = bookTitleEl.value.trim() || 'My Mantra Book';
    let html = `<div class="muted">Generated: ${new Date().toLocaleString()}</div><hr/>`;
    selected.forEach((s, i) => {
      html += `<div class="book-mantra"><h3 style="margin:6px 0 8px">${escapeHtml(s.title)}</h3><div style="white-space:pre-wrap">${escapeHtml(s.text)}</div></div>`;
    });
    bookPreviewEl.innerHTML = html;
    bookPreviewEl.scrollIntoView({behavior:'smooth'});

    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();

    // Open modal
  
  }

function downloadPDF() {
  if (selected.length === 0) {
    return alert('Please select at least one mantra.');
  }

  renderPreview();

  const bookTitle = bookTitleEl.value.trim() || 'My Mantra Book';

  // 🌸 Load devotional fonts
  const fontLink = document.createElement('link');
  fontLink.href = 'https://fonts.googleapis.com/css2?family=Tangerine:wght@700&family=Tiro+Devanagari+Hindi:wght@400;600&display=swap';
  fontLink.rel = 'stylesheet';
  document.head.appendChild(fontLink);

  const fontLink2 = document.createElement('link');
  fontLink2.href = 'https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600;700&display=swap';
  fontLink2.rel = 'stylesheet';
  document.head.appendChild(fontLink2);

  const element = document.createElement('div');
  element.style.fontFamily = "'Tiro Devanagari Hindi', serif";
  element.style.color = '#1f1f1f';
  element.style.background = 'linear-gradient(to bottom right, #fffefc, #f6f8ff)';
  element.style.padding = '25px 30px 15px';
  element.style.borderRadius = '6px';
  element.style.width = '100%';
  element.style.boxSizing = 'border-box';

  // 🪔 Header with spiritual font
  element.innerHTML = `
    <div style="text-align:center; margin-bottom:4px;">
      <img src="assets/images/logo/bakthi-logo.png" alt="Bhakti Kalpa" style="height:70px; margin-bottom:2px;">
      <h2 style="margin:3px 0 1px; color:#2c4da5;     font-family: 'Caveat', cursive; font-size:34px;">
        ${escapeHtml(bookTitle)}
      </h2>
      <p style="font-size:12px; color:#777; margin:0;">Compiled with devotion — Bhakti Kalpa | www.bhaktikalpa.in</p>
      <hr style="margin:6px 0 4px; border:0; border-top:1px solid #d8e3ff;">
    </div>
    <div id="mantraContent" style="margin-top:0;"></div>
  `;

  const mantraContent = element.querySelector('#mantraContent');
  mantraContent.innerHTML = bookPreviewEl.innerHTML;

  mantraContent.querySelectorAll(':scope > *').forEach(el => (el.style.marginTop = '0'));

  // 🕉️ Style & index mantras
  const mantras = [...mantraContent.querySelectorAll(':scope > div')].filter(m => m.querySelector('h3'));

  mantras.forEach((m, i) => {
    m.style.marginBottom = '16px';
    m.style.padding = '10px 14px';
    m.style.background = i % 2 === 0 ? '#f8f9ff' : '#fff';
    m.style.borderRadius = '8px';
    m.style.border = '1px solid #e6e8f2';
    m.style.pageBreakInside = 'avoid';

    const title = m.querySelector('h3');
    if (title) {
      const cleanTitle = title.textContent.trim().replace(/^\d+\.\s*/, '');
      title.textContent = `${i + 1}. ${cleanTitle}`;
    }
  });

  // 🪶 Title & mantra text style
  element.querySelectorAll('h3').forEach(h => {
    h.style.fontSize = '18px';
    h.style.color = '#2c4da5';
    h.style.margin = '0 0 6px';
    h.style.fontWeight = '600';
    h.style.fontFamily = "'Tiro Devanagari Hindi', serif";
  });

  element.querySelectorAll('div[style*="white-space"]').forEach(p => {
    p.style.fontSize = '15px';
    p.style.lineHeight = '1.6';
    p.style.fontFamily = "'Tiro Devanagari Hindi', serif";
  });

  const opt = {
    margin: [15, 10, 15, 10],
    filename: `${bookTitle.replace(/\s+/g, '_')}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true, scrollY: 0, backgroundColor: null },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
  };

  html2pdf()
    .set(opt)
    .from(element)
    .toPdf()
    .get('pdf')
    .then(pdf => {
      const totalPages = pdf.internal.getNumberOfPages();
      const { width, height } = pdf.internal.pageSize;

      for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);

        pdf.setTextColor(80, 120, 180);
        pdf.setFontSize(60);
        pdf.setFont('helvetica', 'bold');
        pdf.saveGraphicsState();
        pdf.setGState(new pdf.GState({ opacity: 0.05 }));
        pdf.text('Bhakti Kalpa', width / 2, height / 2, { align: 'center', angle: 45 });
        pdf.restoreGraphicsState();

        pdf.setFontSize(10);
        pdf.setTextColor(100);
        pdf.text('Bhakti Kalpa — Your Spiritual Companion', width - 20, height - 10, { align: 'right' });
      }
    })
    .save()
    .catch(e => {
      console.error('PDF generation failed:', e);
      alert('PDF generation failed: ' + e.message);
    });
}


  // Save-to-account placeholder (for future backend integration)
  function saveToAccountPlaceholder(){
    if(selected.length === 0) return alert('No mantras selected to save');
    const meta = {
      id: Date.now(),
      title: bookTitleEl.value.trim() || 'My Mantra Book',
      items: selected.map(s => ({ id: s.id, title: s.title })),
      created_at: new Date().toISOString()
    };
    // demo: store in localStorage and notify user
    const saved = JSON.parse(localStorage.getItem('bk_saved_books_demo') || '[]');
    saved.unshift(meta);
    localStorage.setItem('bk_saved_books_demo', JSON.stringify(saved));
    alert('Book saved locally (demo). When ready, we will connect this to your server to save per-user in DB.');
    // TODO: replace this function with an API POST call to save metadata & PDF to DB when backend is ready.
    // Example (future):
    // fetch('/api/books', { method: 'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(meta) })
  }

  // small util
  function escapeHtml(s){ if(s === null || s === undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  // init
  init();
  </script>
</body>
</html>
