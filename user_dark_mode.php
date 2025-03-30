<?php
session_start();
require_once('config/config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$email = $_SESSION['user_email'];
$input = json_decode(file_get_contents("php://input"), true);
$dark_mode = isset($input['dark_mode']) ? (int)$input['dark_mode'] : 0;

$query = "UPDATE users SET dark_mode = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $dark_mode, $email);
if ($stmt->execute()) {
    $_SESSION['DARK_MODE'] = $dark_mode;
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database update failed"]);
}
$stmt->close();
