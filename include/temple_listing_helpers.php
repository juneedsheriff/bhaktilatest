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

function temples_india_listing_title_display(string $title, ?string $cityName, ?string $stateName): string
{
    $parts = [trim($title)];
    if (!empty($cityName)) {
        $parts[] = trim($cityName);
    }
    if (!empty($stateName)) {
        $parts[] = trim($stateName);
    }

    return htmlspecialchars(implode(', ', array_filter($parts)), ENT_QUOTES, 'UTF-8');
}

function temples_india_listing_html(array $row): string
{
    $indexId = (int) ($row['index_id'] ?? 0);
    $photos = htmlspecialchars((string) ($row['photos'] ?? ''), ENT_QUOTES, 'UTF-8');
    $title = temples_india_listing_title_display(
        (string) ($row['title'] ?? ''),
        $row['city_name'] ?? null,
        $row['state_name'] ?? null
    );

    return "<div class='listing'>
                <a href='temple-details.php?id={$indexId}'>
                    <img src='app/uploads/temple/{$photos}' alt=''>
                </a>
                <div class='listing-details'>
                    <a href='temple-details.php?id={$indexId}'>
                        <div class='listing-title'>{$title}</div>
                    </a>
                    <div class='listing-rating text-dark'><a href='temple-details.php?id={$indexId}'>Read more</a></div>
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

    $listSql = "SELECT t.index_id, t.photos, t.title, c.city_name, s.state_name
        FROM temples t
        LEFT JOIN city c ON c.city_id = t.city
        LEFT JOIN state s ON s.country_code = t.country
            AND (s.state_code = t.state OR CAST(s.state_id AS CHAR) = t.state)
        WHERE " . $clause['where_sql'] . "
        ORDER BY t.order_by ASC";

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
