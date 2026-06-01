<?php
// DB CONNECTION (adjust if needed)
include_once './app/class/XssClean.php';



include_once './app/class/databaseConn.php';



include_once './app/lib/requestHandler.php';







$DatabaseCo = new DatabaseConn();



$xssClean = new xssClean();

include('./include/header.php');
?>


<style>
  .dataTables_filter{
    display: none; /* Search via temple dropdown only */
  }
  .dataTables_paginate .pagination{
    float: right;
  }

  /* ===== Search / Temple dropdown styling ===== */
  .live-darshan-search-wrap {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 1.25rem 1.5rem;
    border: 1px solid rgba(220, 53, 69, 0.15);
  }
  .live-darshan-search-wrap .form-label {
    color: #333;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
  }
  .live-darshan-search-wrap .select2-container {
    margin-top: 0;
  }
  .live-darshan-search-wrap .select2-container--default .select2-selection--single {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    height: 52px;
    padding: 0 1rem;
    background: #fafafa;
  }
  .live-darshan-search-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 50px;
    padding-left: 0;
    color: #333;
  }
  .live-darshan-search-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 50px;
    right: 12px;
  }
  .live-darshan-search-wrap .select2-container--default.select2-container--focus .select2-selection--single,
  .live-darshan-search-wrap .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.15);
    background: #fff;
  }
  .live-darshan-search-wrap .select2-dropdown {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  }
  .live-darshan-search-wrap .select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
  }
  .live-darshan-search-wrap .select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #dc3545;
    outline: none;
  }
  .live-darshan-search-wrap .select2-results__option--highlighted[aria-selected] {
    background-color: #dc3545;
    color: #fff;
  }

  /* ===== DEVOTIONAL CARD UI ===== */
div.dataTables_wrapper div.dataTables_info {
    padding-top: 20px;
    padding-bottom: 20px;
}
div.dataTables_wrapper div.dataTables_paginate {
    margin: 0;
    white-space: nowrap;
    text-align: right;
    padding-top: 20px;
}
.darshan-card{
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
    transition:all .3s ease;
}
.darshan-card:hover{
    transform:translateY(-5px);
}

.darshan-thumb{
    width:100%;
    height:200px;
    object-fit:cover;
}

.status-badge{
    position:absolute;
    top:12px;
    left:12px;
    padding:5px 12px;
    font-size:12px;
    font-weight:600;
    border-radius:20px;
    color:#fff;
}

.status-live{
    background:#dc3545;
    animation:pulse 1.5s infinite;
}

.status-offline{
    background:#6c757d;
}

@keyframes pulse{
    0%{box-shadow:0 0 0 0 rgba(220,53,69,.7);}
    70%{box-shadow:0 0 0 12px rgba(220,53,69,0);}
    100%{box-shadow:0 0 0 0 rgba(220,53,69,0);}
}

.watch-btn{
    border-radius:20px;
    padding:6px 18px;
}
</style>
</head>

<body>

<div class="container py-5">

    <div class="text-center mb-5">
        <h1 class="fw-bold text-danger">🔴 Live Darshan</h1>
        <p class="text-muted text-center">
            Experience divine blessings from sacred temples across India
        </p>
    </div>

<?php
$query = "SELECT * FROM live_darshan ORDER BY temple_name ASC";
$result = mysqli_query($DatabaseCo->dbLink, $query);
$all_rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $all_rows[] = $r;
}
?>

<!-- Search: Temple dropdown (searchable) -->
<div class="row justify-content-center mb-4">
    <div class="col-12 col-md-8 col-lg-6 live-darshan-search-wrap">
        <label class="form-label fw-semibold" for="templeSearch">Search temple</label>
        <select id="templeSearch" class="form-select form-select-lg" style="width:100%;">
            <option value="">All temples</option>
            <?php foreach ($all_rows as $r): ?>
            <option value="<?= htmlspecialchars($r['temple_name'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($r['temple_name']); ?> — <?= htmlspecialchars($r['god_name']); ?> (<?= htmlspecialchars($r['location']); ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Selected temple details (shown when one temple is selected) -->
<!-- <div id="selectedTempleDetails" class="row justify-content-center mb-4 d-none">
    <div class="col-12 col-lg-8">
        <div class="darshan-card position-relative border border-2 border-danger rounded-3 overflow-hidden bg-white shadow">
            <span id="selStatusBadge" class="status-badge status-offline">—</span>
            <div class="row g-0 align-items-center">
                <div class="col-md-4">
                    <img id="selThumb" src="" alt="" class="darshan-thumb w-100" style="height:220px;object-fit:cover;" onerror="this.src='assets/images/default-image.png'">
                </div>
                <div class="col-md-8 p-4">
                    <h5 id="selTempleName" class="fw-bold text-danger mb-1"></h5>
                    <p id="selGodName" class="text-muted mb-1"></p>
                    <p id="selLocation" class="small mb-2"></p>
                    <p id="selStreamTime" class="small text-success mb-3"></p>
                    <button id="selWatchBtn" type="button" class="btn btn-danger watch-btn">▶ Watch Live</button>
                </div>
            </div>
        </div>
    </div>
</div> -->

<!-- HIDDEN TABLE (FOR DATATABLE PAGINATION) -->
<table id="darshanTable" class="table d-none">
<thead>
<tr>
    <th>Temple</th>
    <th>God</th>
    <th>Location</th>
</tr>
</thead>
<tbody>
<?php foreach ($all_rows as $row): ?>
<tr data-row='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8"); ?>'>
    <td><?= htmlspecialchars($row['temple_name']); ?></td>
    <td><?= htmlspecialchars($row['god_name']); ?></td>
    <td><?= htmlspecialchars($row['location']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- CARD VIEW -->
<div class="row" id="darshanCards"></div>

</div>

<!-- VIDEO MODAL -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <iframe id="videoFrame" width="100%" height="450"
                    frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<?php 
include('./include/footer.php');
?>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let table = $('#darshanTable').DataTable({
    pageLength: 6,
    lengthChange: false,
    ordering: false,
    searching: true,
    language:{
        search: "🔍 Filter:",
        searchPlaceholder: "Type here..."
    }
});

/* Searchable temple dropdown (Select2) */
$('#templeSearch').select2({
    placeholder: 'Search or select a temple…',
    allowClear: true,
    width: '100%'
});

/* Filtxr table and cards when temple is selected */
$('#templeSearch').on('change', function(){
    var val = $(this).val();
    table.column(0).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();

    /* Show selected temple details panel when one temple is chosen */
    var $details = $('#selectedTempleDetails');
    if (!val) {
        $details.addClass('d-none');
        return;
    }
    var $row = $('#darshanTable tbody tr').filter(function(){
        return $(this).find('td:first').text().trim() === val;
    }).first();
    if (!$row.length) return;
    var data = JSON.parse($row.attr('data-row'));
    var statusClass = data.status === 'Live' ? 'status-live' : 'status-offline';
    var embedUrl = (data.live_url || '').replace('watch?v=', 'embed/');
    $('#selStatusBadge').removeClass('status-live status-offline').addClass(statusClass).text(data.status || '—');
    $('#selThumb').attr('src', data.thumbnail || 'assets/images/default-image.png').attr('alt', data.temple_name);
    $('#selTempleName').text(data.temple_name || '');
    $('#selGodName').text(data.god_name || '');
    $('#selLocation').text(data.location || '');
    $('#selStreamTime').html('🕒 ' + (data.stream_start || '24x7') + (data.stream_end ? ' – ' + data.stream_end : ''));
    $('#selWatchBtn').off('click').on('click', function(){ playVideo(embedUrl); }).prop('disabled', data.status === 'Offline');
    $details.removeClass('d-none');
});

/* Render cards on pagination/filter */
table.on('draw', function(){
    renderCards();
});
renderCards();

function renderCards(){
    let html = '';
    let rows = table.rows({ page:'current' }).nodes();

    $(rows).each(function(){
        let data = JSON.parse($(this).attr('data-row'));

        let statusClass = data.status === 'Live'
            ? 'status-live'
            : 'status-offline';

        let embedUrl = data.live_url.replace('watch?v=', 'embed/');

        html += `
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="darshan-card position-relative h-100">

                <span class="status-badge ${statusClass}">
                    ${data.status}
                </span>

                <img src="${data.thumbnail || 'assets/images/default-image.png'}"
                     class="darshan-thumb"
                     onerror="this.src='assets/images/default-image.png'">

                <div class="p-3 text-center">
                    <h6 class="fw-bold">${data.temple_name}</h6>
                    <p class="text-muted mb-1">${data.god_name}</p>
                    <small>${data.location}</small>

                    <div class="small text-success mt-1">
                        🕒 ${data.stream_start ?? '24x7'}
                        ${data.stream_end ? ' - ' + data.stream_end : ''}
                    </div>

                    <button class="btn btn-danger btn-sm mt-3 watch-btn"
                        onclick="playVideo('${embedUrl}')"
                        ${data.status === 'Offline' ? 'disabled' : ''}>
                        ▶ Watch Live
                    </button>
                </div>

            </div>
        </div>`;
    });

    $('#darshanCards').html(html);
}

/* Video Controls */
function playVideo(url){
    $('#videoFrame').attr('src', url + '?autoplay=1');
    $('#videoModal').modal('show');
}

$('#videoModal').on('hidden.bs.modal', function(){
    $('#videoFrame').attr('src','');
});
</script>
