<?php
$servername = "localhost";  // XAMPP default server
$username = "root";         // Default MySQL username
$password = "";             // Default MySQL password (empty in XAMPP)
$database = "online_food_php"; // Ensure this matches your database name

// Enable error reporting (useful during development)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Create a connection
    $conn = new mysqli($servername, $username, $password, $database);
    $conn->set_charset("utf8mb4"); // Set UTF-8 encoding
} catch (Exception $e) {
    // Handle connection errors gracefully
    die("Database Connection Failed: " . $e->getMessage());
}

// Debugging mode: Show only in development
$debug_mode = true; // Change to `false` in production
if ($debug_mode && basename($_SERVER['PHP_SELF']) == "connect.php") {
    echo "✅ Database connected successfully!";
}
?>
