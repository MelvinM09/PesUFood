<?php
$admin_password = "admin@123";  // Change to your desired password
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);
echo "Hashed Password: " . $hashed_password;
?>
