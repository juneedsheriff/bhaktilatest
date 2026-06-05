<?php
/**
 * Remove pre-import mountain duplicates (page_id=8) kept when CSV titles differ by accents.
 */
require_once __DIR__ . '/../class/databaseConn.php';

$removeIds = [51, 53, 54]; // Ahkkå, Ba Vi mountain range, Ceahläu Massif

$db = new DatabaseConn();
mysqli_set_charset($db->dbLink, 'utf8');

foreach ($removeIds as $id) {
    $id = (int) $id;
    $check = mysqli_query($db->dbLink, "SELECT index_id, title, page_id FROM other_page WHERE index_id = $id");
    $row = mysqli_fetch_assoc($check);
    if (!$row) {
        echo "Skip: index_id $id not found\n";
        continue;
    }
    if ((string) $row['page_id'] !== '8') {
        echo "Skip: index_id $id is page_id {$row['page_id']}, not 8\n";
        continue;
    }
    if (!mysqli_query($db->dbLink, "DELETE FROM other_page WHERE index_id = $id AND page_id = '8'")) {
        echo "Failed to delete index_id $id: " . mysqli_error($db->dbLink) . "\n";
        continue;
    }
    echo "Deleted index_id $id | {$row['title']}\n";
}

$counts = mysqli_fetch_assoc(mysqli_query(
    $db->dbLink,
    "SELECT COUNT(*) AS total,
            SUM(CASE WHEN LOWER(TRIM(status)) = 'approved' THEN 1 ELSE 0 END) AS approved
     FROM other_page WHERE page_id = '8'"
));
echo "\nAfter cleanup — total: {$counts['total']}, approved: {$counts['approved']}\n";
