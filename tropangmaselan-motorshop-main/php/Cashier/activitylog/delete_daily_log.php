<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$log_id = $data['log_id'];
$user_id = $_SESSION['user_id'];

$pdo = new PDO("mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Get record_date
$stmt = $pdo->prepare("SELECT record_date FROM activity_log WHERE log_id=? AND user_id=?");
$stmt->execute([$log_id, $user_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$record) {
    echo json_encode(['success' => false]);
    exit;
}
$record_date = $record['record_date'];

// Delete all daily_records for that day
$stmt = $pdo->prepare("DELETE FROM daily_records WHERE user_id=? AND record_date=?");
$stmt->execute([$user_id, $record_date]);

// Delete from activity_log
$stmt = $pdo->prepare("DELETE FROM activity_log WHERE user_id=? AND record_date=?");
$stmt->execute([$user_id, $record_date]);

echo json_encode(['success' => true]);
