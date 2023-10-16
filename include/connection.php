<?php
$serverName = "easecholar.mysql.database.azure.com";
$databaseName = "easecholar";
$username = "easecholar";
$password = "IphonenaMaarte0208";

// Check if the mysqli extension is available
if (!function_exists('mysqli_init')) {
    die('mysqli extension is not available. Please enable it in your PHP configuration.');
}

// Construct the connection string with SSL options
$connectionString = "Server=$serverName;Database=$databaseName;User Id=$username;Password=$password;Encrypt=true;TrustServerCertificate=false";

// Create a database connection with the mysqli_ssl_set function
$conn = mysqli_init();
if (!$conn) {
    die("mysqli_init failed");
}

if (!$conn->real_connect($serverName, $username, $password, $databaseName, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connect Error: " . mysqli_connect_error());
}

// If you reach this point, the connection was successful
echo "Connected to the database!";
?>
