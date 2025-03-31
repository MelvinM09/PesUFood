<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Redirect if not logged in
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /PesUFood/login.php");
    exit();
}

// Handle file upload
function handleImageUpload() {
    $targetDir = __DIR__ . "/../uploads/dishes/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES["dish_image_upload"]["name"]);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image file is actual image
    $check = getimagesize($_FILES["dish_image_upload"]["tmp_name"]);
    if ($check === false) {
        throw new Exception("File is not an image.");
    }

    // Check file size (max 2MB)
    if ($_FILES["dish_image_upload"]["size"] > 2000000) {
        throw new Exception("Sorry, your file is too large (max 2MB).");
    }

    // Allow certain file formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES["dish_image_upload"]["tmp_name"], $targetFile)) {
        return "/PesUFood/uploads/dishes/" . $fileName;
    } else {
        throw new Exception("Sorry, there was an error uploading your file.");
    }
}

// Handle dish addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_dish'])) {
    try {
        $name = mysqli_real_escape_string($conn, $_POST['dish_name']);
        $price = floatval($_POST['dish_price']);
        $discount = !empty($_POST['dish_discount']) ? floatval($_POST['dish_discount']) : NULL; // Store as float or NULL

        // Validate discount
        if ($discount !== NULL && ($discount < 0 || $discount > 100)) {
            throw new Exception("Discount must be between 0 and 100.");
        }

        // Handle image input
        $image = '';
        if (!empty($_FILES['dish_image_upload']['name'])) {
            $image = handleImageUpload();
        } elseif (!empty($_POST['dish_image_url'])) {
            $image = mysqli_real_escape_string($conn, $_POST['dish_image_url']);
        } else {
            throw new Exception("Please provide either an image URL or upload an image.");
        }

        $query = "INSERT INTO dishes (name, price, discount, image) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdss", $name, $price, $discount, $image);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Dish added successfully!";
            header("Location: dishes.php");
            exit();
        } else {
            throw new Exception("Failed to add dish: " . $conn->error);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header("Location: dishes.php");
        exit();
    }
}

// Handle dish deletion
if (isset($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        $query = "DELETE FROM dishes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Dish deleted successfully!";
        } else {
            throw new Exception("Failed to delete dish: " . $conn->error);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    header("Location: dishes.php");
    exit();
}

// Handle dish modification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_dish'])) {
    try {
        $id = intval($_POST['dish_id']);
        $name = mysqli_real_escape_string($conn, $_POST['dish_name']);
        $price = floatval($_POST['dish_price']);
        $discount = !empty($_POST['dish_discount']) ? floatval($_POST['dish_discount']) : NULL;

        // Validate discount
        if ($discount !== NULL && ($discount < 0 || $discount > 100)) {
            throw new Exception("Discount must be between 0 and 100.");
        }

        // Get current image
        $current_image_query = "SELECT image FROM dishes WHERE id = ?";
        $stmt = $conn->prepare($current_image_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_image = $row['image'];
        
        // Determine which image to use
        $image = $current_image; // default to current image
        
        if (!empty($_FILES['dish_image_upload']['name'])) {
            $image = handleImageUpload();
        } elseif (!empty($_POST['dish_image_url'])) {
            $image = mysqli_real_escape_string($conn, $_POST['dish_image_url']);
        }

        $query = "UPDATE dishes SET name = ?, price = ?, discount = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdssi", $name, $price, $discount, $image, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Dish updated successfully!";
        } else {
            throw new Exception("Failed to update dish: " . $conn->error);
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    header("Location: dishes.php");
    exit();
}

// Fetch all dishes
$query = "SELECT * FROM dishes";
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
    <title>Manage Dishes</title>
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
        
        /* Fix for placeholder visibility in dark mode */
        .dark-mode .form-control::placeholder {
            color: #adb5bd;
            opacity: 1;
        }
        
        /* Common Styles */
        .img-thumbnail {
            max-width: 100px;
            max-height: 100px;
        }
        
        .nav-tabs .nav-link.active {
            font-weight: bold;
        }
        
        /* Ensure all text is visible */
        .text-muted {
            color: #6c757d !important;
        }
        
        .dark-mode .text-muted {
            color: #adb5bd !important;
        }
        
        /* Button colors */
        .btn-secondary {
            color: #212529;
        }
        
        .dark-mode .btn-secondary {
            color: #f8f9fa;
        }
        
        .btn-success,
        .btn-danger,
        .btn-primary {
            color: white;
        }
        
        .btn-warning {
            color: #212529;
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
                <li class="nav-item"><a class="nav-link" href="manage_orders.php"><i class="bi bi-receipt"></i> Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a></li>
                <li class="nav-item"><a class="nav-link active" href="dishes.php"><i class="bi bi-egg"></i> Dishes</a></li>
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
        <h2><i class="bi bi-egg"></i> Manage Dishes</h2>
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

    <!-- Add New Dish Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add New Dish</h5>
        </div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Dish Name</label>
                        <input type="text" name="dish_name" class="form-control" placeholder="e.g., Margherita Pizza" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price (₹)</label>
                        <input type="number" step="0.01" name="dish_price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Discount (%) </label>
                        <input type="number" step="0.1" name="dish_discount" class="form-control" placeholder="e.g., 10 for 10%">
                    </div>
                    <div class="col-md-3">
                        <ul class="nav nav-tabs" id="imageTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="url-tab" data-bs-toggle="tab" data-bs-target="#url" type="button">Image URL</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">Upload Image</button>
                            </li>
                        </ul>
                        <div class="tab-content p-3 border border-top-0 rounded-bottom">
                            <div class="tab-pane fade show active" id="url" role="tabpanel">
                                <input type="text" name="dish_image_url" class="form-control" placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="tab-pane fade" id="upload" role="tabpanel">
                                <input type="file" name="dish_image_upload" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2MB (JPG, PNG, GIF)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="add_dish" class="btn btn-success w-100">
                            <i class="bi bi-plus-lg"></i> Add Dish
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Dishes Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul"></i> Current Dishes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>₹<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['discount'] !== NULL ? number_format($row['discount'], 1) . '%' : 'None' ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="<?= htmlspecialchars($row['image']) ?>" class="img-thumbnail" alt="Dish Image">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this dish?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Dish: <?= htmlspecialchars($row['name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="dish_id" value="<?= $row['id'] ?>">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="dish_name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Price (₹)</label>
                                                        <input type="number" step="0.01" name="dish_price" class="form-control" value="<?= $row['price'] ?>" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Discount (%)</label>
                                                        <input type="number" step="0.1" name="dish_discount" class="form-control" value="<?= htmlspecialchars($row['discount'] ?? '') ?>" placeholder="e.g., 10 for 10%">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Current Image</label>
                                                        <div>
                                                            <?php if (!empty($row['image'])): ?>
                                                                <img src="<?= htmlspecialchars($row['image']) ?>" class="img-thumbnail mb-2" style="max-height: 100px;">
                                                            <?php else: ?>
                                                                <span class="text-muted">No image</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Update Image URL</label>
                                                        <input type="text" name="dish_image_url" class="form-control" placeholder="Leave blank to keep current">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Or Upload New Image</label>
                                                        <input type="file" name="dish_image_upload" class="form-control" accept="image/*">
                                                        <small class="text-muted">Max 2MB (JPG, PNG, GIF)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" name="update_dish" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Activate tab switching
    const imageTab = new bootstrap.Tab(document.getElementById('url-tab'));
    const uploadTab = new bootstrap.Tab(document.getElementById('upload-tab'));
</script>
</body>
</html>