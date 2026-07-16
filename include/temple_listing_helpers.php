<?php

/**
 * Build WHERE clause for India temple god/temple checkbox filters.
 *
 * @return array{where_sql: string, params: array<int|string>, types: string}|null
 */
function temples_india_god_temple_filter_clause(array $godIds, array $templeIds, string $country = 'IN'): ?array
{
    $godIds = array_values(array_filter(array_map('intval', $godIds)));
    $templeIds = array_values(array_filter(array_map('intval', $templeIds)));

    if (empty($godIds) && empty($templeIds)) {
        return null;
    }

    $params = [];
    $types = '';
    $filterParts = [];

    if ($country !== '') {
        $where = "t.status = 'approved' AND t.country = ?";
        $params[] = $country;
        $types .= 's';
    } else {
        $where = "t.status = 'approved'";
    }

    if (!empty($godIds)) {
        $placeholders = implode(',', array_fill(0, count($godIds), '?'));
        $filterParts[] = "t.god_id IN ($placeholders)";
        foreach ($godIds as $godId) {
            $params[] = $godId;
            $types .= 'i';
        }
    }

    if (!empty($templeIds)) {
        $placeholders = implode(',', array_fill(0, count($templeIds), '?'));
        $filterParts[] = "t.index_id IN ($placeholders)";
        foreach ($templeIds as $templeId) {
            $params[] = $templeId;
            $types .= 'i';
        }
    }

    $where .= ' AND (' . implode(' OR ', $filterParts) . ')';

    return [
        'where_sql' => $where,
        'params' => $params,
        'types' => $types,
    ];
}

function temples_india_place_label(array $row): string
{
    $place = trim((string) ($row['temple_place'] ?? ''));
    if ($place !== '') {
        return $place;
    }

    return trim((string) ($row['city_name'] ?? ''));
}

function temples_india_listing_thumbnail_filename(array $row): string
{
    $normalize = static function ($path): string {
        $path = trim(str_replace('\\', '/', (string) $path));
        if ($path === '') {
            return '';
        }
        $parts = array_values(array_filter(explode('/', $path), static function ($part) {
            return $part !== '' && strtolower($part) !== 'uploadedimage';
        }));
        if (empty($parts)) {
            return '';
        }

        $filename = basename(implode('/', $parts));
        if (strtolower($filename) === 'uploadedimage') {
            return '';
        }

        return $filename;
    };

    $image2 = $normalize($row['image2'] ?? '');
    if ($image2 !== '') {
        return $image2;
    }

    $image1 = $normalize($row['image1'] ?? '');
    if ($image1 !== '') {
        return $image1;
    }

    return $normalize($row['photos'] ?? '');
}

function temples_india_listing_thumbnail_src(array $row): string
{
    $filename = temples_india_listing_thumbnail_filename($row);
    if ($filename === '') {
        return '';
    }

    return 'app/uploads/temple/' . $filename;
}

function temples_india_listing_thumbnail_attrs(array $row, string $alt = ''): string
{
    $src = htmlspecialchars(temples_india_listing_thumbnail_src($row), ENT_QUOTES, 'UTF-8');
    $altAttr = htmlspecialchars($alt !== '' ? $alt : (string) ($row['title'] ?? ''), ENT_QUOTES, 'UTF-8');

    return 'src="' . $src . '" alt="' . $altAttr . '" loading="lazy"';
}

function temples_india_listing_place_line(?string $placeName, ?string $stateName): string
{
    $parts = [];
    $place = trim((string) $placeName);
    $state = trim((string) $stateName);
    if ($place !== '') {
        $parts[] = $place;
    }
    if ($state !== '') {
        $parts[] = $state;
    }
    if ($parts === []) {
        return '';
    }

    return htmlspecialchars(implode(', ', $parts), ENT_QUOTES, 'UTF-8');
}

function temples_india_listing_details_inner_html(string $title, ?string $placeName = null, ?string $stateName = null): string
{
    $titleHtml = htmlspecialchars(trim($title), ENT_QUOTES, 'UTF-8');
    $placeHtml = temples_india_listing_place_line($placeName, $stateName);
    $html = "<div class='listing-title'>{$titleHtml}</div>";
    if ($placeHtml !== '') {
        $html .= "<div class='listing-place'>{$placeHtml}</div>";
    }

    return $html;
}

function temples_india_listing_html(array $row): string
{
    $indexId = (int) ($row['index_id'] ?? 0);
    $thumbAttrs = temples_india_listing_thumbnail_attrs($row);
    $details = temples_india_listing_details_inner_html(
        (string) ($row['title'] ?? ''),
        temples_india_place_label($row),
        $row['state_name'] ?? null
    );

    return "<div class='listing'>
                <a href='temple-details.php?id={$indexId}'>
                    <img {$thumbAttrs}>
                </a>
                <div class='listing-details'>
                    <a href='temple-details.php?id={$indexId}'>
                        {$details}
                    </a>
                </div>
              </div>";
}

/**
 * Fetch filtered India temple listings using a single JOIN query (no per-row lookups).
 *
 * @return array{html: string, total: int}
 */
function temples_india_fetch_god_temple_listings(mysqli $link, array $godIds, array $templeIds, string $country = 'IN', ?int $limit = null, int $offset = 0): array
{
    $clause = temples_india_god_temple_filter_clause($godIds, $templeIds, $country);
    if ($clause === null) {
        return ['html' => '', 'total' => 0];
    }

    $params = $clause['params'];
    $types = $clause['types'];

    $countSql = 'SELECT COUNT(*) AS total FROM temples t WHERE ' . $clause['where_sql'];
    $countStmt = $link->prepare($countSql);
    if (!$countStmt) {
        return ['html' => '<p>No listings found.</p>', 'total' => 0];
    }
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = (int) (($countResult->fetch_assoc()['total'] ?? 0));
    $countStmt->close();

    if ($total === 0) {
        return ['html' => '<p>No listings found.</p>', 'total' => 0];
    }

    $listSql = "SELECT t.index_id, t.photos, t.image1, t.image2, t.title, t.temple_place, c.city_name, s.state_name
        FROM temples t
        LEFT JOIN city c ON c.city_id = t.city
        LEFT JOIN state s ON s.country_code = t.country
            AND (s.state_code = t.state OR CAST(s.state_id AS CHAR) = t.state)
        WHERE " . $clause['where_sql'] . "
        ORDER BY t.index_id ASC";

    $listParams = $params;
    $listTypes = $types;

    if ($limit !== null) {
        $listSql .= ' LIMIT ?, ?';
        $listParams[] = $offset;
        $listParams[] = $limit;
        $listTypes .= 'ii';
    }

    $listStmt = $link->prepare($listSql);
    if (!$listStmt) {
        return ['html' => '<p>No listings found.</p>', 'total' => $total];
    }
    $listStmt->bind_param($listTypes, ...$listParams);
    $listStmt->execute();
    $result = $listStmt->get_result();

    $html = '';
    while ($row = $result->fetch_assoc()) {
        $html .= temples_india_listing_html($row);
    }
    $listStmt->close();

    if ($html === '') {
        $html = '<p>No listings found.</p>';
    }

    return ['html' => $html, 'total' => $total];
}
