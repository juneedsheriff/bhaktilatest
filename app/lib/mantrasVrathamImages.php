<?php

function normalizeMantraTitleKey($title)
{
    return strtolower(trim(preg_replace('/\s+/', ' ', (string) $title)));
}

function getMantraDetailsUrl($title)
{
    return 'mantras-details.php?godname=' . rawurlencode(trim((string) $title));
}

function loadMantraVrathamMap()
{
    static $map = null;

    if ($map !== null) {
        return $map;
    }

    $map = [
        'by_id' => [],
        'by_title' => [],
    ];

    $csvPath = dirname(__DIR__) . '/data/mantras_vratham_images.csv';
    if (!is_readable($csvPath)) {
        return $map;
    }

    if (($handle = fopen($csvPath, 'r')) === false) {
        return $map;
    }

    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 3) {
            continue;
        }

        $indexId = (int) trim(preg_replace('/^\xEF\xBB\xBF/', '', $row[0]));
        $title = trim($row[1]);
        $imagePath = trim($row[2]);

        if ($indexId <= 0 || $title === '' || $imagePath === '') {
            continue;
        }

        $assetPath = mantraVrathamCsvPathToAsset($imagePath);
        if ($assetPath === '') {
            continue;
        }

        $map['by_id'][$indexId] = $assetPath;
        $map['by_title'][normalizeMantraTitleKey($title)] = $assetPath;
    }

    fclose($handle);

    return $map;
}

function mantraVrathamCsvPathToAsset($imagePath)
{
    $imagePath = str_replace('\\', '/', trim((string) $imagePath));
    if ($imagePath === '') {
        return '';
    }

    if (stripos($imagePath, 'assets/img/') === 0) {
        return $imagePath;
    }

    if (stripos($imagePath, 'img/vratham/') === 0) {
        return 'assets/' . $imagePath;
    }

    if (stripos($imagePath, 'vratham/') === 0) {
        return 'assets/img/' . $imagePath;
    }

    return 'assets/img/vratham/' . ltrim($imagePath, '/');
}

function getMantraVrathamImage($title, $indexId = null)
{
    $map = loadMantraVrathamMap();

    if ($indexId !== null && isset($map['by_id'][(int) $indexId])) {
        return $map['by_id'][(int) $indexId];
    }

    $titleKey = normalizeMantraTitleKey($title);
    if ($titleKey !== '' && isset($map['by_title'][$titleKey])) {
        return $map['by_title'][$titleKey];
    }

    return '';
}

function mantraVrathamResolveAssetPath($assetPath)
{
    $assetPath = trim((string) $assetPath);
    if ($assetPath === '') {
        return '';
    }

    $rootPath = dirname(__DIR__, 2);
    $fullPath = $rootPath . '/' . ltrim(str_replace('\\', '/', $assetPath), '/');

    if (is_file($fullPath)) {
        return $assetPath;
    }

    $directory = dirname($fullPath);
    $filename = basename($fullPath);

    if (!is_dir($directory)) {
        return '';
    }

    foreach (scandir($directory) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        if (strcasecmp($entry, $filename) === 0) {
            return dirname($assetPath) . '/' . $entry;
        }
    }

    return '';
}

function mantraVrathamAssetExists($assetPath)
{
    return mantraVrathamResolveAssetPath($assetPath) !== '';
}

function getMantraSubcategoryPhotoSrc(array $row, $defaultImage = 'assets/images/default-image.png')
{
    $title = $row['title'] ?? '';
    $indexId = isset($row['index_id']) ? (int) $row['index_id'] : null;

    $vrathamImage = getMantraVrathamImage($title, $indexId);
    if ($vrathamImage !== '') {
        $resolvedImage = mantraVrathamResolveAssetPath($vrathamImage);
        if ($resolvedImage !== '') {
            return $resolvedImage;
        }
    }

    $photos = trim((string) ($row['photos'] ?? ''));
    if ($photos !== '') {
        if (stripos($photos, 'assets/') === 0) {
            return $photos;
        }

        return 'app/uploads/gods/' . $photos;
    }

    $banner = trim((string) ($row['banner'] ?? ''));
    if ($banner !== '') {
        if (stripos($banner, 'assets/') === 0) {
            return $banner;
        }

        return 'app/uploads/gods/banner/' . $banner;
    }

    return $defaultImage;
}

function getMantraGodCategoryFilters()
{
    static $categories = null;

    if ($categories !== null) {
        return $categories;
    }

    $displayOrder = [
        'GOD & GODDESS' => 'GOD & GODDESS',
        'GURUS' => 'GURUS',
        'PLANATES' => 'NAVAGRAHA',
        'RIVER GODESS' => 'RIVER GODDESS',
    ];

    $grouped = [];
    $csvPath = dirname(__DIR__) . '/data/mantras_vratham_images.csv';

    if (!is_readable($csvPath)) {
        $categories = [];
        return $categories;
    }

    if (($handle = fopen($csvPath, 'r')) === false) {
        $categories = [];
        return $categories;
    }

    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 6) {
            continue;
        }

        $status = strtolower(trim((string) $row[5]));
        if ($status !== 'active') {
            continue;
        }

        $categoryKey = trim((string) $row[3]);
        if (!isset($displayOrder[$categoryKey])) {
            continue;
        }

        $title = trim((string) $row[1]);
        if ($title === '') {
            continue;
        }

        $grouped[$categoryKey][] = [
            'csv_id' => (int) trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0])),
            'title' => $title,
            'title_key' => normalizeMantraTitleKey($title),
        ];
    }

    fclose($handle);

    $categories = [];
    foreach ($displayOrder as $categoryKey => $label) {
        if (empty($grouped[$categoryKey])) {
            continue;
        }

        usort($grouped[$categoryKey], function ($a, $b) {
            return $a['csv_id'] <=> $b['csv_id'];
        });

        $categories[] = [
            'category_key' => $categoryKey,
            'label' => $label,
            'count' => count($grouped[$categoryKey]),
            'gods' => $grouped[$categoryKey],
        ];
    }

    return $categories;
}

function getMantraActiveGodCountFromCsv()
{
    $total = 0;
    foreach (getMantraGodCategoryFilters() as $category) {
        $total += (int) $category['count'];
    }

    return $total;
}
