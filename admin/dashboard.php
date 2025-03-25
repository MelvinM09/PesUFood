<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>
<p>Your email: <?php echo $_SESSION['user_email']; ?></p>
<a href="logout.php">Logout</a>
