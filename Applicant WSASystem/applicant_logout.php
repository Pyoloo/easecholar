<?php
session_name("ApplicantSession");
session_start();

$_SESSION = array();

session_destroy();

header('Location: applicant_login.php');
exit();
?>
