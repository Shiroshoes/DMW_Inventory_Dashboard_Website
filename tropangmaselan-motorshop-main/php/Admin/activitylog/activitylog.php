<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: /website/tropangmaselan-motorshop-main/php/login/login.php");
  exit();
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
  die("Database connection failed");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'Cashier'; // Default to Cashier if role not set

// Optional date filter
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Base query
$query = "SELECT log_id, user_id, record_date, total_entries, total_amount FROM activity_log WHERE 1=1";
$params = [];

// Apply role-based restriction
if ($role !== 'Admin') {
  $query .= " AND user_id=?";
  $params[] = $user_id;
}

// Apply date filter
if ($start_date && $end_date) {
  $query .= " AND record_date BETWEEN ? AND ?";
  $params[] = $start_date;
  $params[] = $end_date;
}

// Order newest first
$query .= " ORDER BY record_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Log</title>
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.css">
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" />
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">
</head>

<body class="Log-In-Pov-body">

  <header class="main-header">
    <div class="logo">
      <img src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo" style="height:auto; width:120px;">
    </div>
    <div class="menu-toggle">☰</div>
    <nav>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Home/html/home.php">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Record/record.php">Record</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/dashboard/dashboard.php">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.php" class="active">Activity Log</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/addaccount/staffactivity.php">Account List</a>
    </nav>
    <div class="header-actions">
      <span>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <button class="logout-btn" onclick="if(confirm('Logout?')) window.location.href='/website/tropangmaselan-motorshop-main/php/logout.php'">Logout</button>
    </div>
  </header>

  <main class="Log-In-Pov-content">
    <section class="Log-In-Pov-list-section" style="flex:1 1 100%;">
      <h2>Activity Log</h2>

      <form method="get" style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">
        <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        <button type="submit" style="background:#2a508c;color:white;border:none;border-radius:25px;padding:8px 20px;font-weight:600;cursor:pointer;">Filter</button>
        <a href="<?= $_SERVER['PHP_SELF'] ?>" style="background:#3467a1;color:white;border:none;border-radius:25px;padding:8px 20px;text-decoration:none;font-weight:600;">Reset</a>
      </form>

      <table>
        <thead>
          <tr>
            <?php if ($role === 'Admin') echo '<th>User ID</th>'; ?>
            <th>Date</th>
            <th>Total Entries</th>
            <th>Total Amount</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($logs): ?>
            <?php foreach ($logs as $log): ?>
              <tr>
                <?php if ($role === 'Admin') echo '<td>' . htmlspecialchars($log['user_id']) . '</td>'; ?>
                <td><?= htmlspecialchars($log['record_date']) ?></td>
                <td><?= htmlspecialchars($log['total_entries']) ?></td>
                <td><?= number_format($log['total_amount'], 2) ?></td>
                <td>
                  <a href="edit_record.php?date=<?= htmlspecialchars($log['record_date']) ?>" style="color:#fff;background:#2a508c;padding:5px 10px;border-radius:5px;text-decoration:none; height: 29px;">Edit</a>
                  <button class="delete-log-btn" data-logid="<?= $log['log_id'] ?>" style="background:#c0392b;color:white;border:none;padding:5px 10px;border-radius:5px;cursor:pointer; height: 29px">Delete</button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="<?= $role === 'Admin' ? 5 : 4 ?>" class="Log-In-Pov-empty">No activity found for selected dates</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer class="Log-In-Pov-footer">
    <p>© 2025 Tropang Maselan Motorshop</p>
  </footer>

  <script>
    document.querySelectorAll('.delete-log-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        if (!confirm("Delete this day's record completely?")) return;
        const logId = btn.dataset.logid;
        fetch('delete_daily_log.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            log_id: logId
          })
        }).then(res => res.json()).then(data => {
          if (data.success) {
            alert('Deleted');
            btn.closest('tr').remove();
          } else alert('Delete failed');
        });
      });
    });
  </script>

</body>

</html>