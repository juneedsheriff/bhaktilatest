<?php

/**
 * Mystery temples from mystertemple.csv (exported Mystery table) + app/uploads/Mystery images.
 *
 * ASP.NET mystery_temple_details.aspx.cs binddata():
 *   Name, V_For, History, image1, image2, tempstatus
 * CSV columns: [0] id, [1] Name, [2] V_For, [3] History, [4] image1, [5] image2, [6] status
 */

function mystery_csv_file_path()
{
    return __DIR__ . '/../app/data/mystertemple.csv';
}

function mystery_csv_image_url($path)
{
    $path = trim((string) $path);
    if ($path === '') {
        return 'assets/images/default-image.png';
    }

    $path = str_replace('\\', '/', $path);
    if (stripos($path, 'Mystery/') === 0) {
        $path = substr($path, strlen('Mystery/'));
    }

    return 'app/uploads/Mystery/' . $path;
}

function mystery_csv_normalize_id($id)
{
    $id = trim((string) $id);
    $id = preg_replace('/^\xEF\xBB\xBF/', '', $id);

    return $id;
}

function mystery_csv_parse_row(array $row)
{
    if (count($row) < 6) {
        return null;
    }

    $status = trim((string) ($row[6] ?? ''));
    if ($status !== '' && strcasecmp($status, 'Approved') !== 0) {
        return null;
    }

    $id = mystery_csv_normalize_id($row[0] ?? '');
    $title = trim((string) ($row[1] ?? ''));
    if ($id === '' || $title === '') {
        return null;
    }

    $image1 = trim((string) ($row[4] ?? ''));
    $image2 = trim((string) ($row[5] ?? ''));
    $vFor = (string) ($row[2] ?? '');
    $history = (string) ($row[3] ?? '');

    return [
        'index_id' => $id,
        'name' => $title,
        'title' => $title,
        'v_for' => $vFor,
        'history' => $history,
        // Legacy keys used elsewhere in the PHP app.
        'description' => $vFor,
        'small_description' => $history,
        'image1' => basename(str_replace('\\', '/', $image1)),
        'image2' => basename(str_replace('\\', '/', $image2)),
        'photos' => basename(str_replace('\\', '/', $image1)),
        'photo2' => basename(str_replace('\\', '/', $image2)),
        'image_url' => mystery_csv_image_url($image1),
        'gallery_images' => $image2 !== '' ? [mystery_csv_image_url($image2)] : [],
        'status' => $status,
        'log_date' => trim((string) ($row[7] ?? '')),
        'god_label' => trim((string) ($row[8] ?? '')),
        'location' => trim((string) ($row[9] ?? '')),
        'item_type' => 'csv',
        'source_table' => 'csv',
        'upload_folder' => 'Mystery',
    ];
}

function mystery_csv_load_all()
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $cache = [];
    $file = mystery_csv_file_path();
    if (!is_readable($file)) {
        return $cache;
    }

    $handle = fopen($file, 'r');
    if (!$handle) {
        return $cache;
    }

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        $item = mystery_csv_parse_row($row);
        if ($item !== null) {
            $cache[] = $item;
        }
    }

    fclose($handle);

    usort($cache, static function ($a, $b) {
        return (int) $a['index_id'] <=> (int) $b['index_id'];
    });

    return $cache;
}

function mystery_csv_get_by_id($id)
{
    $id = mystery_csv_normalize_id($id);
    foreach (mystery_csv_load_all() as $item) {
        if ((string) $item['index_id'] === (string) $id) {
            return $item;
        }
    }

    return null;
}

function mystery_csv_detail_url(array $row)
{
    $id = rawurlencode((string) ($row['index_id'] ?? ''));

    return 'mystery-details.php?id=' . $id;
}

function mystery_csv_fetch_gods()
{
    $gods = [];
    foreach (mystery_csv_load_all() as $item) {
        $label = trim((string) ($item['god_label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $key = strtolower($label);
        if (!isset($gods[$key])) {
            $gods[$key] = [
                'index_id' => $key,
                'god_name' => strtoupper($label),
            ];
        }
    }

    usort($gods, static function ($a, $b) {
        return strcasecmp($a['god_name'], $b['god_name']);
    });

    return array_values($gods);
}

function mystery_csv_filter_items(array $godKeys = [])
{
    $items = mystery_csv_load_all();
    if (empty($godKeys)) {
        return $items;
    }

    $godKeys = array_map('strtolower', $godKeys);

    return array_values(array_filter($items, static function ($item) use ($godKeys) {
        $label = strtolower(trim((string) ($item['god_label'] ?? '')));

        return in_array($label, $godKeys, true);
    }));
}

function mystery_csv_listing_html(array $row)
{
    $title = htmlspecialchars((string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars((string) ($row['image_url'] ?? ''), ENT_QUOTES, 'UTF-8');
    $detailUrl = htmlspecialchars(mystery_csv_detail_url($row), ENT_QUOTES, 'UTF-8');
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
