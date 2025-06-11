<?php
require 'config.php';

// Ensure session is started, if not already handled in config.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pharma Inventory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Pharma Inventory Management</h1>
    <?php if (isset($_SESSION['username'])): ?>
        <p>Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    <?php endif; ?>
    <ul>
        <li><a href="inventory.php">View Inventory</a></li>
        <li><a href="add_product.php">Add Product</a></li>
        <li><a href="stock_in.php">Record Incoming Stock</a></li>
        <li><a href="stock_out.php">Record Outgoing Stock</a></li>
    </ul>
</body>
</html>