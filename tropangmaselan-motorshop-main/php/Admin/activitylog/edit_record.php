<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: /website/tropangmaselan-motorshop-main/php/login/login.php");
  exit;
}

$pdo = new PDO(
  "mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4",
  'root',
  '',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Determine whose records to edit
$record_date = $_GET['date'] ?? null;
if (!$record_date) die("Invalid date");

// Admin can pass user_id via GET
if ($_SESSION['role'] === 'Admin') {
  $user_id = $_GET['user_id'] ?? null;
  if (!$user_id) {
    // Fetch list of cashiers
    $stmt = $pdo->query("SELECT user_id, fullname FROM users WHERE role='Cashier'");
    $cashiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Select a cashier to view records:</h3>";
    echo "<ul>";
    foreach ($cashiers as $c) {
      echo "<li><a href='?date={$record_date}&user_id={$c['user_id']}'>" . $c['fullname'] . "</a></li>";
    }
    echo "</ul>";
    exit;
  }
}


// Fetch all records for that day
$stmt = $pdo->prepare("SELECT * FROM daily_records WHERE user_id=? AND record_date=?");
$stmt->execute([$user_id, $record_date]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Record <?= htmlspecialchars($record_date) ?></title>
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Cashier/Record/record.css">
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png">
</head>

<body class="Log-In-Pov-body">

  <header class="main-header">
    <div class="logo">
      <img class="staff-activity-logo-icon" src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo">
    </div>
    <div class="menu-toggle">☰</div>
    <nav>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Home/html/home.php">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Record/record.php">Record</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/dashboard/dashboard.php">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/activitylog/activitylog.php">Activity Log</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/addaccount/staffactivity.php">Account List</a>
    </nav>
    <div class="header-actions">
      <span>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <button class="logout-btn" onclick="if(confirm('Logout?')) window.location.href='/website/tropangmaselan-motorshop-main/php/logout.php'">Logout</button>
    </div>
  </header>

  <section style="padding:20px;">
    <h2 style="color:white;">Edit Record for <?= htmlspecialchars($record_date) ?></h2>

    <form id="editRecordsForm">
      <table>
        <thead>
          <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($records): ?>
            <?php foreach ($records as $rec): ?>
              <tr data-id="<?= $rec['record_id'] ?>">
                <td><input type="text" name="item_name[]" value="<?= htmlspecialchars($rec['item_name']) ?>" /></td>
                <td>
                  <select name="category[]">
                    <option value="Product" <?= $rec['category'] === 'Product' ? 'selected' : '' ?>>Product</option>
                    <option value="Service" <?= $rec['category'] === 'Service' ? 'selected' : '' ?>>Service</option>
                    <option value="Motorpart" <?= $rec['category'] === 'Motorpart' ? 'selected' : '' ?>>Motorpart</option>
                  </select>
                </td>
                <td><input type="number" name="quantity[]" value="<?= $rec['quantity'] ?>" /></td>
                <td><input type="number" step="0.01" name="price[]" value="<?= $rec['price'] ?>" /></td>
                <td class="total">₱<?= number_format($rec['total'], 2) ?></td>
                <td><button type="button" class="delete-btn">Delete</button></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">No records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <button type="button" id="saveAllBtn" style="margin-top:15px;background:#223b63;color:white;border:none;border-radius:25px;padding:10px 20px;font-weight:600;cursor:pointer;">
        Save
      </button>

      <button style="margin-top:15px;background:#223b63;color:white;border:none;border-radius:25px;padding:10px 20px;font-weight:600;cursor:pointer;" type="button" onclick="back()">Back</button>

    </form>
  </section>

  <script>
    const back = () => {
      <?php if ($_SESSION['role'] === 'Admin'): ?>
        window.location.href = '/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.php';
      <?php else: ?>
        window.location.href = '/website/tropangmaselan-motorshop-main/php/Cashier/activitylog/activitylog.php';
      <?php endif; ?>
    };

    document.querySelectorAll('tr[data-id]').forEach(row => {
      const qty = row.children[2].querySelector('input');
      const price = row.children[3].querySelector('input');
      const totalCell = row.children[4];

      function updateTotal() {
        const total = parseFloat(qty.value || 0) * parseFloat(price.value || 0);
        totalCell.innerText = `₱${total.toFixed(2)}`;
      }

      qty.addEventListener('input', updateTotal);
      price.addEventListener('input', updateTotal);

      row.querySelector('.delete-btn').addEventListener('click', () => {
        if (!confirm("Delete this record?")) return;
        fetch('delete_daily_record.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            record_id: row.dataset.id
          })
        }).then(res => res.json()).then(data => {
          if (data.success) row.remove();
          else alert("Delete failed");
        });
      });
    });

    document.getElementById('saveAllBtn').addEventListener('click', () => {
      const rows = document.querySelectorAll('tr[data-id]');
      if (rows.length === 0) return alert("No records to save");

      const payload = Array.from(rows).map(row => ({
        record_id: row.dataset.id,
        record_date: '<?= $record_date ?>',
        item_name: row.children[0].querySelector('input').value,
        category: row.children[1].querySelector('select').value,
        quantity: row.children[2].querySelector('input').value,
        price: row.children[3].querySelector('input').value
      }));

      fetch('update_daily_record.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          records: payload
        })
      }).then(res => res.json()).then(data => {
        if (data.success) {
          alert("All records updated successfully");
          rows.forEach(row => {
            const total = parseFloat(row.children[2].querySelector('input').value) *
              parseFloat(row.children[3].querySelector('input').value);
            row.children[4].innerText = `₱${total.toFixed(2)}`;
          });
          window.location.href = '/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.php';
        } else {
          alert(data.message || "Update failed");
        }
      });
    });
  </script>
</body>

</html>