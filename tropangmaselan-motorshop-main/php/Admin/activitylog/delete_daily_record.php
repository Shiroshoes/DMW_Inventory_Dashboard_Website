<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$record_id = $data['record_id'];
$user_id = $_SESSION['user_id'];

$pdo = new PDO("mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4", 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Get the record date before deletion
$stmt = $pdo->prepare("SELECT record_date FROM daily_records WHERE record_id=? AND user_id=?");
$stmt->execute([$record_id, $user_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$record) {
    echo json_encode(['success' => false]);
    exit;
}
$record_date = $record['record_date'];

// Delete the record
$stmt = $pdo->prepare("DELETE FROM daily_records WHERE record_id=? AND user_id=?");
if ($stmt->execute([$record_id, $user_id])) {
    // Check if any records remain for the day
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM daily_records WHERE user_id=? AND record_date=?");
    $stmt2->execute([$user_id, $record_date]);
    if ($stmt2->fetchColumn() == 0) {
        // Delete day from activity_log
        $stmt3 = $pdo->prepare("DELETE FROM activity_log WHERE user_id=? AND record_date=?");
        $stmt3->execute([$user_id, $record_date]);
    } else {
        // Update activity_log totals
        $stmt3 = $pdo->prepare("UPDATE activity_log SET total_entries=(SELECT COUNT(*) FROM daily_records WHERE user_id=? AND record_date=?), total_amount=(SELECT SUM(total) FROM daily_records WHERE user_id=? AND record_date=?) WHERE user_id=? AND record_date=?");
        $stmt3->execute([$user_id, $record_date, $user_id, $record_date, $user_id, $record_date]);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
