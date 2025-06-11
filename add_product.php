<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
        $stmt = $pdo->prepare('INSERT INTO products (name, content, packing, category, quantity, price, expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $content, $packing, $category, $quantity, $price, $expiration]);
        header('Location: inventory.php');
        exit;
    }
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
<?php if ($error): ?>
<p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="post">
    <label>Name <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required></label>
    <label>Content <textarea name="content" rows="3" cols="40"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea></label> <label>Packing <input type="text" name="packing" value="<?php echo htmlspecialchars($_POST['packing'] ?? ''); ?>"></label> <label>Category <input type="text" name="category" value="<?php echo htmlspecialchars($_POST['category'] ?? ''); ?>"></label> <label>Quantity <input type="number" name="quantity" value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>" required></label>
    <label>Price <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required></label>
    <label>Expiration Date <input type="date" name="expiration" value="<?php echo htmlspecialchars($_POST['expiration'] ?? ''); ?>"></label>
    <input type="submit" value="Add">
</form>
<p><a href="inventory.php">Back to Inventory</a></p>
</body>
</html>
