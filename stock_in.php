<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$selectedProduct = (int)($_GET['product_id'] ?? 0);
$products = $pdo->query('SELECT id, name FROM products ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? $selectedProduct);
    $amount = (int)($_POST['amount'] ?? 0);
    $reason = $_POST['reason'] ?? 'purchase';

    if ($product_id <= 0 || $amount <= 0) {
        $error = 'Select a product and enter a positive amount';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO stock_movements (product_id, amount, reason) VALUES (?, ?, ?)');
            $stmt->execute([$product_id, $amount, $reason]);
            $stmt = $pdo->prepare('UPDATE products SET quantity = quantity + ? WHERE id = ?');
            $stmt->execute([$amount, $product_id]);
            $pdo->commit();
            header('Location: inventory.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Record Incoming Stock</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Record Incoming Stock</h1>
<?php if ($error): ?>
<p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
<form method="post">
    <label>Product
        <select name="product_id">
            <option value="">--Select--</option>
            <?php foreach ($products as $p): ?>
            <option value="<?php echo $p['id']; ?>" <?php
                $current = (int)($_POST['product_id'] ?? $selectedProduct);
                if ($current === (int)$p['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($p['name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Amount <input type="number" name="amount" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required></label>
    <label>Reason <input type="text" name="reason" value="<?php echo htmlspecialchars($_POST['reason'] ?? 'purchase'); ?>"></label>
    <input type="submit" value="Record">
</form>
<p><a href="inventory.php">Back to Inventory</a></p>
</body>
</html>
