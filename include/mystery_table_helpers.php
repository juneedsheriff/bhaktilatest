<?php

/**
 * Mystery temples from the `mystery` database table + app/uploads/Mystery images.
 */

function mystery_table_db_query($db, $sql)
{
    try {
        $result = @mysqli_query($db, $sql);
        if ($result === false) {
            return false;
        }

        return $result;
    } catch (Throwable $e) {
        return false;
    }
}

function mystery_table_image_url($filename)
{
    $filename = trim((string) $filename);
    if ($filename === '') {
        return 'assets/images/default-image.png';
    }

    return 'app/uploads/Mystery/' . basename(str_replace('\\', '/', $filename));
}

function mystery_table_normalize_id($id)
{
    $id = trim((string) $id);
    $id = preg_replace('/^\xEF\xBB\xBF/', '', $id);

    return $id;
}

function mystery_table_has_status_column($db)
{
    static $has = null;
    if ($has !== null) {
        return $has;
    }

    if (!($db instanceof mysqli)) {
        $has = false;
        return $has;
    }

    $check = mystery_table_db_query($db, "SHOW COLUMNS FROM `mystery` LIKE 'status'");
    $has = ($check instanceof mysqli_result && mysqli_num_rows($check) > 0);
    if ($check instanceof mysqli_result) {
        mysqli_free_result($check);
    }

    return $has;
}

function mystery_table_where_approved($db = null)
{
    if ($db instanceof mysqli && !mystery_table_has_status_column($db)) {
        return '1=1';
    }

    return "(m.status = '' OR m.status IS NULL OR LOWER(m.status) = 'approved')";
}

/** @return array<string, int> longest keywords first */
function mystery_table_title_god_keywords()
{
    return [
        'venkateshwara' => 94,
        'venkateswar' => 94,
        'padmanabhaswamy' => 614,
        'padmanabha' => 614,
        'meenakshi' => 57,
        'kamakhya' => 417,
        'jagannath' => 274,
        'kaal bhairav' => 90,
        'kal bhairav' => 90,
        'narasimha' => 55,
        'subramanya' => 144,
        'lingaraja' => 34,
        'lingam' => 34,
        'mahadev' => 34,
        'nataraja' => 4,
        'bhairava' => 376,
        'bhairav' => 90,
        'krishna' => 80,
        'brahmeswar' => 37,
        'hanuman' => 47,
        'murugan' => 31,
        'ayyappa' => 36,
        'vinayaka' => 42,
        'ganapati' => 42,
        'ganesh' => 42,
        'brahma' => 37,
        'shiva' => 34,
        'vishnu' => 89,
        'durga' => 41,
        'lakshmi' => 54,
        'govinda' => 485,
        'perumal' => 184,
        'bhagavathy' => 444,
        'amman' => 444,
        'balaji' => 94,
        'sri rama' => 81,
        'rama' => 81,
        'devi' => 41,
    ];
}

function mystery_table_resolve_god_id_from_title($title)
{
    $title = strtolower(trim((string) $title));
    if ($title === '') {
        return 0;
    }

    foreach (mystery_table_title_god_keywords() as $keyword => $godId) {
        if (strpos($title, $keyword) !== false) {
            return (int) $godId;
        }
    }

    return 0;
}

function mystery_table_backfill_god_ids($db)
{
    $result = mystery_table_db_query(
        $db,
        "SELECT index_id, title, god_id FROM `mystery` WHERE god_id IS NULL OR god_id = '' OR god_id = '0'"
    );
    if (!($result instanceof mysqli_result)) {
        return 0;
    }

    $updated = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $godId = mystery_table_resolve_god_id_from_title($row['title'] ?? '');
        if ($godId <= 0) {
            continue;
        }

        $indexId = (int) ($row['index_id'] ?? 0);
        if ($indexId <= 0) {
            continue;
        }

        if (mystery_table_db_query($db, "UPDATE `mystery` SET god_id = '{$godId}' WHERE index_id = {$indexId}")) {
            $updated++;
        }
    }
    mysqli_free_result($result);

    return $updated;
}

function mystery_table_row_to_item(array $row)
{
    $title = trim((string) ($row['title'] ?? ''));
    $photo1 = trim((string) ($row['photos'] ?? ''));
    $photo2 = trim((string) ($row['photo2'] ?? ''));
    $vFor = (string) ($row['description'] ?? '');
    $history = (string) ($row['small_description'] ?? '');
    $location = trim((string) ($row['address'] ?? ''));
    $godName = trim((string) ($row['god_name_resolved'] ?? ''));

    return [
        'index_id' => (string) ($row['index_id'] ?? ''),
        'name' => $title,
        'title' => $title,
        'v_for' => $vFor,
        'history' => $history,
        'description' => $vFor,
        'small_description' => $history,
        'image1' => basename(str_replace('\\', '/', $photo1)),
        'image2' => basename(str_replace('\\', '/', $photo2)),
        'photos' => basename(str_replace('\\', '/', $photo1)),
        'photo2' => basename(str_replace('\\', '/', $photo2)),
        'image_url' => mystery_table_image_url($photo1),
        'gallery_images' => $photo2 !== '' ? [mystery_table_image_url($photo2)] : [],
        'status' => trim((string) ($row['status'] ?? '')),
        'log_date' => trim((string) ($row['log_date'] ?? '')),
        'god_label' => $godName,
        'god_name' => $godName,
        'god_id' => trim((string) ($row['god_id'] ?? '')),
        'location' => $location,
        'item_type' => 'mystery',
        'source_table' => 'mystery',
        'upload_folder' => 'Mystery',
    ];
}

function mystery_table_load_recent($db, $limit = 50)
{
    $limit = max(1, (int) $limit);
    $items = [];
    $sql = 'SELECT m.*, g.god_name AS god_name_resolved
        FROM `mystery` m
        LEFT JOIN `god` g ON g.index_id = m.god_id
        WHERE ' . mystery_table_where_approved($db) . '
        ORDER BY m.index_id DESC
        LIMIT ' . $limit;
    $result = mystery_table_db_query($db, $sql);
    if (!($result instanceof mysqli_result)) {
        return $items;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $item = mystery_table_row_to_item($row);
        if ($item['title'] !== '') {
            $items[] = $item;
        }
    }
    mysqli_free_result($result);

    return $items;
}

function mystery_table_load_all($db)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $cache = [];
    $sql = 'SELECT m.*, g.god_name AS god_name_resolved
        FROM `mystery` m
        LEFT JOIN `god` g ON g.index_id = m.god_id
        WHERE ' . mystery_table_where_approved($db) . '
        ORDER BY m.index_id ASC';
    $result = mystery_table_db_query($db, $sql);
    if (!($result instanceof mysqli_result)) {
        return $cache;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $item = mystery_table_row_to_item($row);
        if ($item['title'] !== '') {
            $cache[] = $item;
        }
    }
    mysqli_free_result($result);

    return $cache;
}

function mystery_table_get_by_id($db, $id)
{
    $id = mystery_table_normalize_id($id);
    if ($id === '') {
        return null;
    }

    $idEsc = mysqli_real_escape_string($db, $id);
    $sql = "SELECT m.*, g.god_name AS god_name_resolved
        FROM `mystery` m
        LEFT JOIN `god` g ON g.index_id = m.god_id
        WHERE m.index_id = '{$idEsc}' AND " . mystery_table_where_approved($db) . ' LIMIT 1';
    $result = mystery_table_db_query($db, $sql);
    if (!($result instanceof mysqli_result)) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    return $row ? mystery_table_row_to_item($row) : null;
}

function mystery_table_detail_url(array $row)
{
    $id = rawurlencode((string) ($row['index_id'] ?? ''));

    return 'mystery-details.php?id=' . $id;
}

function mystery_table_fetch_gods($db)
{
    $sql = 'SELECT DISTINCT g.index_id, g.god_name
        FROM `mystery` m
        INNER JOIN `god` g ON g.index_id = m.god_id
        WHERE ' . mystery_table_where_approved($db) . "
          AND m.god_id IS NOT NULL AND m.god_id != '' AND m.god_id != '0'
        ORDER BY g.god_name ASC";

    $result = mystery_table_db_query($db, $sql);
    if (!($result instanceof mysqli_result)) {
        return [];
    }

    $gods = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $gods[] = $row;
    }
    mysqli_free_result($result);

    return $gods;
}

function mystery_table_filter_items($db, array $godIds = [])
{
    $items = mystery_table_load_all($db);
    if (empty($godIds)) {
        return $items;
    }

    $godIds = array_map('intval', $godIds);
    $godIds = array_values(array_filter($godIds, static function ($id) {
        return $id > 0;
    }));

    if (empty($godIds)) {
        return $items;
    }

    return array_values(array_filter($items, static function ($item) use ($godIds) {
        return in_array((int) ($item['god_id'] ?? 0), $godIds, true);
    }));
}

function mystery_table_listing_html(array $row)
{
    $title = htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars((string) ($row['image_url'] ?? ''), ENT_QUOTES, 'UTF-8');
    $detailUrl = htmlspecialchars(mystery_table_detail_url($row), ENT_QUOTES, 'UTF-8');
    $location = trim((string) ($row['location'] ?? ''));
    $locationSuffix = $location !== '' ? ', ' . htmlspecialchars($location, ENT_QUOTES, 'UTF-8') : '';

    ob_start();
    ?>
    <div class="listing">
        <a href="<?php echo $detailUrl; ?>">
            <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" onerror="this.onerror=null; this.src='assets/images/default-image.png';">
        </a>
        <div class="listing-details">
            <a href="<?php echo $detailUrl; ?>">
                <div class="listing-title"><?php echo $title . $locationSuffix; ?></div>
            </a>
            <div class="listing-rating text-dark">
                <a href="<?php echo $detailUrl; ?>">Read more</a>
            </div>
        </div>
    </div>
    <?php
    return (string) ob_get_clean();
}

function mystery_table_ensure_columns($db)
{
    $columns = [
        'photo2' => "ALTER TABLE `mystery` ADD COLUMN `photo2` VARCHAR(100) NOT NULL DEFAULT ''",
        'status' => "ALTER TABLE `mystery` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'Approved'",
        'god_label' => "ALTER TABLE `mystery` ADD COLUMN `god_label` VARCHAR(50) NOT NULL DEFAULT ''",
    ];

    foreach ($columns as $name => $sql) {
        $check = mystery_table_db_query($db, "SHOW COLUMNS FROM `mystery` LIKE '{$name}'");
        if ($check instanceof mysqli_result && mysqli_num_rows($check) === 0) {
            mystery_table_db_query($db, $sql);
        }
        if ($check instanceof mysqli_result) {
            mysqli_free_result($check);
        }
    }
}

function mystery_table_resolve_god_id($db, $godLabel)
{
    $godLabel = trim((string) $godLabel);
    if ($godLabel === '') {
        return '';
    }

    $esc = mysqli_real_escape_string($db, $godLabel);
    $result = mystery_table_db_query(
        $db,
        "SELECT index_id FROM `god` WHERE LOWER(god_name) = LOWER('{$esc}') LIMIT 1"
    );
    if ($result instanceof mysqli_result && ($row = mysqli_fetch_assoc($result))) {
        mysqli_free_result($result);
        return (string) ($row['index_id'] ?? '');
    }
    if ($result instanceof mysqli_result) {
        mysqli_free_result($result);
    }

    return '';
}

function mystery_table_parse_log_date($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '1970-01-01';
    }

    $ts = strtotime($value);

    return $ts ? date('Y-m-d', $ts) : '1970-01-01';
}

function mystery_table_import_csv($db)
{
    require_once __DIR__ . '/mystery_csv_helpers.php';

    mystery_table_ensure_columns($db);

    $imported = 0;
    $updated = 0;
    $file = mystery_csv_file_path();
    if (!is_readable($file)) {
        return ['imported' => 0, 'updated' => 0, 'error' => 'CSV file not readable'];
    }

    $handle = fopen($file, 'r');
    if (!$handle) {
        return ['imported' => 0, 'updated' => 0, 'error' => 'Could not open CSV'];
    }

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        $item = mystery_csv_parse_row($row);
        if ($item === null) {
            continue;
        }

        $indexId = (int) $item['index_id'];
        if ($indexId <= 0) {
            continue;
        }

        $title = mysqli_real_escape_string($db, mb_substr((string) $item['title'], 0, 100));
        $description = mysqli_real_escape_string($db, (string) ($item['description'] ?? ''));
        $smallDescription = mysqli_real_escape_string($db, (string) ($item['small_description'] ?? ''));
        $photos = mysqli_real_escape_string($db, (string) ($item['photos'] ?? ''));
        $photo2 = mysqli_real_escape_string($db, (string) ($item['photo2'] ?? ''));
        $status = mysqli_real_escape_string($db, (string) ($item['status'] ?? 'Approved'));
        $godLabel = mysqli_real_escape_string($db, (string) ($item['god_label'] ?? ''));
        $address = mysqli_real_escape_string($db, (string) ($item['location'] ?? ''));
        $logDate = mysqli_real_escape_string($db, mystery_table_parse_log_date($item['log_date'] ?? ''));
        $resolvedGodId = mystery_table_resolve_god_id_from_title($item['title'] ?? '');
        $godId = $resolvedGodId > 0 ? (string) $resolvedGodId : '';
        $godId = mysqli_real_escape_string($db, $godId);

        $exists = mystery_table_db_query($db, "SELECT index_id FROM `mystery` WHERE index_id = {$indexId} LIMIT 1");
        $rowExists = $exists instanceof mysqli_result && mysqli_num_rows($exists) > 0;
        if ($exists instanceof mysqli_result) {
            mysqli_free_result($exists);
        }

        if ($rowExists) {
            $sql = "UPDATE `mystery` SET
                `title` = '{$title}',
                `description` = '{$description}',
                `small_description` = '{$smallDescription}',
                `photos` = '{$photos}',
                `photo2` = '{$photo2}',
                `status` = '{$status}',
                `god_label` = '{$godLabel}',
                `god_id` = '{$godId}',
                `address` = '{$address}',
                `log_date` = '{$logDate}'
                WHERE `index_id` = {$indexId}";
            if (mystery_table_db_query($db, $sql)) {
                $updated++;
            }
        } else {
            $sql = "INSERT INTO `mystery`
                (`index_id`, `title`, `description`, `small_description`, `open_time`, `close_time`,
                 `country`, `state`, `city`, `address`, `god_id`, `god_label`, `log_date`, `photos`, `photo2`, `status`)
                VALUES
                ({$indexId}, '{$title}', '{$description}', '{$smallDescription}', '00:00:00', '00:00:00',
                 '', '0', '0', '{$address}', '{$godId}', '{$godLabel}', '{$logDate}', '{$photos}', '{$photo2}', '{$status}')";
            if (mystery_table_db_query($db, $sql)) {
                $imported++;
            }
        }
    }

    fclose($handle);

    $maxResult = mystery_table_db_query($db, 'SELECT MAX(index_id) AS max_id FROM `mystery`');
    if ($maxResult instanceof mysqli_result && ($maxRow = mysqli_fetch_assoc($maxResult))) {
        $nextId = (int) ($maxRow['max_id'] ?? 0) + 1;
        mystery_table_db_query($db, "ALTER TABLE `mystery` AUTO_INCREMENT = {$nextId}");
        mysqli_free_result($maxResult);
    }

    $backfilled = mystery_table_backfill_god_ids($db);

    return ['imported' => $imported, 'updated' => $updated, 'backfilled' => $backfilled, 'error' => ''];
}
