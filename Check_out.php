<?php
session_start();
include_once "config/config.php"; 
include_once "connection/connect.php";
 

if (!isset($_SESSION['user_email'])) {
    echo "Please login to continue!";
    exit;
}

$user_email = $_SESSION['user_email'];
$query = "SELECT * FROM orders WHERE user_email='$user_email'";
$result = mysqli_query($conn, $query);

echo "<h2>Your Order</h2>";

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['dish_name'] . " - $" . $row['price'] . "<br>";
}

echo '<a href="payment.php">Proceed to Payment</a>';
?>
