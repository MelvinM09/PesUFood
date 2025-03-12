<?php
session_start();
include_once "../config/config.php"; // If one level up
include_once "../connection/connect.php"; 

if (!isset($_SESSION['user_email'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['name'];
    $user_email = $_SESSION['user_email'];

    $query = "UPDATE users SET name='$new_name' WHERE email='$user_email'";
    if (mysqli_query($conn, $query)) {
        echo "Profile updated!";
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<form method="post">
    New Name: <input type="text" name="name" required><br>
    <button type="submit">Update</button>
</form>
