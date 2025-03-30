<?php
require_once('../config/config.php');
$id = $_POST['id'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

$query = "UPDATE users SET name='$name', email='$email'";
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password='$hashedPassword'";
}
$query .= " WHERE id=$id";
mysqli_query($conn, $query);
?>
