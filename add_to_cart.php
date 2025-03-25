<?php
session_start();
include_once "config/config.php"; 
include_once "connection/connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dish_id'])) {
    $dish_id = $_POST['dish_id'];
    
    // Check if the dish is already in the cart
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $dish_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $query = "UPDATE orders SET quantity = quantity + 1 WHERE id = ?";
    } else {
        $query = "INSERT INTO orders (id, quantity) VALUES (?, 1)";
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $dish_id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php");
exit;
