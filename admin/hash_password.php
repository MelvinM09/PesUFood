<?php
// Password to be hashed
$password = "admin@123";  // Replace this with your actual password

// Hash the password using BCRYPT
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Display the hashed password
echo "Hashed Password: " . $hashed_password;
?>
