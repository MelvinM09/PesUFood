<?php
session_start();
include_once "connection/connect.php"; // Ensure this is correct

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp']);
    
    if ($entered_otp == $_SESSION['otp_code']) {
        // Register user since OTP is correct
        $name = $_SESSION['otp_name'];
        $email = $_SESSION['otp_email'];
        $password = $_SESSION['otp_password']; // Already hashed
        
        $insert_query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            unset($_SESSION['otp_name'], $_SESSION['otp_email'], $_SESSION['otp_password'], $_SESSION['otp_code']);
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            header("Location: index.php"); // Redirect to user dashboard
            exit;
        } else {
            $error = "Registration failed! Please try again.";
        }
    } else {
        $error = "Invalid OTP! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card mx-auto" style="width: 350px; padding: 20px;">
        <h3 class="text-center">Verify OTP</h3>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="post">
            <div class="mb-3">
                <label>Enter OTP</label>
                <input type="text" name="otp" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>
</div>

</body>
</html>
