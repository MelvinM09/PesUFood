<?php
session_start();
include_once "connection/connect.php";
require_once "config/config.php"; // Ensure this contains DEFAULT_ADMIN_EMAIL

// Check if admin is logged in (You can change this condition based on your authentication logic)
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_email = trim($_POST['new_email']);

    // Validate email format
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update admin email in database
        $update_query = "UPDATE users SET email = ? WHERE role = 'admin'";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("s", $new_email);
        if ($stmt->execute()) {
            $success = "Admin email updated successfully!";
        } else {
            $error = "Failed to update email.";
        }
    }
}

// Fetch current admin email
$query = "SELECT email FROM users WHERE role = 'admin' LIMIT 1";
$result = $conn->query($query);
$admin_email = ($result->num_rows > 0) ? $result->fetch_assoc()['email'] : DEFAULT_ADMIN_EMAIL;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin Email - PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card mx-auto" style="width: 400px; padding: 20px;">
        <h3 class="text-center">Update Admin Email</h3>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>
        <form method="post">
            <div class="mb-3">
                <label>Current Email</label>
                <input type="email" class="form-control" value="<?php echo $admin_email; ?>" disabled>
            </div>
            <div class="mb-3">
                <label>New Email</label>
                <input type="email" name="new_email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Update Email</button>
        </form>
    </div>
</div>

</body>
</html>
