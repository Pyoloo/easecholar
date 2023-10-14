<?php
session_start();

$_SESSION = array();

session_destroy();

header('Location: osa_login.php');
exit();
?>
