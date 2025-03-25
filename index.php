<?php
session_start();
include_once "connection/connect.php"; // Ensure DB connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch user details if logged in
$user_name = 'Guest';
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $email = $_SESSION['user_email'];
    $query = "SELECT name FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $user_name = htmlspecialchars($user['name']);
    }
}

// Fetch menu items from database (Modify this part to match your DB structure)
$menu_items = [
    ['name' => 'Juicy Burger', 'price' => 8.99, 'discount' => 'Up to 20% off', 'image' => 'assets/images/burger.jpg'],
    ['name' => 'Cheesy Pizza', 'price' => 12.99, 'discount' => 'Buy 1 Get 1 Free', 'image' => 'assets/images/pizza.jpg'],
    ['name' => 'Spicy Noodles', 'price' => 7.99, 'discount' => '15% off', 'image' => 'assets/images/noodles.jpg']
];

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $item_name = $_GET['name'] ?? '';

    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $item_name) {
            if ($action === 'increase') {
                $cart_item['quantity']++;
            } elseif ($action === 'decrease' && $cart_item['quantity'] > 1) {
                $cart_item['quantity']--;
            } elseif ($action === 'remove') {
                $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($item) => $item['name'] !== $item_name);
            }
            break;
        }
    }
    unset($cart_item); // Unset reference to avoid issues
}

// Handle adding items to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];

    // Check if item already in cart, then increase quantity
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $item_name) {
            $cart_item['quantity']++;
            $found = true;
            break;
        }
    }
    unset($cart_item);

    if (!$found) {
        $_SESSION['cart'][] = ['name' => $item_name, 'price' => $item_price, 'quantity' => 1];
    }

    $success_message = "Item added to cart!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        .navbar-brand { font-family: 'Pacifico', cursive; font-size: 1.5rem; }
        .main-section { background: url('assets/images/New.jpg') no-repeat center center/cover; background-size: cover; background-attachment: fixed; min-height: 100px; color: black; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
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
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) { ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="registration.php">Register</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Section -->
<section class="main-section text-center py-5">
    <h1 class="display-4 fw-bold" style="font-family: Georgia, serif;">Order Delivery & Take-Out</h1>
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) { ?>
        <h2 class="display-5 fw-bold">Welcome, <?php echo $user_name; ?>!</h2>
    <?php } ?>
</section>

<!-- Popular Dishes Section -->
<section class="container my-5">
    <?php if (isset($success_message)) { echo '<div class="alert alert-success text-center">'.$success_message.'</div>'; } ?>

    <h2 class="text-center mb-4">Popular Dishes of the Month</h2>
    <div class="row justify-content-center">
        <?php foreach ($menu_items as $item) { ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $item['image']; ?>" class="card-img-top" alt="<?php echo $item['name']; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo $item['name']; ?></h5>
                        <p class="card-text"><?php echo $item['discount']; ?></p>
                        <p class="card-text">Price: $<?php echo number_format($item['price'], 2); ?></p>
                        <form method="post">
                            <input type="hidden" name="item_name" value="<?php echo $item['name']; ?>">
                            <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Cart Summary -->
<?php if (!empty($_SESSION['cart'])) { ?>
    <h3 class="text-center mt-5">Your Cart</h3>
    <div class="card shadow-lg">
        <div class="card-body">
            <ul class="list-group">
                <?php foreach ($_SESSION['cart'] as $cart_item) { ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?php echo htmlspecialchars($cart_item['name']); ?></span>

                        <!-- Quantity Selector beside Remove button -->
                        <div class="d-flex align-items-center">
                            <div class="input-group me-3" style="width: 120px;">
                                <a href="?action=decrease&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-outline-secondary">−</a>
                                <input type="text" class="form-control text-center" value="<?php echo $cart_item['quantity']; ?>" readonly>
                                <a href="?action=increase&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-outline-secondary">+</a>
                            </div>

                            <!-- Remove Item -->
                            <a href="?action=remove&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-outline-danger">Remove</a>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <a href="Check_out.php" class="btn btn-primary mt-3 w-100">Proceed to Checkout</a>
        </div>
    </div>
<?php } ?>


