<?php
// Retrieve environment variables
$smtpUsername = getenv('SMTP_USERNAME');
$smtpPassword = getenv('SMTP_PASSWORD');

// Output the values
echo "SMTP Username: " . ($smtpUsername ? $smtpUsername : "Not Set") . "<br>";
echo "SMTP Password: " . ($smtpPassword ? "*****" : "Not Set");
?>