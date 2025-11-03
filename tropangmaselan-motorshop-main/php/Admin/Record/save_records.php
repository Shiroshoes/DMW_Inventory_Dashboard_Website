<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4", 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];

if (!$input || !is_array($input)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

$saved = [];

try {
    $pdo->beginTransaction();

    // --- Insert to daily_records ---
    $stmt = $pdo->prepare("INSERT INTO daily_records (user_id, record_date, category, item_name, quantity, price, total)
                           VALUES (:user_id, :record_date, :category, :item_name, :quantity, :price, :total)");

    $logTotals = []; // To accumulate per date

    foreach ($input as $row) {
        $record_date = $row['record_date'];
        $category = $row['category'];
        $item_name = $row['item_name'];
        $quantity = (float)$row['quantity'];
        $price = (float)$row['price'];
        $total = (float)$row['total'];

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

        // --- Collect totals per date ---
        if (!isset($logTotals[$record_date])) {
            $logTotals[$record_date] = ['entries' => 0, 'amount' => 0];
        }
        $logTotals[$record_date]['entries'] += 1;
        $logTotals[$record_date]['amount'] += $total;
    }

    // --- Insert or update activity_log ---
    $logStmtInsert = $pdo->prepare("
        INSERT INTO activity_log (user_id, record_date, total_entries, total_amount)
        VALUES (:user_id, :record_date, :total_entries, :total_amount)
        ON DUPLICATE KEY UPDATE 
            total_entries = total_entries + VALUES(total_entries),
            total_amount = total_amount + VALUES(total_amount),
            updated_at = CURRENT_TIMESTAMP()
    ");

    foreach ($logTotals as $date => $totals) {
        $logStmtInsert->execute([
            ':user_id' => $user_id,
            ':record_date' => $date,
            ':total_entries' => $totals['entries'],
            ':total_amount' => $totals['amount']
        ]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'saved' => $saved]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
