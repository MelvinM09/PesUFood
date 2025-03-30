<?php
session_start();
require_once('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['key'], $_POST['value'])) {
    $key = trim($_POST['key']);
    $value = trim($_POST['value']);

    // Allow only expected setting keys
    $allowed_keys = ["site_name", "admin_email", "site_logo", "contact_info", "dark_mode", "maintenance_mode"];
    if (!in_array($key, $allowed_keys)) {
        echo "❌ Invalid setting key!";
        exit();
    }

    // Normalize values for toggles
    if ($key === "dark_mode" || $key === "maintenance_mode") {
        $value = ($value === "1") ? "1" : "0";
    }

    // Prepare and execute update query
    $query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $value, $key);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            // Update session values for live toggling
            $_SESSION['SETTINGS'][$key] = $value;

            // Optional: maintain legacy dark_mode session key
            if ($key === "dark_mode") {
                $_SESSION['dark_mode'] = $value;
            }

            echo "✅ Updated successfully!";
        } else {
            echo "❌ Update failed!";
        }
    } else {
        echo "❌ Database query error!";
    }
} else {
    echo "❌ Invalid request!";
}