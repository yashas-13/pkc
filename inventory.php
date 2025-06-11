<?php
require 'config.php';
$stmt = $pdo->query('SELECT * FROM products');
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Inventory</h1>
<p><a href="add_product.php">Add Product</a> | <a href="index.php">Home</a></p>
<table>
    <tr>
        <th>ID</th><th>Name</th><th>Quantity</th><th>Price</th><th>Expiration</th><th>Actions</th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?php echo htmlspecialchars($product['id']); ?></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
        <td><?php echo htmlspecialchars($product['price']); ?></td>
        <td><?php echo htmlspecialchars($product['expiration_date']); ?></td>
        <td>
            <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
            <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
