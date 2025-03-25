<?php
session_start();
include_once "config/config.php"; 
include_once "connection/connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dish_id'])) {
    $dish_id = $_POST['dish_id'];

    if (isset($_POST['increase'])) {
        $query = "UPDATE orders SET quantity = quantity + 1 WHERE id = ?";
    } elseif (isset($_POST['decrease'])) {
        $query = "UPDATE orders SET quantity = quantity - 1 WHERE id = ? AND quantity > 1";
    } elseif (isset($_POST['delete'])) {
        $query = "DELETE FROM orders WHERE id = ?";
    }

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $dish_id);
    mysqli_stmt_execute($stmt);
}

header("Location: check_out.php");
exit;
