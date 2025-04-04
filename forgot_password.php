<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include_once "connection/connect.php";
require_once "config/config.php"; // Ensure this contains DEFAULT_ADMIN_EMAIL

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // Check if email exists
    $check_query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $reset_email = $email; // Use the entered email
    } else {
        // Use default admin email if no account found
        $reset_email = DEFAULT_ADMIN_EMAIL;
    }

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['reset_email'] = $reset_email;
    $_SESSION['reset_otp'] = $otp;

    // Send OTP via Email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Use secure authentication
        $mail->Username = getenv('SMTP_USERNAME'); // Set in your server environment
        $mail->Password = getenv('SMTP_PASSWORD'); // Set in your server environment
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('melvinm1391@gmail.com', 'PesUFood Support');
        $mail->addAddress($reset_email);
        $mail->Subject = 'PesUFood - Password Reset OTP';
        $mail->Body = "Your OTP for password reset is: $otp";

        $mail->send();
        header("Location: verify_reset_otp.php");
        exit;
    } catch (Exception $e) {
        $error = "Email sending failed: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card mx-auto" style="width: 350px; padding: 20px;">
        <h3 class="text-center">Forgot Password</h3>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="post">
            <div class="mb-3">
                <label>Enter Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send OTP</button>
        </form>
    </div>
</div>

</body>
</html>
