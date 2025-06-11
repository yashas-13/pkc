<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $content = $_POST['content']; // Added from x93hgc-codex/create-inventory-management-software-for-pharma-manufacturer
    $packing = $_POST['packing']; // Added from x93hgc-codex/create-inventory-management-software-for-pharma-manufacturer
    $category = $_POST['category']; // Added from x93hgc-codex/create-inventory-management-software-for-pharma-manufacturer
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $expiration = $_POST['expiration'];

    // Merged INSERT statement to include new fields
    $stmt = $pdo->prepare('INSERT INTO products (name, content, packing, category, quantity, price, expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $content, $packing, $category, $quantity, $price, $expiration]);
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
    <label>Content <textarea name="content" rows="3" cols="40"></textarea></label> <label>Packing <input type="text" name="packing"></label> <label>Category <input type="text" name="category"></label> <label>Quantity <input type="number" name="quantity" required></label>
    <label>Price <input type="number" step="0.01" name="price" required></label>
    <label>Expiration Date <input type="date" name="expiration"></label>
    <input type="submit" value="Add">
</form>
<p><a href="inventory.php">Back to Inventory</a></p>
</body>
</html>