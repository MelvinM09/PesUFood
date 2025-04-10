<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Redirect if not logged in
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /PesUFood/login.php");
    exit();
}

// Fetch orders
$query = "SELECT * FROM orders ORDER BY order_id DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Display messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Light Theme */
        body {
            background-color: #f8f9fa;
            color: #212529;
        }
        
        .card {
            background-color: white;
            border: 1px solid #dee2e6;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #212529;
        }
        
        .table td {
            background-color: white;
            color: #212529;
        }
        
        .form-control {
            color: #212529;
            background-color: white;
        }
        
        /* Dark Theme */
        .dark-mode {
            background-color: #121212;
            color: #e9ecef;
        }
        
        .dark-mode .card {
            background-color: #1e1e1e;
            border-color: #444;
            color: #e9ecef;
        }
        
        .dark-mode .table th {
            background-color: #2c2c2c;
            color: #e9ecef;
        }
        
        .dark-mode .table td {
            background-color: #1e1e1e;
            color: #e9ecef;
        }
        
        .dark-mode .form-control {
            background-color: #333;
            color: #f8f9fa;
            border-color: #555;
        }
        
        .dark-mode .form-control:focus {
            background-color: #444;
            color: #f8f9fa;
        }
        
        /* Common Styles */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .date-time {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
        }
        
        .date {
            font-weight: bold;
        }
        
        .time {
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .dark-mode .time {
            color: #e9ecef;
        }
        
        .table {
            table-layout: fixed;
        }
        
        .table th, .table td {
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body class="<?= ($_SESSION['SETTINGS']['dark_mode'] == '1') ? 'dark-mode' : '' ?>">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="bi bi-egg-fried"></i> PesUFood Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a></li>
                <li class="nav-item"><a class="nav-link" href="dishes.php"><i class="bi bi-egg"></i> Dishes</a></li>
                <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Manage Orders</h2>
        <a href="dashboard.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Orders Table Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Recent Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Order ID</th>
                            <th style="width: 18%;">Order Email</th>
                            <th style="width: 15%;">Date / Time</th>
                            <th style="width: 20%;">Order Items</th>
                            <th style="width: 10%;">Price (₹)</th>
                            <th style="width: 8%;">Quantity</th>
                            <th style="width: 10%;">Total (₹)</th>
                            <th style="width: 14%;">Actions</th>
                            <th style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                $order_id = $row['order_id'];
                                $order_items = json_decode($row['order_items'], true);
                                $rowspan = count($order_items);
                                $first_row = true;

                                // Split date and time
                                $datetime = new DateTime($row['order_date']);
                                $date = $datetime->format('Y-m-d');
                                $time = $datetime->format('H:i:s');
                                ?>
                                
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <?php if ($first_row): ?>
                                            <td rowspan="<?= $rowspan ?>"><?= $order_id ?></td>
                                            <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($row['user_email']) ?></td>
                                            <td rowspan="<?= $rowspan ?>">
                                                <div class="date-time">
                                                    <span class="time"><?= $time ?></span>
                                                    <span class="date"><?= $date ?></span>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        
                                        <td><?= htmlspecialchars($item['name']) ?></td>
                                        <td>₹<?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                        
                                        <?php if ($first_row): ?>
                                            <?php
                                            $status = isset($row['status']) ? ucfirst($row['status']) : "Pending";
                                            $status_class = 'status-' . strtolower($status);
                                            ?>
                                            <td rowspan="<?= $rowspan ?>">
                                                <div class="action-buttons">
                                                    <button class="btn btn-success btn-sm" onclick="updateStatus(<?= $order_id ?>, 'Completed')">
                                                        <i class="bi bi-check-circle"></i> Complete
                                                    </button>
                                                    <button class="btn btn-warning btn-sm" onclick="updateStatus(<?= $order_id ?>, 'Pending')">
                                                        <i class="bi bi-hourglass"></i> Pending
                                                    </button>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteOrder(<?= $order_id ?>)">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </div>
                                            </td>
                                            <td rowspan="<?= $rowspan ?>" class="<?= $status_class ?>"><?= $status ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php $first_row = false; ?>
                                <?php endforeach; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateStatus(orderId, status) {
    fetch(`update_status.php?order_id=${orderId}&status=${status}`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "Success") {
            // Update status display
            const statusCell = document.querySelector(`td[class^="status-"][rowspan]`);
            if (statusCell) {
                statusCell.textContent = status;
                // Remove all status classes
                statusCell.classList.remove('status-pending', 'status-completed', 'status-cancelled');
                // Add the new status class
                statusCell.classList.add(`status-${status.toLowerCase()}`);
            }
            alert(`Order #${orderId} marked as ${status}`);
        } else {
            alert(' Status: ' + data);
        }
    })
    .catch(err => {
        console.error('Fetch Error:', err);
        alert('Error updating status: ' + err.message);
    });
}

function deleteOrder(orderId) {
    if (confirm("Are you sure you want to delete this order?")) {
        fetch(`delete_order.php?order_id=${orderId}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "Success") {
                alert(`Order #${orderId} deleted.`);
                location.reload();
            } else {
                alert('Failed to delete order: ' + data);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Error deleting order');
        });
    }
}
</script>
</body>
</html>