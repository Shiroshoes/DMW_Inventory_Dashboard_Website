<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

$saved = [];

try {
    $stmt = $pdo->prepare("INSERT INTO daily_records (user_id, record_date, category, item_name, quantity, price, total)
        VALUES (:user_id, :record_date, :category, :item_name, :quantity, :price, :total)");

    foreach ($input as $row) {
        $record_date = $row['date'];
        $category = $row['category'];
        $item_name = $row['item'];
        $quantity = (float)$row['qty'];
        $price = (float)$row['price'];
        $total = $quantity * $price;

        $stmt->execute([
            ':user_id' => $user_id,
            ':record_date' => $record_date,
            ':category' => $category,
            ':item_name' => $item_name,
            ':quantity' => $quantity,
            ':price' => $price,
            ':total' => $total
        ]);

        $saved[] = [
            'record_date' => $record_date,
            'category' => $category,
            'item_name' => $item_name,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $total
        ];
    }

    echo json_encode(['success' => true, 'saved' => $saved]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
