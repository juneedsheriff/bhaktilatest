<?php

function normalizeMantraTitleName($title)
{
    $title = trim((string) $title);
    $title = preg_replace('/\s+/u', ' ', $title);
    $title = preg_replace('/[^\P{C}\n]+/u', '', $title);

    return trim($title);
}

function loadExistingMantraTitles(mysqli $db)
{
    $titles = [];
    $result = mysqli_query($db, "SELECT TRIM(title) AS title FROM mantras_title WHERE index_id != '0'");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = strtolower(normalizeMantraTitleName($row['title']));
            if ($key !== '') {
                $titles[$key] = true;
            }
        }
    }

    return $titles;
}

function importMantrasTitleFromCsv(mysqli $db, $csvPath)
{
    if (!is_readable($csvPath)) {
        return [
            'success' => false,
            'message' => 'CSV file not found.',
            'imported' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
    }

    $existing = loadExistingMantraTitles($db);
    $handle = fopen($csvPath, 'r');
    $imported = 0;
    $skipped = 0;
    $errors = [];
    $rowNum = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $rowNum++;
        $title = normalizeMantraTitleName($row[0] ?? '');

        if ($title === '') {
            $skipped++;
            continue;
        }

        $key = strtolower($title);
        if (isset($existing[$key])) {
            $skipped++;
            continue;
        }

        $titleEsc = mysqli_real_escape_string($db, $title);
        $sql = "INSERT INTO mantras_title (title) VALUES ('$titleEsc')";

        if (mysqli_query($db, $sql)) {
            $existing[$key] = true;
            $imported++;
        } else {
            $errors[] = "Row $rowNum: Insert failed for '$title' - " . mysqli_error($db);
        }
    }

    fclose($handle);

    return [
        'success' => true,
        'message' => 'Import completed.',
        'imported' => $imported,
        'skipped' => $skipped,
        'errors' => $errors,
    ];
}

function loadMantrasTitleCsvOrder()
{
    static $titles = null;

    if ($titles !== null) {
        return $titles;
    }

    $titles = [];
    $csvPath = dirname(__DIR__) . '/data/mantras_title_list.csv';

    if (!is_readable($csvPath)) {
        return $titles;
    }

    $handle = fopen($csvPath, 'r');
    while (($row = fgetcsv($handle)) !== false) {
        $title = normalizeMantraTitleName($row[0] ?? '');
        if ($title !== '') {
            $titles[] = $title;
        }
    }
    fclose($handle);

    return $titles;
}

function getMantrasTitleFilterList(mysqli $db)
{
    $dbTitles = [];
    $result = mysqli_query($db, "SELECT index_id, TRIM(title) AS title FROM mantras_title WHERE index_id != '0' ORDER BY title ASC");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = strtolower(normalizeMantraTitleName($row['title']));
            if ($key !== '') {
                $dbTitles[$key] = [
                    'index_id' => (int) $row['index_id'],
                    'title' => $row['title'],
                ];
            }
        }
    }

    $filterList = [];
    $used = [];

    foreach (loadMantrasTitleCsvOrder() as $csvTitle) {
        $key = strtolower($csvTitle);
        if (isset($dbTitles[$key])) {
            $filterList[] = $dbTitles[$key];
            $used[$key] = true;
        }
    }

    foreach ($dbTitles as $key => $row) {
        if (!isset($used[$key])) {
            $filterList[] = $row;
        }
    }

    return $filterList;
}

function resolveMantraTitleKeyword(mysqli $db, $titleRef)
{
    $titleRef = trim((string) $titleRef);
    if ($titleRef === '') {
        return '';
    }

    if (ctype_digit($titleRef)) {
        $titleId = (int) $titleRef;
        $result = mysqli_query($db, "SELECT TRIM(title) AS title FROM mantras_title WHERE index_id = $titleId LIMIT 1");
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return normalizeMantraTitleName($row['title']);
        }
    }

    return normalizeMantraTitleName($titleRef);
}
