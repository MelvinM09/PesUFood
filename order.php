<?php
session_start();
include "db.php"; // Database connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user ID and dish ID
$user_id = $_SESSION['user_id'];
if (!isset($_GET['dish_id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$dish_id = $_GET['dish_id'];

// Fetch dish details
$dish_query = "SELECT * FROM dishes WHERE id = '$dish_id'";
$dish_result = mysqli_query($conn, $dish_query);
$dish = mysqli_fetch_assoc($dish_result);

if (!$dish) {
    echo "Dish not found!";
    exit();
}

// Insert order into database
$order_query = "INSERT INTO orders (user_id, dish_id, price, status) VALUES ('$user_id', '$dish_id', '{$dish['price']}', 'Pending')";
if (mysqli_query($conn, $order_query)) {
    echo "<script>
            alert('Order placed successfully!');
            window.location.href = 'user_dashboard.php';
          </script>";
} else {
    echo "Error placing order: " . mysqli_error($conn);
}

?>
