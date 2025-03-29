<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /login.php");
    exit();
}

if (isset($_POST['update_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    // Update password in the database (you'll need the user's email from the session)
    include '../config.php';
    $email = $_SESSION['user_email'];
    $sql = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $email);
    if ($stmt->execute()) {
        $success = "Password updated successfully!";
    } else {
        $error = "Failed to update password.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Settings</title>
</head>
<body>
    <h2>Admin Settings</h2>
    <a href="dashboard.php">⬅ Back to Dashboard</a>

    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <h3>Change Password</h3>
    <form method="POST">
        <label>New Password:</label><br>
        <input type="password" name="new_password" required><br><br>
        <button type="submit" name="update_password">Update Password</button>
    </form>
</body>
</html>