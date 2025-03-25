<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

include_once "connection/connect.php"; // Ensure DB connection

// Fetch user details from the database
$email = $_SESSION['user_email'];
$query = "SELECT name FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_name = $user['name'] ?? 'Guest'; // Default to 'Guest' if name is missing

// Sample menu items (in a real application, this would come from a database)
$menu_items = [
    [
        'name' => 'Juicy Burger',
        'price' => 8.99,
        'discount' => 'Up to 20% off',
        'image' => 'assets/images/burger.png'
    ],
    [
        'name' => 'Pepperoni Pizza',
        'price' => 12.99,
        'discount' => 'Buy 1 Get 1 Free',
        'image' => 'assets/images/pizza.png'
    ],
    [
        'name' => 'Spicy Noodles',
        'price' => 7.99,
        'discount' => 'Flat 10% Off',
        'image' => 'assets/images/noodles.png'
    ]
];

// Handle adding items to cart (basic implementation)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    
    // Initialize cart in session if not already set
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add item to cart
    $_SESSION['cart'][] = [
        'name' => $item_name,
        'price' => $item_price,
        'quantity' => 1 // You can add quantity selection later
    ];
    
    // Optional: Show a success message
    $success_message = "Item added to cart!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PesUFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        body {
            background: url('assets/images/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .navbar-brand {
            font-family: 'Pacifico', cursive;
            font-size: 1.5rem;
        }
        .card {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .menu-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        .menu-item .card-body {
            text-align: center;
        }
        .btn-add-to-cart {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-add-to-cart:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">PesUFood</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="text-center text-white">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p class="text-center text-white">Your email: <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>

        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php } ?>

        <h3 class="text-white mt-4">Popular Dishes of the Month</h3>
        <div class="row">
            <?php foreach ($menu_items as $item) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card menu-item">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $item['name']; ?></h4>
                            <p class="card-text">Price: $<?php echo number_format($item['price'], 2); ?></p>
                            <p class="card-text text-success"><?php echo $item['discount']; ?></p>
                            <form method="post">
                                <input type="hidden" name="item_name" value="<?php echo $item['name']; ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-add-to-cart w-100">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Cart Summary (Optional) -->
        <?php if (!empty($_SESSION['cart'])) { ?>
            <h3 class="text-white mt-4">Your Cart</h3>
            <div class="card">
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        $total = 0;
                        foreach ($_SESSION['cart'] as $cart_item) {
                            $subtotal = $cart_item['price'] * $cart_item['quantity'];
                            $total += $subtotal;
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $cart_item['name']; ?> (x<?php echo $cart_item['quantity']; ?>)
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </li>
                        <?php } ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Total</strong>
                            <span><strong>$<?php echo number_format($total, 2); ?></strong></span>
                        </li>
                    </ul>
                    <a href="checkout.php" class="btn btn-primary w-100 mt-3">Proceed to Checkout</a>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>