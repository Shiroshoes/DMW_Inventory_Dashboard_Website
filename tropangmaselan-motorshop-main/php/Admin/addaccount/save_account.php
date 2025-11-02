<?php
session_start();

// --- Only Admin access ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

// --- Database connection ---
$pdo = new PDO(
    "mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4",
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// --- Determine action ---
$action = $_POST['action'] ?? '';

try {
    if ($action === 'save') {
        // --- Save new account ---
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $fullname = trim($_POST['fullname']);
        $role = $_POST['role'];

        if (!$username || !$password || !$fullname || !$role) throw new Exception("Fill all required fields.");

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, fullname, role) VALUES (?,?,?,?)");
        $stmt->execute([$username, $passwordHash, $fullname, $role]);

        echo json_encode(['success' => true]);
    } elseif ($action === 'update') {
        // --- Update existing account ---
        $user_id = $_POST['user_id'];
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $fullname = trim($_POST['fullname']);
        $role = $_POST['role'];

        if (!$user_id || !$username || !$fullname || !$role) throw new Exception("Fill all required fields.");

        if (!empty($password)) {
            // Update with new password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username=?, password_hash=?, fullname=?, role=? WHERE user_id=?");
            $stmt->execute([$username, $passwordHash, $fullname, $role, $user_id]);
        } else {
            // Update without changing password
            $stmt = $pdo->prepare("UPDATE users SET username=?, fullname=?, role=? WHERE user_id=?");
            $stmt->execute([$username, $fullname, $role, $user_id]);
        }

        echo json_encode(['success' => true]);
    } elseif ($action === 'delete') {
        // --- Delete account ---
        $user_id = $_POST['user_id'];
        if (!$user_id) throw new Exception("Invalid user ID.");

        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->execute([$user_id]);

        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
