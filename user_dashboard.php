<?php
session_start();
include "db.php"; // Database connection

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
</head>
<body>

<h2>Welcome, <?php echo $user['name']; ?>!</h2>
<p>Email: <?php echo $user['email']; ?></p>

<h3>Available Dishes</h3>
<table border="1">
    <tr>
        <th>Dish Name</th>
        <th>Price</th>
        <th>Action</th>
    </tr>
    <?php
    $dish_query = "SELECT * FROM dishes";
    $dish_result = mysqli_query($conn, $dish_query);
    
    while ($dish = mysqli_fetch_assoc($dish_result)) {
        echo "<tr>
                <td>{$dish['name']}</td>
                <td>₹{$dish['price']}</td>
                <td><a href='order.php?dish_id={$dish['id']}'>Order</a></td>
              </tr>";
    }
    ?>
</table>

<h3>Your Orders</h3>
<table border="1">
    <tr>
        <th>Dish Name</th>
        <th>Price</th>
        <th>Status</th>
    </tr>
    <?php
    $order_query = "SELECT o.*, d.name FROM orders o 
                    JOIN dishes d ON o.dish_id = d.id 
                    WHERE o.user_id = '$user_id'";
    $order_result = mysqli_query($conn, $order_query);
    
    while ($order = mysqli_fetch_assoc($order_result)) {
        echo "<tr>
                <td>{$order['name']}</td>
                <td>₹{$order['price']}</td>
                <td>{$order['status']}</td>
              </tr>";
    }
    ?>
</table>

<a href="logout.php">Logout</a>

</body>
</html>
