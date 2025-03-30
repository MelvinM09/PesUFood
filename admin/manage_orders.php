<?php
require_once('../config/config.php'); // Ensure DARK_MODE is available
$darkModeClass = (DARK_MODE) ? "dark-mode" : "";
?>
<body class="<?= $darkModeClass ?>">

<?php
include '../config/config.php';

// Fetch orders
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$result = mysqli_query($conn, $sql);

// Apply dark mode setting
$darkModeClass = (DARK_MODE === "1") ? "dark-mode" : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 100%; margin: auto; }
        .table { width: 100%; table-layout: fixed; }
        .table th, .table td { vertical-align: middle; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .table th { background-color: #212529; color: white; }
        .btn { width: 100px; }
        .no-select { user-select: none; }
        .table td { height: 60px; }
        .action-buttons { display: flex; flex-direction: column; gap: 5px; }
        .date-time { display: flex; flex-direction: column; justify-content: center; gap: 5px; }
        .date { font-weight: bold; }
        .time { font-size: 0.9rem; color: rgb(0, 0, 0); font-weight: bold; }

        /* 🌙 Dark Mode Styles */
        .dark-mode { background-color: #121212; color: white; }
        .dark-mode .table th { background-color: #1e1e1e; }
        .dark-mode .table td { color: white; }
        .dark-mode .btn { border-color: white; }
        .dark-mode .date-time .time { color: white; }
    </style>
</head>
<body class="p-4 <?= $darkModeClass ?>">
    <div class="container">
        <h2 class="mb-4">Manage Orders</h2>
        <a href="dashboard.php" class="btn btn-primary btn-sm mb-2" style="white-space: nowrap; width: 150px; text-align: center;">← Back to Dashboard</a>

        <table class="table table-bordered table-hover">
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
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $order_id = $row['order_id'];
                        $order_items = json_decode($row['order_items'], true);
                        $rowspan = count($order_items);
                        $first_row = true;

                        // Split date and time
                        $datetime = new DateTime($row['order_date']);
                        $date = $datetime->format('Y-m-d');
                        $time = $datetime->format('H:i:s');

                        foreach ($order_items as $item) {
                            echo "<tr>";

                            if ($first_row) {
                                echo "<td rowspan='$rowspan'>{$order_id}</td>";
                                echo "<td rowspan='$rowspan' class='no-select'>{$row['user_email']}</td>";
                                echo "<td rowspan='$rowspan'>
                                        <div class='date-time'>
                                            <span class='time'>{$time}</span>
                                            <span class='date'>{$date}</span>
                                        </div>
                                      </td>";
                            }
 
                            echo "<td>{$item['name']}</td>";
                            echo "<td>₹{$item['price']}</td>";
                            echo "<td>{$item['quantity']}</td>";
                            $item_total = $item['price'] * $item['quantity'];
                            echo "<td>₹{$item_total}</td>";

                            if ($first_row) {
                                $status = isset($row['status']) ? ucfirst($row['status']) : "Pending";
                                echo "<td rowspan='$rowspan'>
                                    <div class='action-buttons'>
                                        <button class='btn btn-success btn-sm' onclick=\"updateStatus({$order_id}, 'Completed')\">Complete</button>
                                        <button class='btn btn-warning btn-sm' onclick=\"updateStatus({$order_id}, 'Pending')\">Pending</button>
                                        <button class='btn btn-danger btn-sm' onclick=\"deleteOrder({$order_id})\">Delete</button>
                                    </div>
                                </td>";
                                echo "<td rowspan='$rowspan' id='status-{$order_id}'>{$status}</td>";
                            }
                            echo "</tr>";
                            $first_row = false;
                        }
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No orders found.</td></tr>";
                }
                mysqli_free_result($result);
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>

    <script>
    function updateStatus(orderId, status) {
        fetch(`admin/update_status.php?order_id=${orderId}&status=${status}`, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "Success") {
                document.getElementById(`status-${orderId}`).textContent = status;
                alert(`Order #${orderId} marked as ${status}`);
            } else {
                alert('Failed to update status: ' + data);
            }
        })
        .catch(err => {
            console.error('Fetch Error:', err);
            alert('Error updating status: ' + err.message);
        });
    }

    function deleteOrder(orderId) {
        if (confirm("Are you sure you want to delete this order?")) {
            fetch(`admin/delete_order.php?order_id=${orderId}`, {
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

</script>
</html>
