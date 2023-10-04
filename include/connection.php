<?php
$serverName = "easecholar.mysql.database.azure.com";
$databaseName = "easecholar";
$username = "easecholar";
$password = "IphonenaMaarte0208";

// Construct the connection string with SSL options
$connectionString = "Server=$serverName;Database=$databaseName;User Id=$username;Password=$password;Encrypt=true;TrustServerCertificate=false";

// Create a database connection with the mysqli_ssl_set function
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
if (!$conn) {
    die("mysqli_init failed");
}
if (!$conn->real_connect($serverName, $username, $password, $databaseName, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connect Error: " . mysqli_connect_error());
}
?>
