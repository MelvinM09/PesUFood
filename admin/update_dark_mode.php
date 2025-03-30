<?php
session_start();
require_once('../config/config.php'); // Include database config

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dark_mode = isset($_POST['dark_mode']) ? intval($_POST['dark_mode']) : 0;

    // Update the settings table
    $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'dark_mode'");
    $stmt->bind_param("s", $dark_mode);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Update session value immediately
        $_SESSION['SETTINGS']['dark_mode'] = $dark_mode;
        echo "success";
    } else {
        echo "failed";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "invalid_request";
}
?>
