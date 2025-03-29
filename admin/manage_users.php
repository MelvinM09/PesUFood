<?php
// Include Config File
require_once('../config/config.php');

// Create "remarks" column if it doesn't exist
mysqli_query($conn, "ALTER TABLE orders ADD COLUMN IF NOT EXISTS remarks VARCHAR(20) DEFAULT 'Pending'");

// Fetch Orders
$sql = "SELECT * FROM orders ORDER BY order_id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        .table {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
        }
        th {
            background-color: #343a40;
            color: white;
            text-align: center;
        }
        td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-action {
            margin-right: 5px;
        }
        .no-select {
            user-select: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Manage Orders</h2>
    <a href="dashboard.php" class="btn btn-primary mb-3">← Back to Dashboard</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Email</th>
                <th>Date/Time</th>
                <th>Order Items</th>
                <th>Price (₹)</th>
                <th>Quantity</th>
                <th>Total (₹)</th>
                <th>Actions</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                $orders = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $orders[$row['order_id']][] = $row;
                }

                foreach ($orders as $order_id => $order_items) {
                    $rowspan = count($order_items);
                    $first = true;

                    foreach ($order_items as $item) {
                        $orderDetails = json_decode($item['order_items'], true);
                        foreach ($orderDetails as $index => $detail) {
                            echo "<tr>";
                            if ($first) {
                                echo "<td rowspan='$rowspan'>{$order_id}</td>";
                                echo "<td rowspan='$rowspan' class='no-select'>{$item['order_email']}</td>";
                                echo "<td rowspan='$rowspan'>{$item['order_date']}</td>";
                                $first = false;
                            }
                            echo "<td>{$detail['name']}</td>";
                            echo "<td>₹{$detail['price']}</td>";
                            echo "<td>{$detail['quantity']}</td>";
                            $total = $detail['price'] * $detail['quantity'];
                            echo "<td>₹{$total}</td>";

                            if ($index === 0) {
                                echo "<td rowspan='$rowspan'>
                                    <button class='btn btn-success btn-action' onclick='updateRemarks($order_id, \"Completed\")'>Complete</button>
                                    <button class='btn btn-warning btn-action' onclick='updateRemarks($order_id, \"Pending\")'>Pending</button>
                                </td>";
                                echo "<td rowspan='$rowspan' id='remarks-$order_id'>{$item['remarks']}</td>";
                            }
                            echo "</tr>";
                        }
                    }
                }
            } else {
                echo "<tr><td colspan='9'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    function updateRemarks(orderId, status) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_remarks.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById('remarks-' + orderId).textContent = status;
            } else {
                alert("Failed to update remarks.");
            }
        };
        xhr.send("order_id=" + orderId + "&remarks=" + status);
    }
</script>

</body>
</html>
