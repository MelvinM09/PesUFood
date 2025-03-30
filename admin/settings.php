<?php
session_start();
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /PesUFood/admin/login.php");
    exit();
}

require_once('../config/config.php');

$default_settings = [
    "site_name" => "PesUFood",
    "admin_email" => "admin@pesufood.com",
    "site_logo" => "",
    "contact_info" => "",
    "dark_mode" => "0"
];

$query = "SELECT * FROM settings";
$result = mysqli_query($conn, $query);
$settings = $default_settings;

while ($row = mysqli_fetch_assoc($result)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - PesUFood</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body { background-color: <?= ($settings['dark_mode'] == '1') ? '#121212' : '#f8f9fa'; ?>; color: <?= ($settings['dark_mode'] == '1') ? '#ffffff' : '#000000'; ?>; padding: 20px; }
        .container { max-width: 700px; background: <?= ($settings['dark_mode'] == '1') ? '#333' : '#fff'; ?>; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px #ccc; }
        .form-control, .form-select { margin-bottom: 10px; }
        .btn-primary { width: 100%; }
        .alert { display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Website Settings</h2>
    <div class="alert alert-success"></div>

    <form id="settings-form">
        <label>Site Name:</label>
        <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name']) ?>" required>

        <label>Admin Email:</label>
        <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($settings['admin_email']) ?>" required>

        <label>Site Logo URL:</label>
        <input type="text" name="site_logo" class="form-control" value="<?= htmlspecialchars($settings['site_logo']) ?>">

        <label>Contact Info:</label>
        <input type="text" name="contact_info" class="form-control" value="<?= htmlspecialchars($settings['contact_info']) ?>">

        <label>Dark Mode:</label>
        <select name="dark_mode" class="form-select">
            <option value="1" <?= ($settings['dark_mode'] == "1") ? "selected" : "" ?>>Enabled</option>
            <option value="0" <?= ($settings['dark_mode'] == "0") ? "selected" : "" ?>>Disabled</option>
        </select>

        <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
    </form>
</div>

<script>
$(document).ready(function () {
    $("#settings-form").on("change", "input, select", function () {
        var key = $(this).attr("name");
        var value = $(this).val();

        $.post("update_settings.php", { key: key, value: value }, function (response) {
            $(".alert-success").html(response).fadeIn().delay(2000).fadeOut();
            if (key === "dark_mode") {
                location.reload();
            }
        });
    });
});
</script>

</body>
</html>