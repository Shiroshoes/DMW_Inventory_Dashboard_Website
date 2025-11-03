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
$role = $_SESSION['role'] ?? 'Cashier';
$records = $data['records'];

$pdo->beginTransaction();

try {
  $record_date = null;

  foreach ($records as $rec) {
    $record_date = $rec['record_date'] ?? $record_date;
    $record_user_id = $rec['user_id'] ?? $user_id; // Use record’s user_id if provided


    if ($role === 'Admin') {
      $stmt = $pdo->prepare("UPDATE daily_records 
                                   SET item_name=?, category=?, quantity=?, price=?, total=? 
                                   WHERE record_id=?");
      $stmt->execute([
        $rec['item_name'],
        $rec['category'],
        $rec['quantity'],
        $rec['price'],
        floatval($rec['quantity']) * floatval($rec['price']),
        $rec['record_id']
      ]);
    } else {
      $stmt = $pdo->prepare("UPDATE daily_records 
                                   SET item_name=?, category=?, quantity=?, price=?, total=? 
                                   WHERE record_id=? AND user_id=?");
      $stmt->execute([
        $rec['item_name'],
        $rec['category'],
        $rec['quantity'],
        $rec['price'],
        floatval($rec['quantity']) * floatval($rec['price']),
        $rec['record_id'],
        $user_id
      ]);
    }
  }


  $targetUser = ($role === 'Admin' && isset($record_user_id)) ? $record_user_id : $user_id;

  $stmt = $pdo->prepare("SELECT COUNT(*) AS total_entries, COALESCE(SUM(total), 0) AS total_amount 
                           FROM daily_records 
                           WHERE user_id=? AND record_date=?");
  $stmt->execute([$targetUser, $record_date]);
  $totals = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $pdo->prepare("UPDATE activity_log 
                           SET total_entries=?, total_amount=? 
                           WHERE user_id=? AND record_date=?");
  $stmt->execute([
    $totals['total_entries'],
    $totals['total_amount'],
    $targetUser,
    $record_date
  ]);

  $pdo->commit();
  echo json_encode(['success' => true]);
} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
