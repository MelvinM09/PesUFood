<?php
require_once('../config/config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['id'])) {
        echo "Error: Missing user ID.";
        exit();
    }

    $id = intval($_POST['id']);

    if (!$conn) {
        echo "Database connection failed: " . mysqli_connect_error();
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "User deleted successfully!";
    } else {
        echo "Error deleting user: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
