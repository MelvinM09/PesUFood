<?php
include_once "config/config.php"; 
include_once "connection/connect.php";


if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed: " . mysqli_connect_error();
}
?>
