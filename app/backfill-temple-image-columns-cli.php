<?php
/**
 * CLI: php backfill-temple-image-columns-cli.php
 * Backfills image1..image10 from photos + gallery_image.
 */

require_once __DIR__ . '/class/databaseConn.php';

$db = (new DatabaseConn())->dbLink;
if (!$db) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

$res = mysqli_query($db, "SELECT index_id, photos, gallery_image, image1, image2, image3, image4, image5, image6, image7, image8, image9, image10 FROM temples");
if (!$res) {
    fwrite(STDERR, "Query failed: " . mysqli_error($db) . PHP_EOL);
    exit(1);
}

$updated = 0;
while ($row = mysqli_fetch_assoc($res)) {
    $images = [];
    $photos = trim((string) ($row['photos'] ?? ''));
    if ($photos !== '') {
        $images[] = $photos;
    }
    $gallery = trim((string) ($row['gallery_image'] ?? ''));
    if ($gallery !== '') {
        foreach (explode(',', $gallery) as $img) {
            $img = trim((string) $img);
            if ($img !== '' && !in_array($img, $images, true)) {
                $images[] = $img;
            }
        }
    }

    if (empty($images)) {
        continue;
    }

    $newValues = [];
    $hasChange = false;
    for ($i = 1; $i <= 10; $i++) {
        $field = 'image' . $i;
        $current = trim((string) ($row[$field] ?? ''));
        $proposed = $current !== '' ? $current : ($images[$i - 1] ?? '');
        $newValues[$field] = $proposed;
        if ($proposed !== $current) {
            $hasChange = true;
        }
    }

    if (!$hasChange) {
        continue;
    }

    $sets = [];
    foreach ($newValues as $field => $value) {
        $sets[] = "`$field`='" . mysqli_real_escape_string($db, $value) . "'";
    }
    $id = (int) ($row['index_id'] ?? 0);
    if ($id <= 0) {
        continue;
    }
    $sql = "UPDATE temples SET " . implode(', ', $sets) . " WHERE index_id = $id";
    if (mysqli_query($db, $sql)) {
        $updated++;
    }
}

echo "Backfilled rows: {$updated}" . PHP_EOL;
