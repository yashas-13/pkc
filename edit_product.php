<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: inventory.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $content = $_POST['content'];
    $packing = $_POST['packing'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $expiration = $_POST['expiration'];

    if (!is_numeric($quantity) || $quantity < 0 || $quantity > 1000000) {
        $error = 'Quantity must be a number between 0 and 1,000,000';
    } elseif (!is_numeric($price) || $price < 0 || $price > 100000) {
        $error = 'Price must be a number between 0 and 100,000';
    } else {
        $quantity = (int)$quantity;
        $price = (float)$price;
        $stmt = $pdo->prepare('UPDATE products SET name=?, content=?, packing=?, category=?, quantity=?, price=?, expiration_date=? WHERE id=?');
        $stmt->execute([$name, $content, $packing, $category, $quantity, $price, $expiration, $id]);
        header('Location: inventory.php');
        exit;
    }
}

if ($error) {
    $product = [
        'name' => $name,
        'content' => $content,
        'packing' => $packing,
        'category' => $category,
        'quantity' => $quantity,
        'price' => $price,
        'expiration_date' => $expiration
    ];
} else {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('Location: inventory.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Edit Product</h1>
<?php if ($error): ?>
<p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="post">
    <label>Name <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required></label>
    <label>Content <textarea name="content" rows="3" cols="40"><?php echo htmlspecialchars($product['content']); ?></textarea></label> <label>Packing <input type="text" name="packing" value="<?php echo htmlspecialchars($product['packing']); ?>"></label> <label>Category <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>"></label> <label>Quantity <input type="number" name="quantity" value="<?php echo htmlspecialchars($product['quantity']); ?>" required></label>
    <label>Price <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required></label>
    <label>Expiration Date <input type="date" name="expiration" value="<?php echo htmlspecialchars($product['expiration_date']); ?>"></label>
    <input type="submit" value="Save">
</form>
<p><a href="inventory.php">Back to Inventory</a></p>
</body>
</html>
