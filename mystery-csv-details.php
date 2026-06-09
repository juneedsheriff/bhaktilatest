<?php
$id = $_GET['id'] ?? '';
header('Location: mystery-details.php?id=' . rawurlencode((string) $id), true, 301);
exit;
