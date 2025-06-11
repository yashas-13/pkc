<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
            $stmt->execute([$_GET['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($product ?: []);
        } else {
            $stmt = $pdo->query('SELECT * FROM products');
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $stmt = $pdo->prepare('INSERT INTO products (name, content, packing, category, quantity, price, expiration_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $data['name'] ?? '',
            $data['content'] ?? '',
            $data['packing'] ?? '',
            $data['category'] ?? '',
            (int)($data['quantity'] ?? 0),
            (float)($data['price'] ?? 0),
            $data['expiration_date'] ?? null
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$id || !$data) { http_response_code(400); echo json_encode(['error'=>'Missing id or data']); break; }
        $stmt = $pdo->prepare('UPDATE products SET name=?, content=?, packing=?, category=?, quantity=?, price=?, expiration_date=? WHERE id=?');
        $stmt->execute([
            $data['name'] ?? '',
            $data['content'] ?? '',
            $data['packing'] ?? '',
            $data['category'] ?? '',
            (int)($data['quantity'] ?? 0),
            (float)($data['price'] ?? 0),
            $data['expiration_date'] ?? null,
            $id
        ]);
        echo json_encode(['updated' => $stmt->rowCount()]);
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) { http_response_code(400); echo json_encode(['error'=>'Missing id']); break; }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
        $stmt->execute([$id]);
        echo json_encode(['deleted' => $stmt->rowCount()]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
