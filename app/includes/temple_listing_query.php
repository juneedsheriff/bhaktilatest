<?php

function temple_table_type_options(): array
{
    return [
        'Aathara Sthalam',
        'Abodes of Murugan',
        'Ashta Veeratta Temples',
        'Asta Vinayak Temples',
        'Chardam Yatra Temples',
        'Divya Desams',
        'Durga Aalayams by Sage Parasurama',
        'Jyothirlingams',
        'Mandi Mandaean Temple',
        'Muktiskhetras',
        'Nagadevatas Temples',
        'Nakshatra Temples & Trees',
        'Narasimha Skhetras',
        'Nava Puliyur Temples',
        'Nava Tirupati Temples',
        'Navagraha Parihara Temples',
        'Others',
        'Paadal Petra Sthalams',
        'Pancha Bhoota Sthalams',
        'Pancha Dwaraka Temples',
        'Pancha Kannan Temples',
        'Pancha Kedar Temples',
        'Pancha Pandava Temples',
        'Pancha Ranga Kshetras',
        'Pancha Sabhai Thalangal',
        'Pancharama Skhetras',
        'Parihara Shiva Temples',
        'Saptha Mangai Stalangal',
        'Saptha Stana Temples',
        'Saptha Sthana Sthalams',
        'Saptha Vidangam',
        'Sapthavigraha Moorthis',
        'Sastha Aalayam by Sage Parasurama',
        'Shakti Peethas',
        'Shiridi Sai Temples',
        'Shiva Temples by Sage Parasurama',
        'Swayambhu Temples',
        'Tevaram Vaippu Sthalams',
        'Vishnumaya Temples',
    ];
}

/** Map CSV column S raw values to canonical table_type values. Null = skip. */
function temple_table_type_csv_map(): array
{
    return [
        'All' => null,
        'Shakti Peetham' => 'Shakti Peethas',
        'Jyothirlingams' => 'Jyothirlingams',
        'Panchabhoota Sthalam' => 'Pancha Bhoota Sthalams',
        'Six Adobes of Murugan(Arupadai Veedu)' => 'Abodes of Murugan',
        'Pancha Pandava Temples' => 'Pancha Pandava Temples',
        'Swayambhu' => 'Swayambhu Temples',
        'Chardham' => 'Chardam Yatra Temples',
        'Pancharama Kshetras' => 'Pancharama Skhetras',
        'Parihaaram Places' => 'Others',
        'Mahaskhetras' => 'Muktiskhetras',
        'Divya Desams' => 'Divya Desams',
    ];
}

function temple_table_type_normalize(string $value): ?string
{
    $value = trim($value);
    if ($value === '' || strtolower($value) === 'null') {
        return null;
    }

    $map = temple_table_type_csv_map();
    if (array_key_exists($value, $map)) {
        return $map[$value];
    }

    return in_array($value, temple_table_type_options(), true) ? $value : null;
}

/** Distinct table_type values present in CSV column S (after normalization). */
function temple_table_type_csv_options(): array
{
    $options = [];
    foreach (temple_table_type_csv_map() as $mapped) {
        if ($mapped !== null && !in_array($mapped, $options, true)) {
            $options[] = $mapped;
        }
    }
    sort($options);

    return $options;
}

function temple_table_type_find_temple_id(mysqli $db, int $legacyId, string $title, string $state = '', string $place = ''): int
{
    if ($legacyId > 0) {
        $check = mysqli_query($db, "SELECT index_id FROM temples WHERE index_id = $legacyId LIMIT 1");
        if ($check && ($row = mysqli_fetch_assoc($check))) {
            return (int) $row['index_id'];
        }

        $orderCheck = mysqli_query(
            $db,
            "SELECT index_id FROM temples WHERE order_by = $legacyId
             ORDER BY CASE WHEN index_id = $legacyId THEN 0 ELSE 1 END, index_id ASC
             LIMIT 1"
        );
        if ($orderCheck && ($row = mysqli_fetch_assoc($orderCheck))) {
            return (int) $row['index_id'];
        }
    }

    $title = trim($title);
    if ($title === '') {
        return 0;
    }

    $titleEsc = $db->real_escape_string($title);
    $stateEsc = $db->real_escape_string(trim($state));
    $placeEsc = $db->real_escape_string(trim($place));

    if ($stateEsc !== '' && $placeEsc !== '') {
        $dup = mysqli_query(
            $db,
            "SELECT index_id FROM temples
             WHERE title = '$titleEsc' AND state = '$stateEsc' AND temple_place = '$placeEsc'
             LIMIT 1"
        );
        if ($dup && ($row = mysqli_fetch_assoc($dup))) {
            return (int) $row['index_id'];
        }
    }

    $dup = mysqli_query($db, "SELECT index_id FROM temples WHERE title = '$titleEsc' LIMIT 1");
    if ($dup && ($row = mysqli_fetch_assoc($dup))) {
        return (int) $row['index_id'];
    }

    return 0;
}

function temple_listing_valid_tabs(): array
{
    return ['approved', 'pending', 'rejected'];
}

function temple_listing_status_sql(string $list_temple_status): string
{
    if ($list_temple_status === 'approved') {
        return " AND t.`status` = 'approved' ";
    }
    if ($list_temple_status === 'pending') {
        return " AND t.`status` = 'unapproved' ";
    }
    if ($list_temple_status === 'rejected') {
        return " AND (t.`status` IN ('rejected') OR t.`status` IS NULL OR TRIM(t.`status`) = '') ";
    }

    return '';
}

function temple_listing_opt_where(string $list_temple_status): string
{
    return "t.index_id > 0" . temple_listing_status_sql($list_temple_status);
}

function temple_listing_parse_filters(mysqli $db, array $params): array
{
    $f_state = isset($params['f_state']) ? trim((string) $params['f_state']) : '';
    $f_place = isset($params['f_place']) ? trim((string) $params['f_place']) : '';
    $f_type = isset($params['f_type']) ? trim((string) $params['f_type']) : '';
    $f_god = isset($params['f_god']) ? (int) $params['f_god'] : 0;
    $f_tid = isset($params['f_tid']) ? (int) $params['f_tid'] : 0;

    $filter_sql = '';
    if ($f_state !== '' && strtoupper($f_state) !== 'ALL') {
        $filter_sql .= " AND t.`state` = '" . $db->real_escape_string($f_state) . "' ";
    }
    if ($f_place !== '' && strtoupper($f_place) !== 'ALL') {
        $filter_sql .= " AND t.`city` = '" . $db->real_escape_string($f_place) . "' ";
    }
    if ($f_type !== '' && strtoupper($f_type) !== 'ALL') {
        $filter_sql .= " AND t.`table_type` = '" . $db->real_escape_string($f_type) . "' ";
    }
    if ($f_god > 0) {
        $filter_sql .= ' AND t.`god_id` = ' . $f_god . ' ';
    }
    if ($f_tid > 0) {
        $filter_sql .= ' AND t.`index_id` = ' . $f_tid . ' ';
    }

    $has_listing_filters = ($f_state !== '' && strtoupper($f_state) !== 'ALL')
        || ($f_place !== '' && strtoupper($f_place) !== 'ALL')
        || ($f_type !== '' && strtoupper($f_type) !== 'ALL')
        || $f_god > 0;

    return [
        'f_state' => $f_state,
        'f_place' => $f_place,
        'f_type' => $f_type,
        'f_god' => $f_god,
        'f_tid' => $f_tid,
        'filter_sql' => $filter_sql,
        'has_listing_filters' => $has_listing_filters,
    ];
}

function temple_listing_where_sql(string $list_temple_status, string $filter_sql, string $search = ''): string
{
    $where = temple_listing_opt_where($list_temple_status) . $filter_sql;
    if ($search !== '') {
        $where .= " AND (t.`title` LIKE '%" . $search . "%' OR g.`god_name` LIKE '%" . $search . "%') ";
    }

    return $where;
}

function temple_listing_order_sql(int $columnIndex, string $direction): string
{
    $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
    $columns = [
        0 => 't.index_id',
        1 => 't.photos',
        2 => 't.title',
        3 => 'g.god_name',
        4 => 't.table_type',
        5 => 't.status',
    ];

    $column = $columns[$columnIndex] ?? 't.index_id';

    return $column . ' ' . $direction;
}

function temple_listing_row_status_html(string $status): string
{
    $rowStatus = strtolower(trim($status));
    $isRejected = ($rowStatus === 'rejected' || $rowStatus === 'reject' || $rowStatus === 'denied' || $rowStatus === 'disapproved' || $rowStatus === '');

    if ($rowStatus === 'approved') {
        return '<div class="icon-container"><i class="fa fa-thumbs-up text-success" style="font-size: 20px;" title="Approved"></i></div>';
    }
    if ($isRejected) {
        return '<div class="icon-container"><i class="fa fa-ban text-danger" style="font-size: 20px;" title="Rejected"></i></div>';
    }

    return '<div class="icon-container"><i class="fa fa-clock text-warning" style="font-size: 20px;" title="Approval pending"></i></div>';
}

function temple_listing_action_html(int $indexId, string $status, string $userRole): string
{
    $rowStatus = strtolower(trim($status));
    $html = '';

    if ($userRole === 'Admin') {
        $html .= '<a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" href="add-temple.php?id=' . $indexId . '"><i class="fas fa-pencil-alt"></i></a> &nbsp; &nbsp;';
        $html .= '<a class="btn btn-sm p-2 btn-danger delete-board alert-box-trigger waves-effect waves-light kill-drop text-white" data-modal="delete-board-alert" data-toggle="modal" data-target="#delete-board-alert" href="#0" data-id="' . $indexId . '" id="delete-board' . $indexId . '"><i class="fa fa-trash text-white"></i></a>';
    } elseif ($userRole === 'Staff' && $rowStatus === 'unapproved') {
        $html .= '<a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" href="add-temple.php?id=' . $indexId . '"><i class="fas fa-pencil-alt"></i></a>';
    }

    return $html;
}

function temple_listing_fetch_datatable(
    mysqli $db,
    string $list_temple_status,
    string $filter_sql,
    string $search,
    int $start,
    int $length,
    int $orderColumn,
    string $orderDir,
    string $userRole
): array {
    $searchEsc = $db->real_escape_string($search);
    $where = temple_listing_where_sql($list_temple_status, $filter_sql, $searchEsc);
    $from = ' FROM `temples` t LEFT JOIN `god` g ON g.index_id = t.god_id WHERE ' . $where;

    $countRes = mysqli_query($db, 'SELECT COUNT(*) AS total' . $from);
    if (!$countRes) {
        return ['error' => mysqli_error($db)];
    }
    $recordsFiltered = (int) (mysqli_fetch_assoc($countRes)['total'] ?? 0);

    $totalWhere = temple_listing_opt_where($list_temple_status);
    $totalRes = mysqli_query($db, 'SELECT COUNT(*) AS total FROM `temples` t WHERE ' . $totalWhere);
    $recordsTotal = $totalRes ? (int) (mysqli_fetch_assoc($totalRes)['total'] ?? 0) : 0;

    $orderSql = temple_listing_order_sql($orderColumn, $orderDir);
    $limitSql = $length > 0 ? ' LIMIT ' . max(0, $start) . ', ' . (int) $length : '';

    $sql = 'SELECT t.index_id, t.title, t.photos, t.status, t.table_type, g.god_name AS listing_god_name'
        . $from
        . ' ORDER BY ' . $orderSql
        . $limitSql;

    $result = mysqli_query($db, $sql);
    if (!$result) {
        return ['error' => mysqli_error($db)];
    }

    $rows = [];
    $serial = $start + 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $indexId = (int) ($row['index_id'] ?? 0);
        $photos = trim((string) ($row['photos'] ?? ''));
        $godName = htmlspecialchars(trim((string) ($row['listing_god_name'] ?? '')), ENT_QUOTES, 'UTF-8');
        $tableType = htmlspecialchars(trim((string) ($row['table_type'] ?? '')), ENT_QUOTES, 'UTF-8');
        $title = htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $photoHtml = '';
        if ($photos !== '') {
            $photoEsc = htmlspecialchars($photos, ENT_QUOTES, 'UTF-8');
            $photoHtml = '<a href="./uploads/temple/' . $photoEsc . '" target="_blank"><img src="./uploads/temple/' . $photoEsc . '" class="header-profile-user" width="60" alt="" loading="lazy"></a>';
        }

        $rows[] = [
            $serial++,
            $photoHtml,
            $title,
            $godName,
            $tableType,
            temple_listing_row_status_html((string) ($row['status'] ?? '')),
            temple_listing_action_html($indexId, (string) ($row['status'] ?? ''), $userRole),
        ];
    }

    return [
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $rows,
    ];
}
