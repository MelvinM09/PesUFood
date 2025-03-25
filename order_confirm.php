<?php
session_start();
include("connection/connect.php"); // Ensure the correct path

if (!isset($_SESSION['user_email'])) {
    die("Error: User not logged in!");
}

if (empty($_SESSION['cart'])) {
    die("Error: No items in cart!");
}

$user_email = $_SESSION['user_email'];
$order_items_json = json_encode($_SESSION['cart']); // Convert cart to JSON

// Calculate total price
$total_price = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $total_price += $cart_item['price'] * $cart_item['quantity'];
}

// Insert order into database
try {
    $stmt = $conn->prepare("INSERT INTO orders (user_email, order_items, total_price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $user_email, $order_items_json, $total_price);
    $stmt->execute();
    
    // Clear cart after order is placed
    unset($_SESSION['cart']);

    echo "<script>alert('Order placed successfully!'); window.location='index.php';</script>";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
