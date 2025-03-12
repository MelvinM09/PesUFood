<?php
include_once "config/config.php"; 
include_once "connection/connect.php";
 

$query = "SELECT * FROM dishes";
$result = mysqli_query($conn, $query);

echo "<h2>Menu</h2>";

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['name'] . " - $" . $row['price'];
    echo '<a href="Check_out.php">Order Now</a><br>';
}
?>
