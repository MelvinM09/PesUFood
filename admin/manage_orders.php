<?php
include_once "config/config.php"; 
include_once "connection/connect.php";



$query = "SELECT * FROM orders";
$result = mysqli_query($conn, $query);

echo "<h2>All Orders</h2>";

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['order_id'] . " - " . $row['user_email'] . " - " . $row['dish_name'] . "<br>";
}
?>
