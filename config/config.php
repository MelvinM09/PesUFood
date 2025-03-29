<?php
// Site Configuration
define("SITE_NAME", "PesUFood");
define("ADMIN_EMAIL", "admin@pesufood.com");

// Admin Password (Hashed)
define("ADMIN_PASSWORD", '$2y$10$q8pvFAFzw8xORDtWYYKk0e0MkWy8n4ytaXyV0ba0Yq6QdoLMWateK');

// Database Configuration
$servername = "localhost";    // Default for XAMPP
$username = "root";           // Default XAMPP username
$password = "";               // Default XAMPP password
$dbname = "online_food_php";  // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("⚠️ Database connection failed: " . mysqli_connect_error());
}
?>
