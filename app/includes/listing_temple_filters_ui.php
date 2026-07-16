<?php
/**
 * Shared filter bar UI + CSS fragment for temple listing pages.
 * Expects: $listing_filter_qs (array for http_build_query), $f_state, $f_place, $f_type, $f_god, $f_tid,
 * $opt_states (mysqli_result|false), $opt_places (mysqli_result|false), $opt_types (array rows or mysqli_result),
 * $opt_gods (mysqli_result|false), $opt_temples (mysqli_result|false),
 * $listing_type_label (string e.g. "Temple Type"), $listing_place_label (string e.g. "Place Name"),
 * $listing_primary_label (string e.g. "State" or "Country"), $listing_primary_field (string e.g. "f_state" or "f_country"),
 * $listing_primary_value_key, $listing_primary_label_key, $listing_clear_url (optional),
 * $listing_show_type_filter (bool, default true).
 */
if (!isset($listing_filter_qs) || !is_array($listing_filter_qs)) {
    $listing_filter_qs = [];
}
$listing_show_type_filter = $listing_show_type_filter ?? true;
$listing_primary_field = $listing_primary_field ?? 'f_state';
$listing_primary_label = $listing_primary_label ?? 'State';
$listing_primary_value_key = $listing_primary_value_key ?? 'state_code';
$listing_primary_label_key = $listing_primary_label_key ?? 'state_name';
$f_state = $f_state ?? '';
$f_country = $f_country ?? '';
$f_place = $f_place ?? '';
$f_type = $f_type ?? '';
$f_god = isset($f_god) ? (int) $f_god : 0;
$f_tid = isset($f_tid) ? (int) $f_tid : 0;
$primary_filter_value = '';
if ($listing_primary_field === 'f_country') {
    $primary_filter_value = $f_country;
} else {
    $primary_filter_value = $f_state;
}
?>
<style>
    .listing-filter-wrap { background: #eceeef; border-radius: 6px; padding: 1rem 1.1rem; margin-bottom: 1rem; }
    .listing-filter-wrap .filter-label { font-size: 0.7rem; letter-spacing: 0.04em; color: #555; margin-bottom: 0.28rem; text-transform: uppercase; display: block; font-weight: 600; }
    .listing-filter-wrap .form-select { min-width: 132px; max-width: 220px; height: 38px; font-size: 0.875rem; }
    .listing-filter-row { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 0.75rem 1rem; }
    .listing-filter-field { flex: 0 0 auto; }
    .btn-filter-search { background: #e8682e; color: #fff !important; font-weight: 600; border: none; padding: 0.5rem 1.35rem; border-radius: 4px; height: 38px; }
    .btn-filter-search:hover { background: #d45a24; color: #fff !important; }
    .btn-filter-clear { background: #6c757d; color: #fff !important; font-weight: 600; border: none; padding: 0.5rem 1.35rem; border-radius: 4px; height: 38px; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-filter-clear:hover { background: #5a6268; color: #fff !important; }
</style>
<div class="listing-filter-wrap">
    <form method="get" action="" class="listing-filter-row">
        <?php foreach ($listing_filter_qs as $qk => $qv): ?>
            <input type="hidden" name="<?php echo htmlspecialchars((string) $qk, ENT_QUOTES, 'UTF-8'); ?>" value="<?php echo htmlspecialchars((string) $qv, ENT_QUOTES, 'UTF-8'); ?>" />
        <?php endforeach; ?>
        <div class="listing-filter-field">
            <label class="filter-label" for="<?php echo htmlspecialchars($listing_primary_field, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($listing_primary_label, ENT_QUOTES, 'UTF-8'); ?></label>
            <select class="form-select" name="<?php echo htmlspecialchars($listing_primary_field, ENT_QUOTES, 'UTF-8'); ?>" id="<?php echo htmlspecialchars($listing_primary_field, ENT_QUOTES, 'UTF-8'); ?>">
                <option value="ALL"<?php echo ($primary_filter_value === '' || strtoupper($primary_filter_value) === 'ALL') ? ' selected' : ''; ?>>ALL</option>
                <?php
                if ($opt_states) {
                    while ($sr = mysqli_fetch_assoc($opt_states)) {
                        $code = (string) ($sr[$listing_primary_value_key] ?? '');
                        $name = (string) ($sr[$listing_primary_label_key] ?? $code);
                        if ($code === '') {
                            continue;
                        }
                        $sel = ($primary_filter_value !== '' && strtoupper($primary_filter_value) !== 'ALL' && $primary_filter_value === $code) ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="listing-filter-field">
            <label class="filter-label" for="f_place"><?php echo htmlspecialchars($listing_place_label ?? 'Place Name', ENT_QUOTES, 'UTF-8'); ?></label>
            <select class="form-select" name="f_place" id="f_place">
                <option value="ALL"<?php echo ($f_place === '' || strtoupper($f_place) === 'ALL') ? ' selected' : ''; ?>>ALL</option>
                <?php
                if ($opt_places) {
                    while ($pr = mysqli_fetch_assoc($opt_places)) {
                        $pv = (string) ($pr['place_value'] ?? '');
                        $pl = trim((string) ($pr['place_label'] ?? ''));
                        if ($pv === '' || $pl === '') {
                            continue;
                        }
                        $sel = ($f_place !== '' && strtoupper($f_place) !== 'ALL' && $f_place === $pv) ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($pv, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($pl, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <?php if ($listing_show_type_filter): ?>
        <div class="listing-filter-field">
            <label class="filter-label" for="f_type"><?php echo htmlspecialchars($listing_type_label ?? 'Temple Type', ENT_QUOTES, 'UTF-8'); ?></label>
            <select class="form-select" name="f_type" id="f_type">
                <option value="ALL"<?php echo ($f_type === '' || strtoupper($f_type) === 'ALL') ? ' selected' : ''; ?>>ALL</option>
                <?php
                if (isset($opt_types_mode) && $opt_types_mode === 'mystery') { ?>
                    <option value="0"<?php echo $f_type === '0' ? ' selected' : ''; ?>>Regular</option>
                    <option value="1"<?php echo $f_type === '1' ? ' selected' : ''; ?>>Mystery</option>
                <?php } elseif (!empty($opt_types) && is_array($opt_types)) {
                    foreach ($opt_types as $tr) {
                        $tid = (string) ($tr['type_id'] ?? '');
                        $tt = (string) ($tr['type_label'] ?? $tid);
                        if ($tid === '') {
                            continue;
                        }
                        $sel = ($f_type !== '' && strtoupper($f_type) !== 'ALL' && $f_type === $tid) ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($tt, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                } elseif (!empty($opt_types) && $opt_types instanceof mysqli_result) {
                    while ($tr = mysqli_fetch_assoc($opt_types)) {
                        $tid = (string) ($tr['type_id'] ?? '');
                        $tt = (string) ($tr['type_label'] ?? $tid);
                        if ($tid === '') {
                            continue;
                        }
                        $sel = ($f_type !== '' && strtoupper($f_type) !== 'ALL' && $f_type === $tid) ? ' selected' : '';
                        echo '<option value="' . htmlspecialchars($tid, ENT_QUOTES, 'UTF-8') . '"' . $sel . '>' . htmlspecialchars($tt, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="listing-filter-field">
            <label class="filter-label" for="f_god">God Name</label>
            <select class="form-select" name="f_god" id="f_god">
                <option value="0"<?php echo ($f_god <= 0) ? ' selected' : ''; ?>>ALL</option>
                <?php
                if ($opt_gods) {
                    while ($gr = mysqli_fetch_assoc($opt_gods)) {
                        $gid = (int) ($gr['god_id'] ?? 0);
                        $gn = (string) ($gr['god_name'] ?? '');
                        if ($gid <= 0) {
                            continue;
                        }
                        $sel = ((int) $f_god === $gid) ? ' selected' : '';
                        echo '<option value="' . $gid . '"' . $sel . '>' . htmlspecialchars($gn, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="listing-filter-field">
            <label class="filter-label" for="f_tid">Temple Name</label>
            <select class="form-select" name="f_tid" id="f_tid">
                <option value="0"<?php echo ($f_tid <= 0) ? ' selected' : ''; ?>>ALL</option>
                <?php
                if ($opt_temples) {
                    while ($tr = mysqli_fetch_assoc($opt_temples)) {
                        $tid = (int) ($tr['index_id'] ?? 0);
                        $tt = (string) ($tr['title'] ?? '');
                        if ($tid <= 0) {
                            continue;
                        }
                        $sel = ((int) $f_tid === $tid) ? ' selected' : '';
                        $label = (mb_strlen($tt) > 38) ? mb_substr($tt, 0, 38) . '.' : $tt;
                        echo '<option value="' . $tid . '"' . $sel . ' title="' . htmlspecialchars($tt, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div class="listing-filter-field d-flex gap-2">
            <button type="submit" class="btn btn-filter-search">SEARCH</button>
            <?php if (!empty($listing_clear_url)): ?>
            <a href="<?php echo htmlspecialchars($listing_clear_url, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-filter-clear">CLEAR ALL</a>
            <?php endif; ?>
        </div>
    </form>
</div>
