<?php
session_start();
include_once "connection/connect.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if maintenance mode is enabled
$query = "SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

// Store the current requested page (excluding maintenance.php itself)
$current_page = basename($_SERVER['PHP_SELF']);
if ($current_page !== 'maintenance.php') {
    $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
}

if ($row && $row['setting_value'] === '1') {
    header("Location: maintenance.php");
    exit();
}

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

// Fetch dishes from database
$query = "SELECT * FROM dishes";
$result = mysqli_query($conn, $query);
$menu_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $menu_items[] = [
        'name' => $row['name'],
        'price' => $row['price'],
        'discount' => $row['discount'] ?? 'No discount',
        'image' => $row['image'] ?? 'assets/images/default.jpg'
    ];
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

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
    unset($cart_item);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
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
        body.dark-mode { background-color: #121212; color: white; }
        .dark-mode .navbar { background-color: #222 !important; }
        .dark-mode .card { background-color: #333; color: white; }
        .dark-mode .table { background-color: #444; color: white; }
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
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) { ?>
                    <li class="nav-item"><a class="nav-link" href="Check_out.php">Checkout</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="registration.php">Register</a></li>
                <?php } ?>
                <li class="nav-item">
                    <button class="btn btn-outline-light ms-3" id="darkModeToggle">🌙 Dark Mode</button>
                </li>
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
                        <p class="card-text">Price: ₹<?php echo number_format($item['price'], 2); ?></p>
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
<?php if (!empty($_SESSION['cart'])) {
    $total_price = 0;
?>
    <h3 class="text-center mt-5 fw-bold">🛒 Your Cart</h3>
    <div class="card shadow-lg p-4">
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
                    <?php foreach ($_SESSION['cart'] as $cart_item) {
                        $item_total = $cart_item['price'] * $cart_item['quantity'];
                        $total_price += $item_total;
                    ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($cart_item['name']); ?></td>
                            <td>₹<?php echo number_format($cart_item['price'], 2); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="?action=decrease&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-sm btn-outline-secondary">−</a>
                                    <input type="text" class="form-control text-center mx-2" value="<?php echo $cart_item['quantity']; ?>" readonly style="width: 40px;">
                                    <a href="?action=increase&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-sm btn-outline-secondary">+</a>
                                </div>
                            </td>
                            <td>₹<?php echo number_format($item_total, 2); ?></td>
                            <td>
                                <a href="?action=remove&name=<?php echo urlencode($cart_item['name']); ?>" class="btn btn-sm btn-outline-danger">🗑 Remove</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="text-end mt-3">
                <h4 class="fw-bold">Total Price: ₹<?php echo number_format($total_price, 2); ?></h4>
            </div>
            <a href="Check_out.php" class="btn btn-lg btn-primary w-100 mt-3">Proceed to Checkout</a>
        </div>
    </div>
<?php } ?>

<!-- Dark Mode Script -->
<script>
    const darkModeToggle = document.getElementById("darkModeToggle");
    const body = document.body;

    if (localStorage.getItem("dark-mode") === "enabled") {
        body.classList.add("dark-mode");
        darkModeToggle.textContent = "☀ Light Mode";
    }

    darkModeToggle.addEventListener("click", () => {
        const isDarkMode = body.classList.toggle("dark-mode");
        localStorage.setItem("dark-mode", isDarkMode ? "enabled" : "disabled");
        darkModeToggle.textContent = isDarkMode ? "☀ Light Mode" : "🌙 Dark Mode";

        <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) { ?>
        fetch('user_dark_mode.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dark_mode: isDarkMode ? 1 : 0 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                console.error('Failed to save dark mode:', data.message);
            }
        })
        .catch(err => console.error('Error:', err));
        <?php } ?>
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>