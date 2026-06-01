<?php ob_start();
session_start();
$_SESSION['admin_id']="";
$_SESSION['staff_id']="";
session_destroy();

header("location:index.html");

?>
