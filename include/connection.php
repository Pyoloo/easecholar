<?php
$serverName = "easecholar.mysql.database.azure.com";
$databaseName = "easecholar"; // Replace with your actual database name
$username = "easecholar";
$password = "your-password"; // Replace with your actual password

// Construct the connection string
$connectionString = "Server=$serverName;Database=$databaseName;User Id=$username;Password=$password;";

// Create a database connection
$conn = new mysqli($serverName, $username, $password, $databaseName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

