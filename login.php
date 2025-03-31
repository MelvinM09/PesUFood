<?php
session_start();
require_once "connection/connect.php"; // Changed from include_once to require_once
require_once "config/config.php"; // Site-wide config

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if maintenance mode is enabled
$maintenance = false;
$stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc(); // Get the row first
    if ($row && isset($row['setting_value']) && $row['setting_value'] === '1') {
        $maintenance = true;
    }
    $stmt->close();
}

if ($maintenance) {
    header("Location: maintenance.php");
    exit();
}

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: /PesUFood/admin/dashboard.php");
    exit;
}

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

// Initialize variables
$error = '';
$email = '';

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check in Admin Table first
        $query = "SELECT id, email, password FROM admin WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $admin = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $admin['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_id'] = $admin['id'];
                    
                    // Set last login time
                    $update_stmt = $conn->prepare("UPDATE admin SET last_login = NOW() WHERE id = ?");
                    $update_stmt->bind_param("i", $admin['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    header("Location: /PesUFood/admin/dashboard.php");
                    exit;
                } else {
                    $error = "Incorrect email or password";
                }
            }
            $stmt->close();
        }

        // If not admin, check in users table
        // If not admin, check in users table
if (empty($error)) {
    try {
        // Basic user query without status check
        $query = "SELECT id, email, password, name FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);
                    
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    // Update last login if column exists
                    $conn->query("SHOW COLUMNS FROM users LIKE 'last_login'");
                    if ($conn->affected_rows > 0) {
                        $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                        $update_stmt->bind_param("i", $user['id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                    
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Incorrect email or password";
                }
            } else {
                $error = "No account found with this email";
            }
            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    } catch (mysqli_sql_exception $e) {
        error_log("Login error: " . $e->getMessage());
        $error = "A system error occurred. Please try again later.";
    }
}
    }
    
    // Add delay to prevent brute force attacks
    sleep(1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PesUFood - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: url('assets/images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 400px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.95);
        }
        .form-control {
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .alert {
            border-radius: 5px;
        }
        .password-container {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="text-center mb-4">
        <h3>Login to PesUFood</h3>
        <p class="text-muted">Access your account to continue</p>
    </div>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <form method="post" autocomplete="off">
        <!-- Email -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?php echo htmlspecialchars($email); ?>" 
                   required autofocus>
        </div>
        
        <!-- Password -->
        <div class="mb-3 password-container">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" 
                   class="form-control" required>
            <span class="password-toggle" id="togglePassword">
                <i class="far fa-eye"></i>
            </span>
        </div>
        
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </div>
    </form>

    <div class="text-center mt-3">
        <p class="mb-1">
            <a href="forgot_password.php" class="text-decoration-none">
                <i class="fas fa-key"></i> Forgot Password?
            </a>
        </p>
        <p class="mb-0">
            Don't have an account? 
            <a href="registration.php" class="text-decoration-none">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
</script>
</body>
</html>