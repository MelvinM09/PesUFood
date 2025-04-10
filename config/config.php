<?php
// Start session only if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_food_php";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Initialize settings session array
if (!isset($_SESSION['SETTINGS'])) {
    $_SESSION['SETTINGS'] = [];
}

// Fetch settings from database
$settingsQuery = "SELECT setting_key, setting_value FROM settings";
$result = $conn->query($settingsQuery);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $_SESSION['SETTINGS'][$row['setting_key']] = $row['setting_value'];
    }
} else {
    error_log("⚠ No settings found in the database!");
}

// Set default admin email
define("DEFAULT_ADMIN_EMAIL", "melvinm1391@gmail.com");

// ❌ DO NOT CLOSE `$conn` HERE! Other pages need it for queries.
?>
