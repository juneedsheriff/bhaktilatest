<?php

/**
 * Mystery temples — same data as app/temple-mystery-listing.php (temples, iconic, abroad).
 * No SQL UNION (avoids collation fatals on PHP 8+).
 */
function mystery_where()
{
    return 'my_stery = 1';
}

/** @return list<array{type:string,table:string,detail:string,upload:string}> */
function mystery_sources()
{
    return [
        ['type' => 'temple', 'table' => 'temples', 'upload' => 'temple'],
        ['type' => 'iconic', 'table' => 'iconic', 'upload' => 'iconic'],
        ['type' => 'abroad', 'table' => 'abroad', 'upload' => 'abroad'],
    ];
}

function mystery_db_query($db, $sql)
{
    try {
        $result = @mysqli_query($db, $sql);
        return $result instanceof mysqli_result ? $result : false;
    } catch (Throwable $e) {
        return false;
    }
}

function mystery_god_name($db, $godId)
{
    $godId = (int) $godId;
    if ($godId <= 0) {
        return '';
    }

    $result = mystery_db_query($db, "SELECT god_name FROM god WHERE index_id = {$godId} LIMIT 1");
    if (!$result) {
        return '';
    }

    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    return (string) ($row['god_name'] ?? '');
}

function mystery_fetch_gods($db)
{
    $godIds = [];

    foreach (mystery_sources() as $src) {
        $sql = 'SELECT DISTINCT god_id FROM `' . $src['table'] . '` WHERE ' . mystery_where() . ' AND god_id > 0';
        $result = mystery_db_query($db, $sql);
        if (!$result) {
            continue;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $id = (int) ($row['god_id'] ?? 0);
            if ($id > 0) {
                $godIds[$id] = true;
            }
        }
        mysqli_free_result($result);
    }

    if (empty($godIds)) {
        return [];
    }

    $idList = implode(',', array_keys($godIds));
    $result = mystery_db_query($db, "SELECT index_id, god_name FROM god WHERE index_id IN ({$idList}) ORDER BY god_name ASC");
    if (!$result) {
        return [];
    }

    $gods = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $gods[] = $row;
    }
    mysqli_free_result($result);

    return $gods;
}

/**
 * Collect rows in admin order: temples, then iconic, then abroad (each index_id DESC).
 */
function mystery_collect_items($db, array $godIds = [])
{
    $where = mystery_where();
    $items = [];

    foreach (mystery_sources() as $src) {
        $sql = 'SELECT index_id, title, photos, city, state, country, god_id
            FROM `' . $src['table'] . '` WHERE ' . $where;

        if (!empty($godIds)) {
            $ids = array_map('intval', $godIds);
            $sql .= ' AND god_id IN (' . implode(',', $ids) . ')';
        }

        $sql .= ' ORDER BY index_id DESC';

        $result = mystery_db_query($db, $sql);
        if (!$result) {
            continue;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $row['item_type'] = $src['type'];
            $row['source_table'] = $src['table'];
            $row['upload_folder'] = $src['upload'];
            $items[] = $row;
        }
        mysqli_free_result($result);
    }

    return $items;
}

function mystery_count_items($db, array $godIds = [])
{
    return count(mystery_collect_items($db, $godIds));
}

function mystery_detail_url(array $row)
{
    $type = (string) ($row['item_type'] ?? 'temple');
    $id = (int) ($row['index_id'] ?? 0);

    return 'mystery-details.php?id=' . $id . '&type=' . rawurlencode($type);
}

function mystery_upload_folder(array $row)
{
    return (string) ($row['upload_folder'] ?? 'temple');
}

function mystery_listing_html($db, array $row)
{
    $photos = (string) ($row['photos'] ?? '');
    $title = htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $detailUrl = mystery_detail_url($row);
    $uploadFolder = mystery_upload_folder($row);
    $cityName = '';
    $stateName = '';
    if (!empty($row['city'])) {
        $cityId = mysqli_real_escape_string($db, (string) $row['city']);
        $ccc = mystery_db_query($db, "SELECT city_name FROM `city` WHERE city_id='{$cityId}' LIMIT 1");
        if ($ccc && ($cff = mysqli_fetch_assoc($ccc))) {
            $cityName = htmlspecialchars((string) ($cff['city_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        }
        if ($ccc) {
            mysqli_free_result($ccc);
        }
    }
    if (!empty($row['state']) && !empty($row['country'])) {
        $stateCode = mysqli_real_escape_string($db, (string) $row['state']);
        $countryCode = mysqli_real_escape_string($db, (string) $row['country']);
        $sss = mystery_db_query(
            $db,
            "SELECT state_name FROM `state` WHERE state_code='{$stateCode}' AND country_code='{$countryCode}' LIMIT 1"
        );
        if ($sss && ($fff = mysqli_fetch_assoc($sss))) {
            $stateName = htmlspecialchars((string) ($fff['state_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        }
        if ($sss) {
            mysqli_free_result($sss);
        }
    }

    $locationSuffix = '';
    if ($cityName !== '') {
        $locationSuffix .= ', ' . $cityName;
    }
    if ($stateName !== '') {
        $locationSuffix .= ', ' . $stateName;
    }

    ob_start();
    ?>
    <div class="listing">
        <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>">
            <img src="app/uploads/<?php echo htmlspecialchars($uploadFolder, ENT_QUOTES, 'UTF-8'); ?>/<?php echo htmlspecialchars($photos, ENT_QUOTES, 'UTF-8'); ?>" alt="">
        </a>
        <div class="listing-details">
            <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <div class="listing-title"><?php echo $title . $locationSuffix; ?></div>
            </a>
            <div class="listing-rating text-dark">
                <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>">Read more</a>
            </div>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}
