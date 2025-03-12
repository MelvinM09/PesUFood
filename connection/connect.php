<?php
$servername = "localhost";  // XAMPP default server
$username = "root";         // Default MySQL username
$password = "";             // Default MySQL password (empty in XAMPP)
$database = "online_food_php"; // Use lowercase as seen in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set UTF-8 character encoding
$conn->set_charset("utf8mb4");

// Uncomment for debugging
// echo "Connected successfully";
if ($conn) {
    echo "Database connected successfully!";
}

?>
