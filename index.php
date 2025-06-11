<?php require 'config.php';
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
    <p>Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?> | <a href="logout.php">Logout</a></p>
    <ul>
        <li><a href="inventory.php">View Inventory</a></li>
        <li><a href="add_product.php">Add Product</a></li>
        <li><a href="stock_in.php">Record Incoming Stock</a></li>
        <li><a href="stock_out.php">Record Outgoing Stock</a></li>
    </ul>
</body>
</html>
