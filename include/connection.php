<?php
$dbServer = "easecholar.mysql.database.azure.com";
$dbUsername = "easecholar";
$dbPassword = "IphonenaMaarte0208";
$dbName = "easecholar";

$dbConn = mysqli_connect($dbServer, $dbUsername, $dbPassword, $dbName);

if (!$dbConn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
