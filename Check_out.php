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
    header("Location: Check_out.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
                <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Order Summary -->
<section class="container my-5">
    <h2 class="text-center">Your Order</h2>
    <?php if (!empty($_SESSION['cart'])) { ?>
        <div class="card shadow-lg">
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($_SESSION['cart'] as $name => $cart_item) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?php echo htmlspecialchars($cart_item['name']); ?></span>

                            <!-- Quantity Selector beside Remove button -->
                            <div class="d-flex align-items-center">
                                <div class="input-group me-3" style="width: 120px;">
                                    <a href="?action=decrease&name=<?php echo urlencode($name); ?>" class="btn btn-outline-secondary">−</a>
                                    <input type="text" class="form-control text-center" value="<?php echo $cart_item['quantity']; ?>" readonly>
                                    <a href="?action=increase&name=<?php echo urlencode($name); ?>" class="btn btn-outline-secondary">+</a>
                                </div>

                                <!-- Remove Item -->
                                <a href="?action=remove&name=<?php echo urlencode($name); ?>" class="btn btn-outline-danger">Remove</a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                <!-- Proceed to Checkout Button -->
                <div class="text-center mt-4">
                    <a href="order_confirm.php" class="btn btn-success btn-lg">Proceed to Checkout</a>
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
