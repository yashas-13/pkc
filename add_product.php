<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $expiration = $_POST['expiration'];

    $stmt = $pdo->prepare('INSERT INTO products (name, quantity, price, expiration_date) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $quantity, $price, $expiration]);
    header('Location: inventory.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Add Product</h1>
<form method="post">
    <label>Name <input type="text" name="name" required></label>
    <label>Quantity <input type="number" name="quantity" required></label>
    <label>Price <input type="number" step="0.01" name="price" required></label>
    <label>Expiration Date <input type="date" name="expiration"></label>
    <input type="submit" value="Add">
</form>
<p><a href="inventory.php">Back to Inventory</a></p>
</body>
</html>
