<?php
session_start();
require_once('../config/config.php');

// Redirect if not logged in
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /PesUFood/login.php");
    exit();
}

// Fetch latest settings into session
$query = "SELECT * FROM settings";
$result = mysqli_query($conn, $query);
$_SESSION['SETTINGS'] = [];
while ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['SETTINGS'][$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; color: black; transition: 0.3s; }
        .container { margin-top: 50px; }
        .card { transition: 0.3s; }
        .card:hover { box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }

        /* Dark Mode Styles */
        .dark-mode { background-color: #121212; color: white; }
        .dark-mode .card { background-color: #1e1e1e; color: white; }
        .dark-mode .navbar { background-color: #222; }
        .dark-mode .btn { border-color: white; }
    </style>
</head>
<body class="<?= ($_SESSION['SETTINGS']['dark_mode'] == '1') ? 'dark-mode' : '' ?>">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">PesUFood Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="manage_orders.php">Manage Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button id="darkModeToggle" class="btn btn-outline-light me-2">
                        <?= ($_SESSION['SETTINGS']['dark_mode'] == '1') ? '☀ Light Mode' : '🌙 Dark Mode' ?>
                    </button>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container text-center">
    <h1 class="mt-4">Welcome Admin!</h1>
    <p class="lead">Manage orders, users, and settings efficiently.</p>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card p-3">
                <div class="card-body">
                    <h5 class="card-title">Manage Orders</h5>
                    <p class="card-text">View and manage customer orders.</p>
                    <a href="manage_orders.php" class="btn btn-primary">Go to Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">View and control user accounts.</p>
                    <a href="manage_users.php" class="btn btn-success">Go to Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3">
                <div class="card-body">
                    <h5 class="card-title">Settings</h5>
                    <p class="card-text">Configure system settings.</p>
                    <a href="settings.php" class="btn btn-warning">Go to Settings</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Toggle -->
    <div class="mt-5">
        <h5>Maintenance Mode</h5>
        <button id="maintenanceToggle" class="btn btn-outline-danger">
            <?= ($_SESSION['SETTINGS']['maintenance_mode'] ?? '0') == '1' ? 'Disable Maintenance' : 'Enable Maintenance' ?>
        </button>
        <small class="d-block mt-2" style="color: rgba(113, 111, 111, 0.7);">Pause frontend for users when maintenance is on.</small>
        </div>
</div>

<!-- JavaScript for Toggles -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const darkModeToggle = document.getElementById("darkModeToggle");
        const maintenanceToggle = document.getElementById("maintenanceToggle");
        const body = document.body;

        // Dark Mode
        let isDarkMode = <?= ($_SESSION['SETTINGS']['dark_mode'] == "1") ? 'true' : 'false' ?>;
        darkModeToggle.addEventListener("click", function () {
            isDarkMode = !isDarkMode;
            body.classList.toggle("dark-mode");
            darkModeToggle.textContent = isDarkMode ? "☀ Light Mode" : "🌙 Dark Mode";

            fetch("update_settings.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "key=dark_mode&value=" + (isDarkMode ? "1" : "0")
            });
        });

        // Maintenance Mode
        maintenanceToggle.addEventListener("click", function () {
            const isEnabling = maintenanceToggle.textContent.includes("Enable");
            const newValue = isEnabling ? "1" : "0";

            fetch("update_settings.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "key=maintenance_mode&value=" + newValue
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                maintenanceToggle.textContent = isEnabling ? "Disable Maintenance" : "Enable Maintenance";
            })
            .catch(error => alert("Failed to update maintenance setting."));
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>