<?php

require_once __DIR__ . '/../app/class/databaseConn.php';
require_once __DIR__ . '/../include/mystery_table_helpers.php';

$db = new DatabaseConn();
$updated = mystery_table_backfill_god_ids($db->dbLink);

echo 'Backfilled god_id for ' . $updated . ' mystery temples.' . PHP_EOL;

$gods = mystery_table_fetch_gods($db->dbLink);
echo 'Filter gods (' . count($gods) . '):' . PHP_EOL;
foreach ($gods as $god) {
    echo '  - ' . $god['god_name'] . PHP_EOL;
}
