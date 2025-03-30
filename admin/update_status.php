<?php
session_start();
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /PesUFood/login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../config/config.php');

// Validate input
if (!isset($_GET['order_id'], $_GET['status'])) {
    echo "Invalid request: Missing parameters.";
    exit();
}

$order_id = intval($_GET['order_id']);
$status = strtolower(trim($_GET['status'])); // Trim for safety

// Validate allowed statuses
$allowed_statuses = ['pending', 'completed'];
if (!in_array($status, $allowed_statuses)) {
    echo "Invalid status value: " . htmlspecialchars($status);
    exit();
}

// Verify database connection
if (!$conn) {
    echo "Database connection failed: " . mysqli_connect_error();
    exit();
}

try {
    // Update query with prepared statement
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }

    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        echo "Order status updated successfully!";
    } else {
        echo "Execute failed: " . $stmt->error;
    }

    $stmt->close();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
