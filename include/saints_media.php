<?php

/** Fallback category index_id for saints / poets (other_page.page_id). */
const SAINTS_PAGE_ID = '5';

/** Hindu Ashrams category (other_page.page_id). */
const HINDU_ASHRAMS_PAGE_ID = '6';

/** Sacred Trees category (other_page.page_id). */
const SACRED_TREES_PAGE_ID = '7';

/** Sacred Mountains category (other_page.page_id). */
const SACRED_MOUNTAINS_PAGE_ID = '8';

/** Vahana Gods category (other_page.page_id). */
const VAHANA_GODS_PAGE_ID = '9';

/** Alwars category (other_page.page_id). */
const ALWARS_PAGE_ID = '10';

const SAINTS_DEFAULT_IMAGE = 'assets/images/default-image.png';

/**
 * Resolve the category index_id for Saints & Poets from the database.
 */
function saints_poets_page_id(mysqli $db)
{
    static $resolved = null;
    if ($resolved !== null) {
        return $resolved;
    }

    $sql = "SELECT index_id FROM category WHERE index_id != '0'
            AND (name LIKE '%Saints%Poets%' OR name LIKE '%Saint%Poet%')
            LIMIT 1";
    $result = mysqli_query($db, $sql);
    if ($result && ($row = mysqli_fetch_assoc($result))) {
        $resolved = (string) $row['index_id'];
        return $resolved;
    }

    $resolved = SAINTS_PAGE_ID;
    return $resolved;
}

/**
 * SQL fragment: only approved rows for public saints/poets listings.
 */
function saints_public_listing_status_sql($tableAlias = '')
{
    $prefix = $tableAlias !== '' ? $tableAlias . '.' : '';

    return " AND LOWER(TRIM(COALESCE({$prefix}`status`,''))) = 'approved' ";
}

function saints_uses_poet_uploads($pageId, mysqli $db = null)
{
    if ($db === null) {
        return (string) $pageId === SAINTS_PAGE_ID;
    }

    return (string) $pageId === saints_poets_page_id($db);
}

function saints_uses_ashram_uploads($pageId)
{
    return (string) $pageId === HINDU_ASHRAMS_PAGE_ID;
}

function saints_uses_tree_uploads($pageId)
{
    return (string) $pageId === SACRED_TREES_PAGE_ID;
}

function saints_uses_mountain_uploads($pageId)
{
    return (string) $pageId === SACRED_MOUNTAINS_PAGE_ID;
}

function saints_uses_vahana_uploads($pageId)
{
    return (string) $pageId === VAHANA_GODS_PAGE_ID;
}

function saints_uses_alwar_uploads($pageId)
{
    return (string) $pageId === ALWARS_PAGE_ID;
}

/**
 * @return array{prefix: string, prefixes: string[], dir: string, url_prefix: string}|null
 */
function saints_category_upload_config($pageId)
{
    if (saints_uses_ashram_uploads($pageId)) {
        return [
            'prefix' => 'ashram/',
            'prefixes' => ['ashram/'],
            'dir' => 'ashram',
            'url_prefix' => 'app/uploads/ashram/',
        ];
    }
    if (saints_uses_tree_uploads($pageId)) {
        return [
            'prefix' => 'tree/',
            'prefixes' => ['tree/'],
            'dir' => 'tree',
            'url_prefix' => 'app/uploads/tree/',
        ];
    }
    if (saints_uses_mountain_uploads($pageId)) {
        return [
            'prefix' => 'mountain/',
            'prefixes' => ['mountain/'],
            'dir' => 'mountain',
            'url_prefix' => 'app/uploads/mountain/',
        ];
    }
    if (saints_uses_vahana_uploads($pageId)) {
        return [
            'prefix' => 'vahana/',
            'prefixes' => ['vahana/', 'vanaha/'],
            'dir' => 'vahana',
            'url_prefix' => 'app/uploads/vahana/',
        ];
    }
    if (saints_uses_alwar_uploads($pageId)) {
        return [
            'prefix' => 'alwar/',
            'prefixes' => ['alwar/'],
            'dir' => 'alwar',
            'url_prefix' => 'app/uploads/alwar/',
        ];
    }

    return null;
}

function saints_category_upload_dir($pageId)
{
    $config = saints_category_upload_config($pageId);

    return $config ? dirname(__DIR__) . '/app/uploads/' . $config['dir'] : '';
}

function saints_category_relative_path($path, $pageId)
{
    $path = trim(str_replace('\\', '/', (string) $path));
    $path = ltrim($path, '/');
    $config = saints_category_upload_config($pageId);
    if ($config === null || $path === '') {
        return '';
    }
    $bare = rtrim($config['dir'], '/');
    if ($path === $bare) {
        return '';
    }
    $prefixes = $config['prefixes'] ?? [$config['prefix']];
    foreach ($prefixes as $prefix) {
        if (strpos($path, $prefix) === 0) {
            $path = substr($path, strlen($prefix));
            break;
        }
    }

    return trim($path, '/');
}

function saints_category_file_url($relativePath, $pageId)
{
    $config = saints_category_upload_config($pageId);
    if ($config === null) {
        return '';
    }
    $relativePath = saints_category_relative_path($relativePath, $pageId);
    if ($relativePath === '') {
        return '';
    }
    $parts = array_filter(explode('/', $relativePath), static function ($p) {
        return $p !== '';
    });

    return $config['url_prefix'] . implode('/', array_map('rawurlencode', $parts));
}

function saints_category_resolve_disk_path($path, $pageId, $uploadDir = null)
{
    $relative = saints_category_relative_path($path, $pageId);
    if ($relative === '' || saints_category_upload_config($pageId) === null) {
        return null;
    }

    $uploadDir = $uploadDir ?? saints_category_upload_dir($pageId);
    if (!is_dir($uploadDir)) {
        return null;
    }

    $basename = saints_poet_normalize_filename(basename($relative));
    $candidates = [$relative, $basename];
    $nbspVariant = str_replace(' ', "\xC2\xA0", $basename);
    if ($nbspVariant !== $basename) {
        $candidates[] = $nbspVariant;
    }

    foreach (array_unique($candidates) as $candidate) {
        $full = $uploadDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate);
        if (is_file($full)) {
            return str_replace('\\', '/', $candidate);
        }
    }

    $rawBase = trim(basename($relative));
    if ($rawBase !== '') {
        foreach (scandir($uploadDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $full = $uploadDir . DIRECTORY_SEPARATOR . $entry;
            if (!is_file($full)) {
                continue;
            }
            if (strcasecmp($entry, $rawBase) === 0) {
                return str_replace('\\', '/', $entry);
            }
            $entryStem = pathinfo($entry, PATHINFO_FILENAME);
            $baseStem = pathinfo($rawBase, PATHINFO_FILENAME);
            if ($entryStem !== '' && strcasecmp($entryStem, $baseStem) === 0) {
                return str_replace('\\', '/', $entry);
            }
        }
    }

    return null;
}

function saints_category_photo_src($photos, $pageId)
{
    $resolved = saints_category_resolve_disk_path($photos, $pageId);
    if ($resolved !== null) {
        return saints_category_file_url($resolved, $pageId);
    }
    $legacy = ltrim((string) $photos, '/');
    if ($legacy !== '') {
        $legacyPath = dirname(__DIR__) . '/app/uploads/others/' . $legacy;
        if (is_file($legacyPath)) {
            return 'app/uploads/others/' . $legacy;
        }
    }

    return SAINTS_DEFAULT_IMAGE;
}

function saints_category_banner_src($banner, $pageId)
{
    $resolved = saints_category_resolve_disk_path($banner, $pageId);
    if ($resolved !== null) {
        return saints_category_file_url($resolved, $pageId);
    }
    $legacy = ltrim((string) $banner, '/');
    if ($legacy !== '') {
        foreach (['banner/', ''] as $sub) {
            $legacyPath = dirname(__DIR__) . '/app/uploads/others/' . $sub . $legacy;
            if (is_file($legacyPath)) {
                return 'app/uploads/others/' . $sub . $legacy;
            }
        }
    }

    return SAINTS_DEFAULT_IMAGE;
}

/**
 * Absolute path to Sacred Trees uploads (app/uploads/tree).
 */
function saints_tree_upload_dir()
{
    return dirname(__DIR__) . '/app/uploads/tree';
}

/**
 * Normalize DB path for Sacred Trees (tree/Peepal.PNG → Peepal.PNG).
 */
function saints_tree_relative_path($path)
{
    $path = trim(str_replace('\\', '/', (string) $path));
    $path = ltrim($path, '/');
    if ($path === '' || $path === 'tree') {
        return '';
    }
    if (strpos($path, 'tree/') === 0) {
        $path = substr($path, 5);
    }

    return trim($path, '/');
}

/**
 * Build URL under app/uploads/tree/ (encodes each path segment).
 */
function saints_tree_file_url($relativePath)
{
    $relativePath = saints_tree_relative_path($relativePath);
    if ($relativePath === '') {
        return '';
    }
    $parts = array_filter(explode('/', $relativePath), static function ($p) {
        return $p !== '';
    });

    return 'app/uploads/tree/' . implode('/', array_map('rawurlencode', $parts));
}

/**
 * Resolve tree image on disk (handles NBSP vs space in filenames).
 *
 * @return string|null Path relative to tree upload dir
 */
function saints_tree_resolve_disk_path($path, $treeDir = null)
{
    $relative = saints_tree_relative_path($path);
    if ($relative === '') {
        return null;
    }

    $treeDir = $treeDir ?? saints_tree_upload_dir();
    if (!is_dir($treeDir)) {
        return null;
    }

    $basename = saints_poet_normalize_filename(basename($relative));
    $candidates = [$relative, $basename];
    $nbspVariant = str_replace(' ', "\xC2\xA0", $basename);
    if ($nbspVariant !== $basename) {
        $candidates[] = $nbspVariant;
    }

    foreach (array_unique($candidates) as $candidate) {
        $full = $treeDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $candidate);
        if (is_file($full)) {
            return str_replace('\\', '/', $candidate);
        }
    }

    $rawBase = trim(basename($relative));
    if ($rawBase !== '') {
        foreach (scandir($treeDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (strcasecmp($entry, $rawBase) === 0 && is_file($treeDir . DIRECTORY_SEPARATOR . $entry)) {
                return str_replace('\\', '/', $entry);
            }
        }
    }

    return null;
}

/**
 * Normalize whitespace (including NBSP) in a filename.
 */
function saints_poet_normalize_filename($name)
{
    $name = (string) $name;
    $name = str_replace(["\xC2\xA0", "\xE2\x80\xAF"], ' ', $name);
    $name = preg_replace('/\s+/u', ' ', $name);

    return trim($name);
}

/**
 * Normalize a CSV or DB image path to a bare filename.
 */
function saints_poet_filename($path)
{
    $path = trim((string) $path);
    if ($path === '' || $path === 'poet/' || $path === 'poet/uploadedimage/') {
        return '';
    }
    $path = str_replace('\\', '/', $path);
    if (strpos($path, 'poet/') === 0) {
        $path = substr($path, 5);
    }
    $path = trim($path, '/');
    if ($path === '' || $path === 'uploadedimage') {
        return '';
    }

    return saints_poet_normalize_filename(basename($path));
}

/**
 * Bare filename from DB/CSV without changing NBSP (for disk lookup).
 */
function saints_poet_raw_basename($path)
{
    $path = trim((string) $path);
    if ($path === '' || $path === 'poet/' || $path === 'poet/uploadedimage/') {
        return '';
    }
    $path = str_replace('\\', '/', $path);
    if (strpos($path, 'poet/') === 0) {
        $path = substr($path, 5);
    }
    $path = trim($path, '/');
    if ($path === '' || $path === 'uploadedimage') {
        return '';
    }

    return trim(basename($path));
}

/**
 * Placeholder filenames from bad imports (e.g. 1.jpg, 2.png) — not real saint photos.
 */
function saints_poet_is_generic_filename($filename)
{
    $filename = saints_poet_raw_basename($filename);
    if ($filename === '') {
        $filename = saints_poet_filename($filename);
    }
    if ($filename === '') {
        return true;
    }

    return (bool) preg_match('/^\d+\.(jpe?g|png|gif|webp|jfif)$/i', $filename);
}

/**
 * Absolute path to poet uploads directory.
 */
function saints_poet_upload_dir()
{
    return dirname(__DIR__) . '/app/uploads/poet';
}

/**
 * Build URL-safe path under app/uploads/poet/ (encodes each path segment).
 */
function saints_poet_file_url($relativePath)
{
    $relativePath = str_replace('\\', '/', $relativePath);
    $parts = array_filter(explode('/', $relativePath), static function ($p) {
        return $p !== '';
    });

    return 'app/uploads/poet/' . implode('/', array_map('rawurlencode', $parts));
}

/**
 * Find an existing file on disk for a DB photos value (handles NBSP vs space).
 *
 * @return string|null Actual filename relative to poet dir, or null if not found
 */
function saints_poet_resolve_disk_path($photos, $poetDir = null, $title = '')
{
    if (saints_poet_is_generic_filename($photos)) {
        $photos = '';
    }

    $poetDir = $poetDir ?? saints_poet_upload_dir();
    if (!is_dir($poetDir)) {
        return null;
    }

    $raw = saints_poet_raw_basename($photos);
    $name = saints_poet_filename($photos);
    $candidates = [];
    foreach ([$raw, $name] as $candidate) {
        if ($candidate !== '' && !in_array($candidate, $candidates, true)) {
            $candidates[] = $candidate;
        }
    }
    if ($name !== '') {
        $nbspAll = str_replace(' ', "\xC2\xA0", $name);
        if ($nbspAll !== $name && !in_array($nbspAll, $candidates, true)) {
            $candidates[] = $nbspAll;
        }
    }

    foreach ($candidates as $candidate) {
        $full = $poetDir . DIRECTORY_SEPARATOR . $candidate;
        if (is_file($full)) {
            return str_replace('\\', '/', $candidate);
        }
    }

    if ($raw !== '') {
        foreach (scandir($poetDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (strcasecmp($entry, $raw) === 0 && is_file($poetDir . DIRECTORY_SEPARATOR . $entry)) {
                return str_replace('\\', '/', $entry);
            }
        }
    }

    if ($title !== '') {
        return saints_poet_resolve_by_title($title, $poetDir);
    }

    return null;
}

/**
 * Loose key for matching titles / filenames (letters and digits only, lowercase).
 */
function saints_poet_title_key($text)
{
    $text = saints_poet_normalize_filename($text);
    $text = preg_replace('/[^a-z0-9]+/i', '', $text);
    $text = strtolower($text);
    $text = str_replace('ramadas', 'ramdas', $text);

    return $text;
}

/**
 * Find an image file from saint/poet title when photos column is empty or wrong.
 */
function saints_poet_resolve_by_title($title, $poetDir = null)
{
    $poetDir = $poetDir ?? saints_poet_upload_dir();
    if (!is_dir($poetDir)) {
        return null;
    }

    $titleKey = saints_poet_title_key($title);
    if ($titleKey === '') {
        return null;
    }

    $best = null;
    foreach (scandir($poetDir) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $full = $poetDir . DIRECTORY_SEPARATOR . $entry;
        if (!is_file($full) || !preg_match('/\.(jpe?g|png|gif|webp|jfif)$/i', $entry)) {
            continue;
        }
        if (saints_poet_is_generic_filename($entry)) {
            continue;
        }
        $fileKey = saints_poet_title_key(pathinfo($entry, PATHINFO_FILENAME));
        $fileStem = preg_replace('/\d+$/', '', $fileKey);
        if ($fileKey === $titleKey || $fileStem === $titleKey || strpos($fileKey, $titleKey) === 0 || strpos($titleKey, $fileStem) === 0) {
            if ($best === null || preg_match('/\s1$/', pathinfo($entry, PATHINFO_FILENAME))) {
                $best = str_replace('\\', '/', $entry);
            }
        }
    }

    return $best;
}

/**
 * Public URL for a listing/card photo (Saints & Poets → app/uploads/poet/).
 */
function saints_photo_src($photos, $pageId, mysqli $db = null, $title = '')
{
    if (saints_category_upload_config($pageId) !== null) {
        return saints_category_photo_src($photos, $pageId);
    }

    if (!saints_uses_poet_uploads($pageId, $db)) {
        $photos = ltrim((string) $photos, '/');
        if ($photos === '') {
            return SAINTS_DEFAULT_IMAGE;
        }
        $path = dirname(__DIR__) . '/app/uploads/others/' . $photos;

        return is_file($path) ? 'app/uploads/others/' . $photos : SAINTS_DEFAULT_IMAGE;
    }

    $resolved = saints_poet_resolve_disk_path($photos, null, $title);
    if ($resolved === null) {
        return SAINTS_DEFAULT_IMAGE;
    }

    return saints_poet_file_url($resolved);
}

/**
 * Public URL for a detail banner (Saints & Poets → app/uploads/poet/).
 */
function saints_banner_src($banner, $pageId, mysqli $db = null, $title = '')
{
    if (saints_category_upload_config($pageId) !== null) {
        return saints_category_banner_src($banner, $pageId);
    }

    if (!saints_uses_poet_uploads($pageId, $db)) {
        $banner = ltrim((string) $banner, '/');
        if ($banner === '') {
            return SAINTS_DEFAULT_IMAGE;
        }
        $path = dirname(__DIR__) . '/app/uploads/others/banner/' . $banner;

        return is_file($path) ? 'app/uploads/others/banner/' . $banner : SAINTS_DEFAULT_IMAGE;
    }

    $resolved = saints_poet_resolve_disk_path($banner, null, $title);
    if ($resolved === null) {
        return SAINTS_DEFAULT_IMAGE;
    }

    return saints_poet_file_url($resolved);
}
