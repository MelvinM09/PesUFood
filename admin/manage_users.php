<?php
include_once "config/config.php"; 
include_once "connection/connect.php";


$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

echo "<h2>All Users</h2>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Email: " . $row['email'] . "<br>";
}
?>
