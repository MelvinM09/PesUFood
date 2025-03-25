<?php
session_start();

// Handle cart actions (increase, decrease, remove)
if (isset($_GET['action']) && isset($_GET['name'])) {
    $item_name = urldecode($_GET['name']);

    if ($_GET['action'] == 'increase') {
        $_SESSION['cart'][$item_name]['quantity']++;
    } elseif ($_GET['action'] == 'decrease' && $_SESSION['cart'][$item_name]['quantity'] > 1) {
        $_SESSION['cart'][$item_name]['quantity']--;
    } elseif ($_GET['action'] == 'remove') {
        unset($_SESSION['cart'][$item_name]);
    }

    // Redirect to prevent multiple form submissions
    header("Location: check_out.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">PesUFood</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="check_out.php">Checkout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Order Summary -->
<section class="container my-5">
    <h2 class="text-center fw-bold">🛍 Your Order</h2>
    <?php if (!empty($_SESSION['cart'])) { 
        $total_price = 0; // Initialize total price
    ?>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $name => $cart_item) { 
                            $item_total = $cart_item['price'] * $cart_item['quantity']; 
                            $total_price += $item_total; 
                        ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($cart_item['name']); ?></td>
                                <td>₹<?php echo number_format($cart_item['price'], 2); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="?action=decrease&name=<?php echo urlencode($name); ?>" class="btn btn-sm btn-outline-secondary">−</a>
                                        <input type="text" class="form-control text-center mx-2" value="<?php echo $cart_item['quantity']; ?>" readonly style="width: 40px;">
                                        <a href="?action=increase&name=<?php echo urlencode($name); ?>" class="btn btn-sm btn-outline-secondary">+</a>
                                    </div>
                                </td>
                                <td>₹<?php echo number_format($item_total, 2); ?></td>
                                <td>
                                    <a href="?action=remove&name=<?php echo urlencode($name); ?>" class="btn btn-sm btn-outline-danger">🗑 Remove</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Total Price Display -->
                <div class="text-end mt-3">
                    <h4 class="fw-bold">Total: ₹<?php echo number_format($total_price, 2); ?></h4>
                </div>

                <!-- Proceed to Checkout Button -->
                <div class="text-center mt-4">
                    <a href="order_confirm.php" class="btn btn-lg btn-success">✅ Place Order</a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center mt-4">No items in your order!</div>
    <?php } ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
