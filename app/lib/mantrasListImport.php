<?php

function mantraListNormalizeStatus($status)
{
    return strtolower(trim((string) $status)) === 'approved' ? 'approved' : 'unapproved';
}

function mantraListNormalizeAudio($audioPath)
{
    $audioPath = trim((string) $audioPath);
    if ($audioPath === '' || strtoupper($audioPath) === 'NULL') {
        return '';
    }

    $audioPath = str_replace('\\', '/', $audioPath);
    return basename($audioPath);
}

function mantraListLoadGodMap(mysqli $db)
{
    $map = [];
    $result = mysqli_query($db, "SELECT index_id, categories_id, TRIM(title) AS title FROM mantras_subcategory");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $key = strtolower(trim($row['title']));
            $map[$key] = [
                'sub_category' => (int) $row['index_id'],
                'categories_id' => (int) $row['categories_id'],
            ];
        }
    }

    return $map;
}

function mantraListResolveGod($godname, array $godMap)
{
    $key = strtolower(trim((string) $godname));
    return $godMap[$key] ?? null;
}

function importMantrasListFromCsv(mysqli $db, $csvPath, $options = [])
{
    $skipHeader = $options['skip_header'] ?? false;
    $updateExisting = $options['update_existing'] ?? true;

    if (!is_readable($csvPath)) {
        return [
            'success' => false,
            'message' => 'CSV file not found.',
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];
    }

    $godMap = mantraListLoadGodMap($db);
    $handle = fopen($csvPath, 'r');
    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = [];
    $rowNum = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $rowNum++;

        if (count($row) < 7) {
            $skipped++;
            continue;
        }

        $indexId = (int) trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $row[0]));
        $title = trim((string) $row[1]);
        $content = (string) ($row[2] ?? '');
        $status = mantraListNormalizeStatus($row[3] ?? 'unapproved');
        $godname = trim((string) ($row[6] ?? ''));
        $meaning = (string) ($row[7] ?? '');
        $audio = mantraListNormalizeAudio($row[9] ?? '');

        if ($audio === '') {
            $audio = mantraListNormalizeAudio($row[10] ?? '');
        }

        if ($title === '' || $godname === '' || strtoupper($godname) === 'NULL') {
            $skipped++;
            continue;
        }

        $god = mantraListResolveGod($godname, $godMap);
        if ($god === null) {
            $errors[] = "Row $rowNum: God '$godname' not found for '$title'.";
            $skipped++;
            continue;
        }

        $titleEsc = mysqli_real_escape_string($db, $title);
        $contentEsc = mysqli_real_escape_string($db, $content);
        $meaningEsc = mysqli_real_escape_string($db, $meaning);
        $audioEsc = mysqli_real_escape_string($db, $audio);
        $subCategory = (int) $god['sub_category'];
        $categoriesId = (int) $god['categories_id'];

        $exists = false;
        if ($indexId > 0) {
            $check = mysqli_query($db, "SELECT index_id FROM mantras_stotras WHERE index_id = $indexId LIMIT 1");
            $exists = $check && mysqli_num_rows($check) > 0;
        }

        if ($exists && $updateExisting) {
            $sql = "UPDATE mantras_stotras SET
                title = '$titleEsc',
                content = '$contentEsc',
                meaning = '$meaningEsc',
                audio = '$audioEsc',
                sub_category = '$subCategory',
                categories_id = '$categoriesId',
                status = '$status'
                WHERE index_id = $indexId";
            if (mysqli_query($db, $sql)) {
                $updated++;
            } else {
                $errors[] = "Row $rowNum: Update failed - " . mysqli_error($db);
            }
            continue;
        }

        if ($exists) {
            $skipped++;
            continue;
        }

        if ($indexId > 0) {
            $sql = "INSERT INTO mantras_stotras
                (index_id, title, content, meaning, audio, sub_category, categories_id, mantras_title, status)
                VALUES
                ($indexId, '$titleEsc', '$contentEsc', '$meaningEsc', '$audioEsc', '$subCategory', $categoriesId, 0, '$status')";
        } else {
            $sql = "INSERT INTO mantras_stotras
                (title, content, meaning, audio, sub_category, categories_id, mantras_title, status)
                VALUES
                ('$titleEsc', '$contentEsc', '$meaningEsc', '$audioEsc', '$subCategory', $categoriesId, 0, '$status')";
        }

        if (mysqli_query($db, $sql)) {
            $imported++;
        } else {
            $errors[] = "Row $rowNum: Insert failed - " . mysqli_error($db);
        }
    }

    fclose($handle);

    return [
        'success' => true,
        'message' => 'Import completed.',
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors,
    ];
}
