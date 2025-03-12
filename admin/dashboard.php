<?php
include_once "config/config.php"; 
include_once "connection/connect.php";



session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

echo "<h2>Admin Dashboard</h2>";
echo "<a href='manage_orders.php'>Manage Orders</a> | ";
echo "<a href='manage_users.php'>Manage Users</a> | ";
echo "<a href='settings.php'>Settings</a>";
?>
