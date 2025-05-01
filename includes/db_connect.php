<?php
// includes/db_connect.php

$host = "localhost";     // Your database host
$user = "root";          // Your DB username
$password = "";          // Your DB password (keep blank for default XAMPP)
$database = "laundrygo"; // Your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
