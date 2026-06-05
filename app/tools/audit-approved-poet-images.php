<?php
/**
 * CLI: count approved poets with missing/broken listing images.
 * php app/tools/audit-approved-poet-images.php
 */
require_once dirname(__DIR__) . '/class/databaseConn.php';
require_once dirname(__DIR__, 2) . '/include/saints_media.php';

$db = (new DatabaseConn())->dbLink;
$pageId = saints_poets_page_id($db);
$poetDir = saints_poet_upload_dir();
$approvedSql = saints_public_listing_status_sql();

$r = mysqli_query(
    $db,
    "SELECT index_id, title, photos, status FROM other_page
     WHERE page_id = '" . mysqli_real_escape_string($db, $pageId) . "' $approvedSql
     ORDER BY order_by ASC"
);

$total = 0;
$ok = 0;
$emptyDb = 0;
$genericDb = 0;
$missingFile = 0;
$showsDefault = 0;
$missingSamples = [];

while ($row = mysqli_fetch_assoc($r)) {
    $total++;
    $photos = trim((string) $row['photos']);
    $src = saints_photo_src($photos, $pageId, $db, $row['title']);

    if ($src === SAINTS_DEFAULT_IMAGE) {
        $showsDefault++;
        if ($photos === '') {
            $emptyDb++;
        } elseif (saints_poet_is_generic_filename($photos)) {
            $genericDb++;
        } else {
            $missingFile++;
        }
        if (count($missingSamples) < 25) {
            $missingSamples[] = [
                'order' => $row['index_id'],
                'title' => $row['title'],
                'photos' => $photos,
                'reason' => $photos === '' ? 'empty photos' : (saints_poet_is_generic_filename($photos) ? 'generic filename' : 'file not on disk'),
            ];
        }
    } else {
        $ok++;
    }
}

echo "Page ID: $pageId\n";
echo "Poet upload dir: $poetDir\n";
echo "Dir exists: " . (is_dir($poetDir) ? 'yes' : 'NO') . "\n";
if (is_dir($poetDir)) {
    $fileCount = 0;
    foreach (scandir($poetDir) ?: [] as $e) {
        if ($e !== '.' && $e !== '..' && is_file($poetDir . DIRECTORY_SEPARATOR . $e)) {
            $fileCount++;
        }
    }
    echo "Files in poet folder: $fileCount\n";
}
echo "\nApproved records: $total\n";
echo "  With working image URL: $ok\n";
echo "  Showing default (no image): $showsDefault\n";
echo "    - empty photos column: $emptyDb\n";
echo "    - generic (1.jpg etc): $genericDb\n";
echo "    - photos set but file missing: $missingFile\n";

if ($missingSamples) {
    echo "\nSample missing (" . count($missingSamples) . "):\n";
    foreach ($missingSamples as $s) {
        echo "  [{$s['order']}] {$s['title']} | db=[{$s['photos']}] | {$s['reason']}\n";
    }
}
