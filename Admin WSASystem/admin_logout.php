<?php
session_name("AdminSession");
session_start();

$_SESSION = array();

session_destroy();

header('Location: admin_login.php');
exit();
?>
