<?php

function abroad_temple_listing_valid_tabs(): array
{
    return ['approved', 'pending', 'rejected'];
}

function abroad_temple_listing_rejected_sql(string $alias = 'a'): string
{
    $status = $alias !== '' ? $alias . '.`status`' : '`status`';

    return "( LOWER(TRIM(COALESCE({$status}, ''))) IN ('rejected', 'reject', 'denied', 'disapproved') OR TRIM(COALESCE({$status}, '')) = '' )";
}

function abroad_temple_listing_status_sql(string $list_temple_status, string $alias = 'a'): string
{
    $status = $alias !== '' ? $alias . '.`status`' : '`status`';

    if ($list_temple_status === 'approved') {
        return " AND LOWER(TRIM(COALESCE({$status}, ''))) = 'approved' ";
    }
    if ($list_temple_status === 'pending') {
        return " AND LOWER(TRIM(COALESCE({$status}, ''))) = 'unapproved' ";
    }
    if ($list_temple_status === 'rejected') {
        return ' AND ' . abroad_temple_listing_rejected_sql($alias) . ' ';
    }

    return '';
}

function abroad_temple_listing_opt_where(string $list_temple_status): string
{
    return 'a.index_id != \'0\'' . abroad_temple_listing_status_sql($list_temple_status, 'a');
}

function abroad_temple_listing_parse_filters(mysqli $db, array $params): array
{
    $f_country = isset($params['f_country']) ? trim((string) $params['f_country']) : '';
    $f_place = isset($params['f_place']) ? trim((string) $params['f_place']) : '';
    $f_god = isset($params['f_god']) ? (int) $params['f_god'] : 0;
    $f_tid = isset($params['f_tid']) ? (int) $params['f_tid'] : 0;

    $filter_sql = '';
    if ($f_country !== '' && strtoupper($f_country) !== 'ALL') {
        $filter_sql .= " AND a.`country` = '" . $db->real_escape_string($f_country) . "' ";
    }
    if ($f_place !== '' && strtoupper($f_place) !== 'ALL') {
        $filter_sql .= " AND a.`temple_place` = '" . $db->real_escape_string($f_place) . "' ";
    }
    if ($f_god > 0) {
        $filter_sql .= ' AND a.`god_id` = ' . $f_god . ' ';
    }
    if ($f_tid > 0) {
        $filter_sql .= ' AND a.`index_id` = ' . $f_tid . ' ';
    }

    $has_listing_filters = ($f_country !== '' && strtoupper($f_country) !== 'ALL')
        || ($f_place !== '' && strtoupper($f_place) !== 'ALL')
        || $f_god > 0;

    return [
        'f_country' => $f_country,
        'f_place' => $f_place,
        'f_god' => $f_god,
        'f_tid' => $f_tid,
        'filter_sql' => $filter_sql,
        'has_listing_filters' => $has_listing_filters,
    ];
}

function abroad_temple_listing_where_sql(string $list_temple_status, string $filter_sql, string $search = ''): string
{
    $where = abroad_temple_listing_opt_where($list_temple_status) . $filter_sql;
    if ($search !== '') {
        $where .= " AND (a.`title` LIKE '%" . $search . "%' OR a.`temple_place` LIKE '%" . $search . "%') ";
    }

    return $where;
}

function abroad_temple_listing_order_sql(int $columnIndex, string $direction): string
{
    $direction = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
    $columns = [
        0 => 'a.index_id',
        1 => 'a.photos',
        2 => 'a.title',
        3 => 'a.temple_place',
        4 => 'a.status',
    ];

    $column = $columns[$columnIndex] ?? 'a.index_id';

    return $column . ' ' . $direction;
}

function abroad_temple_listing_row_status_html(string $status): string
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

function abroad_temple_listing_action_html(int $indexId, string $status, string $userRole): string
{
    $rowStatus = strtolower(trim($status));
    $html = '';

    if ($userRole === 'Admin') {
        $html .= '<a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" href="add-abroad-temple.php?id=' . $indexId . '"><i class="fas fa-pencil-alt"></i></a> &nbsp; &nbsp;';
        $html .= '<a class="btn btn-sm p-2 btn-danger delete-board alert-box-trigger waves-effect waves-light kill-drop text-white" data-modal="delete-board-alert" data-toggle="modal" data-target="#delete-board-alert" href="#0" data-id="' . $indexId . '" id="delete-board' . $indexId . '"><i class="fa fa-trash text-white"></i></a>';
    } elseif ($userRole === 'Staff' && $rowStatus === 'unapproved') {
        $html .= '<a class="btn btn-sm p-2 btn-primary text-white edit-board alert-box-trigger waves-effect waves-light kill-drop" href="add-abroad-temple.php?id=' . $indexId . '"><i class="fas fa-pencil-alt"></i></a>';
    }

    return $html;
}

function abroad_temple_listing_fetch_datatable(
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
    $where = abroad_temple_listing_where_sql($list_temple_status, $filter_sql, $searchEsc);
    $from = ' FROM `abroad` a WHERE ' . $where;

    $countRes = mysqli_query($db, 'SELECT COUNT(*) AS total' . $from);
    if (!$countRes) {
        return ['error' => mysqli_error($db)];
    }
    $recordsFiltered = (int) (mysqli_fetch_assoc($countRes)['total'] ?? 0);

    $totalWhere = abroad_temple_listing_opt_where($list_temple_status);
    $totalRes = mysqli_query($db, 'SELECT COUNT(*) AS total FROM `abroad` a WHERE ' . $totalWhere);
    $recordsTotal = $totalRes ? (int) (mysqli_fetch_assoc($totalRes)['total'] ?? 0) : 0;

    $orderSql = abroad_temple_listing_order_sql($orderColumn, $orderDir);
    $limitSql = $length > 0 ? ' LIMIT ' . max(0, $start) . ', ' . (int) $length : '';

    $sql = 'SELECT a.index_id, a.title, a.photos, a.temple_place, a.status'
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
        $title = htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $place = htmlspecialchars(trim((string) ($row['temple_place'] ?? '')), ENT_QUOTES, 'UTF-8');
        $photoHtml = '';
        if ($photos !== '') {
            $photoEsc = htmlspecialchars($photos, ENT_QUOTES, 'UTF-8');
            $photoHtml = '<a href="./uploads/abroad/' . $photoEsc . '" target="_blank"><img src="./uploads/abroad/' . $photoEsc . '" class="header-profile-user" width="60" alt="" loading="lazy"></a>';
        }

        $rows[] = [
            $serial++,
            $photoHtml,
            $title,
            $place,
            abroad_temple_listing_row_status_html((string) ($row['status'] ?? '')),
            abroad_temple_listing_action_html($indexId, (string) ($row['status'] ?? ''), $userRole),
        ];
    }

    return [
        'recordsTotal' => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data' => $rows,
    ];
}
