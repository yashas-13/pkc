<?php
require 'config.php';

// Initialize search variable and prepare statement based on search query
$search = $_GET['search'] ?? '';
if ($search !== '') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE name LIKE ? OR category LIKE ?');
    $stmt->execute(['%' . $search . '%', '%' . $search . '%']);
} else {
    $stmt = $pdo->query('SELECT * FROM products');
}
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

<form method="get" style="margin-bottom: 10px;">
    <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
    <a href="inventory.php">Clear</a>
</form>

<p><a href="add_product.php">Add Product</a> | <a href="index.php">Home</a></p>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Content</th>
        <th>Packing</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Expiration</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?php echo htmlspecialchars($product['id']); ?></td>
        <td><?php echo htmlspecialchars($product['name']); ?></td>
        <td><?php echo nl2br(htmlspecialchars($product['content'])); ?></td>
        <td><?php echo htmlspecialchars($product['packing']); ?></td>
        <td><?php echo htmlspecialchars($product['category']); ?></td>
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