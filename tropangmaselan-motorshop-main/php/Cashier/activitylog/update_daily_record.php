<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$host = 'localhost';
$dbname = 'tropangmaselandb';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['records'])) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$user_id = $_SESSION['user_id'];
$records = $data['records'];

// Start transaction
$pdo->beginTransaction();

try {
    $record_date = null;

    foreach ($records as $rec) {
        $record_date = $rec['record_date'] ?? $record_date; // capture the date from first record if exists

        $stmt = $pdo->prepare("UPDATE daily_records 
                               SET item_name=?, category=?, quantity=?, price=?, total=? 
                               WHERE record_id=? AND user_id=?");

        $total = floatval($rec['quantity']) * floatval($rec['price']);
        $stmt->execute([
            $rec['item_name'],
            $rec['category'],
            $rec['quantity'],
            $rec['price'],
            $total,
            $rec['record_id'],
            $user_id
        ]);
    }

    // Recalculate totals for the day
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_entries, SUM(total) AS total_amount 
                           FROM daily_records 
                           WHERE user_id=? AND record_date=?");
    $stmt->execute([$user_id, $record_date]);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update activity_log table
    $stmt = $pdo->prepare("UPDATE activity_log 
                           SET total_entries=?, total_amount=? 
                           WHERE user_id=? AND record_date=?");
    $stmt->execute([
        $totals['total_entries'],
        $totals['total_amount'],
        $user_id,
        $record_date
    ]);

    $pdo->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
