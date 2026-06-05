<?php

/**
 * Import legacy other_page CSV (9 columns, no header) into `other_page`.
 *
 * Columns: legacy_id, title, subtitle, content, photos, banner, status, created_at, author
 */
function other_page_import_normalize_title($title)
{
    $title = trim(preg_replace('/\s+/u', ' ', (string) $title));

    return mb_strtolower($title, 'UTF-8');
}

function other_page_import_map_status($raw)
{
    $status = strtolower(trim((string) $raw));

    return $status === 'approved' ? 'approved' : 'unapproved';
}

function other_page_import_media_path($path)
{
    $path = trim(str_replace('\\', '/', (string) $path));
    if ($path === '' || preg_match('#^(tree|mountain|vahana|vanaha|alwar|ashram)/?$#', $path)) {
        return '';
    }
    if (strpos($path, 'vanaha/') === 0) {
        $path = 'vahana/' . substr($path, 7);
    }

    return mb_substr($path, 0, 100);
}

/** 15-column Alwar export: status in column 10, photos/banner in 4 and 6. */
function other_page_import_is_alwar_row(array $row)
{
    return count($row) >= 11
        && preg_match('/^(approved|pending|unapproved)$/i', trim((string) ($row[10] ?? '')));
}

/** 9-column Ashram export: col2 NULL, content in col3, status in col6. */
function other_page_import_is_ashram_row(array $row)
{
    if (count($row) < 7 || count($row) >= 11) {
        return false;
    }
    $col2 = strtolower(trim((string) ($row[2] ?? '')));
    $col3 = trim((string) ($row[3] ?? ''));
    $statusCol = strtolower(trim((string) ($row[6] ?? '')));
    if (!preg_match('/^(approved|pending|unapproved)$/i', $statusCol)) {
        return false;
    }
    if ($col2 !== 'null' && $col2 !== '') {
        return false;
    }

    return stripos($col3, '<') !== false || strlen($col3) > 80;
}

/**
 * @return array{legacy_id: string, title: string, subtitle: string, content: string, photos: string, banner: string, status: string}
 */
function other_page_import_parse_row(array $row)
{
    if (other_page_import_is_alwar_row($row)) {
        $content = other_page_import_sanitize_text($row[2] ?? '');
        $extra = other_page_import_sanitize_text($row[11] ?? '');
        if ($extra !== '' && stripos($content, $extra) === false) {
            $content .= $extra;
        }

        return [
            'legacy_id' => other_page_import_sanitize_text($row[0] ?? '0'),
            'title' => other_page_import_trim_field(other_page_import_sanitize_text($row[1] ?? ''), 100),
            'subtitle' => '',
            'content' => $content,
            'photos' => other_page_import_media_path($row[3] ?? ''),
            'banner' => other_page_import_media_path($row[6] ?? ''),
            'status' => other_page_import_map_status($row[10] ?? ''),
        ];
    }

    if (other_page_import_is_ashram_row($row)) {
        return [
            'legacy_id' => other_page_import_sanitize_text($row[0] ?? '0'),
            'title' => other_page_import_trim_field(other_page_import_sanitize_text($row[1] ?? ''), 100),
            'subtitle' => '',
            'content' => other_page_import_sanitize_text($row[3] ?? ''),
            'photos' => other_page_import_media_path($row[4] ?? ''),
            'banner' => other_page_import_media_path($row[5] ?? ''),
            'status' => other_page_import_map_status($row[6] ?? ''),
        ];
    }

    return [
        'legacy_id' => other_page_import_sanitize_text($row[0] ?? '0'),
        'title' => other_page_import_trim_field(other_page_import_sanitize_text($row[1] ?? ''), 100),
        'subtitle' => other_page_import_sanitize_text($row[2] ?? ''),
        'content' => other_page_import_sanitize_text($row[3] ?? ''),
        'photos' => other_page_import_media_path($row[4] ?? ''),
        'banner' => other_page_import_media_path($row[5] ?? ''),
        'status' => other_page_import_map_status($row[6] ?? ''),
    ];
}

/** Strip UTF-8 BOM and 4-byte chars (for utf8mb3 columns). */
function other_page_import_sanitize_text($value)
{
    $value = (string) $value;
    $value = preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value;

    return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $value) ?? $value;
}

function other_page_import_trim_field($value, $maxLen)
{
    $value = trim((string) $value);

    return mb_substr($value, 0, $maxLen, 'UTF-8');
}

function &other_page_import_title_cache_ref(mysqli $db, $pageId)
{
    static $cacheByPage = [];
    $pageId = (string) $pageId;
    if (!isset($cacheByPage[$pageId])) {
        $cacheByPage[$pageId] = [];
        $pageEsc = mysqli_real_escape_string($db, $pageId);
        $result = mysqli_query($db, "SELECT index_id, title FROM other_page WHERE page_id = '$pageEsc'");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $key = other_page_import_normalize_title($row['title']);
                $cacheByPage[$pageId][$key] = (int) $row['index_id'];
            }
        }
    }

    return $cacheByPage[$pageId];
}

function other_page_import_find_id_by_title(mysqli $db, $pageId, $titleKey)
{
    $cache = &other_page_import_title_cache_ref($db, $pageId);

    return $cache[$titleKey] ?? null;
}

function other_page_import_remember_title(mysqli $db, $pageId, $titleKey, $indexId)
{
    $cache = &other_page_import_title_cache_ref($db, $pageId);
    $cache[$titleKey] = (int) $indexId;
}

function other_page_import_from_file(mysqli $db, $csvPath, $pageId = '7')
{
    $summary = [
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => [],
    ];

    if (!is_readable($csvPath)) {
        $summary['errors']++;
        $summary['messages'][] = 'CSV not readable: ' . $csvPath;

        return $summary;
    }

    mysqli_set_charset($db, 'utf8');

    $handle = fopen($csvPath, 'rb');
    if ($handle === false) {
        $summary['errors']++;
        $summary['messages'][] = 'Failed to open CSV.';

        return $summary;
    }

    $insertStmt = $db->prepare(
        'INSERT INTO other_page (title, content, page_id, order_by, status, photos, banner)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $updateStmt = $db->prepare(
        'UPDATE other_page SET title = ?, content = ?, page_id = ?, order_by = ?, status = ?, photos = ?, banner = ?
         WHERE index_id = ?'
    );

    if (!$insertStmt || !$updateStmt) {
        fclose($handle);
        $summary['errors']++;
        $summary['messages'][] = 'Prepare failed: ' . mysqli_error($db);

        return $summary;
    }

    $lineNo = 0;
    while (($row = fgetcsv($handle)) !== false) {
        $lineNo++;
        if (count($row) < 7) {
            $summary['skipped']++;
            $summary['messages'][] = "Line $lineNo: too few columns, skipped.";

            continue;
        }

        $parsed = other_page_import_parse_row($row);
        $legacyId = (int) trim($parsed['legacy_id']);
        $title = $parsed['title'];
        $subtitle = $parsed['subtitle'];
        $content = $parsed['content'];
        $photos = $parsed['photos'];
        $banner = $parsed['banner'];
        $status = $parsed['status'];

        if ($title === '') {
            $summary['skipped']++;

            continue;
        }

        if ($subtitle !== '' && stripos($content, $subtitle) === false) {
            $subtitleEsc = htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8');
            $content = '<p><strong>' . $subtitleEsc . '</strong></p>' . $content;
        }

        $orderBy = $legacyId > 0 ? $legacyId : $lineNo;
        $titleKey = other_page_import_normalize_title($title);
        $existingId = other_page_import_find_id_by_title($db, $pageId, $titleKey);

        if ($existingId !== null) {
            $updateStmt->bind_param(
                'sssisssi',
                $title,
                $content,
                $pageId,
                $orderBy,
                $status,
                $photos,
                $banner,
                $existingId
            );
            if ($updateStmt->execute()) {
                $summary['updated']++;
            } else {
                $summary['errors']++;
                $summary['messages'][] = "Line $lineNo update failed ($title): " . $updateStmt->error;
            }
        } else {
            $insertStmt->bind_param(
                'sssisss',
                $title,
                $content,
                $pageId,
                $orderBy,
                $status,
                $photos,
                $banner
            );
            if ($insertStmt->execute()) {
                $summary['imported']++;
                other_page_import_remember_title($db, $pageId, $titleKey, (int) mysqli_insert_id($db));
            } else {
                $summary['errors']++;
                $summary['messages'][] = "Line $lineNo insert failed ($title): " . $insertStmt->error;
            }
        }
    }

    fclose($handle);
    $insertStmt->close();
    $updateStmt->close();

    return $summary;
}
