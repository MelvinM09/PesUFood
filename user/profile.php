<?php
session_start();
include_once "../config/config.php"; // If one level up
include_once "../connection/connect.php"; 

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$query = "SELECT * FROM users WHERE email='$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

echo "<h2>User Profile</h2>";
echo "Name: " . $user['name'] . "<br>";
echo "Email: " . $user['email'] . "<br>";
echo '<a href="edit_profile.php">Edit Profile</a> | <a href="logout.php">Logout</a>';
?>
