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

// Allow month selection via GET
$month = $_GET['month'] ?? date('Y-m');

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $dbuser,
    $dbpass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );
} catch (PDOException $e) {
  die("DB Connection failed: " . $e->getMessage());
}

$user_id = $_SESSION['user_id'];

// KPI totals
$stmt = $pdo->prepare("SELECT SUM(total) AS total_sales, SUM(quantity) AS total_qty FROM daily_records WHERE user_id=? AND DATE_FORMAT(record_date,'%Y-%m')=?");
$stmt->execute([$user_id, $month]);
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

// Top category
$stmt = $pdo->prepare("SELECT category, SUM(total) AS cat_total FROM daily_records WHERE user_id=? AND DATE_FORMAT(record_date,'%Y-%m')=? GROUP BY category ORDER BY cat_total DESC LIMIT 1");
$stmt->execute([$user_id, $month]);
$top_category = $stmt->fetch(PDO::FETCH_ASSOC);

// Pie chart: total sales per category
$stmt = $pdo->prepare("SELECT category, SUM(total) AS cat_total FROM daily_records WHERE user_id=? AND DATE_FORMAT(record_date,'%Y-%m')=? GROUP BY category");
$stmt->execute([$user_id, $month]);
$category_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pie_labels = array_column($category_sales, 'category');
$pie_data = array_column($category_sales, 'cat_total');

// Line chart: daily sales comparison per category
$stmt = $pdo->prepare("SELECT record_date, category, SUM(total) AS daily_total FROM daily_records WHERE user_id=? AND DATE_FORMAT(record_date,'%Y-%m')=? GROUP BY record_date, category ORDER BY record_date ASC");
$stmt->execute([$user_id, $month]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dates = [];
$cats = ['Product', 'Service', 'Motorpart'];
$cat_colors = ['Product' => '#f1c40f', 'Service' => '#e74c3c', 'Motorpart' => '#3498db'];
$cat_data = ['Product' => [], 'Service' => [], 'Motorpart' => []];

foreach ($rows as $r) {
  if (!in_array($r['record_date'], $dates)) $dates[] = $r['record_date'];
}
foreach ($dates as $d) {
  foreach ($cats as $c) {
    $cat_data[$c][] = 0;
    foreach ($rows as $r) {
      if ($r['record_date'] == $d && $r['category'] == $c) $cat_data[$c][count($cat_data[$c]) - 1] = $r['daily_total'];
    }
  }
}

// Bar chart: top 5 highest grossing products
$stmt = $pdo->prepare("SELECT item_name, category, SUM(total) AS total_sale FROM daily_records WHERE user_id=? AND DATE_FORMAT(record_date,'%Y-%m')=? GROUP BY item_name, category ORDER BY total_sale DESC LIMIT 5");
$stmt->execute([$user_id, $month]);
$top5 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$top5_colors = array_map(fn($p) => $cat_colors[$p['category']] ?? '#2a508c', $top5);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Tropang Maselan Motorshop</title>
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" />
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Cashier/dashboard/dashboard.css" />
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .Log-In-Pov-content {
      padding: 20px;
    }

    .dashboard-filters {
      display: flex;
      align-items: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .dashboard-filters input[type="month"],
    .dashboard-filters button {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
      cursor: pointer;
    }

    .dashboard-cards,
    .charts-section {
      display: flex;
      gap: 20px;
      flex-wrap: nowrap;
      margin-bottom: 30px;
    }

    .kpi-card,
    .card {
      flex: 1 1 0;
      min-width: 0;
      border-radius: 16px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    .kpi-card:hover,
    .card:hover {
      transform: translateY(-5px);
    }

    .kpi-card h4 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #fff;
    }

    .kpi-card p {
      font-size: 28px;
      font-weight: 700;
      margin: 0;
      color: #fff;
    }

    .card h4 {
      color: #2a508c;
      margin-bottom: 15px;
    }

    .card canvas {
      width: 100% !important;
      height: 300px !important;
    }

    @media screen and (max-width: 1024px) {

      .dashboard-cards,
      .charts-section {
        flex-direction: column;
        flex-wrap: wrap;
      }
    }

    @media screen and (max-width: 480px) {
      .card canvas {
        height: 200px !important;
      }
    }
  </style>
</head>

<body>

  <header class="main-header">
    <div class="logo">
      <img src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo" style="height:auto; width:100px;">
    </div>
    <nav>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Home/html/home.php">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Record/record.php">Record</a>
      <a href="#" class="active">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/activitylog/activitylog.php">Activity Log</a>
    </nav>
    <div class="header-actions">
      <span>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <button class="logout-btn" onclick="if(confirm('Logout?')) window.location.href='/website/tropangmaselan-motorshop-main/php/logout.php'">Logout</button>
    </div>
  </header>

  <main class="Log-In-Pov-content">

    <div class="dashboard-filters">
      <label for="monthFilter" style="color:#fff; font-weight: bold;">Select Month:</label>
      <input style="border: 5; border-color: #223b63; color: #ffffffff; font-weight: bold;" type="month" id="monthFilter" value="<?= $month ?>">
      <button style="background:#223b63; border: none; color: white; font-weight: bold; height: 40px; width: 100px;" id="resetMonth">Reset</button>
      <button style="background:#223b63; border: none; color: white; font-weight: bold; height: 40px; width: 100px;" onclick="window.print()">Print PDF</button>
    </div>

    <section class="dashboard-cards">
      <div class="kpi-card" style="background:#2f4f7c">
        <h4>Total Sales (<?= date('F Y', strtotime($month . '-01')) ?>)</h4>
        <p>₱<?= number_format($totals['total_sales'] ?? 0, 2) ?></p>
      </div>

      <div class="kpi-card" style="background:#2f4f7c">
        <h4>Total Quantity Sold</h4>
        <p><?= $totals['total_qty'] ?? 0 ?></p>
      </div>

      <div class="kpi-card" style="background:#2f4f7c">
        <h4>Top Category</h4>
        <p><?= $top_category['category'] ?? '-' ?> (₱<?= number_format($top_category['cat_total'] ?? 0, 2) ?>)</p>
      </div>
    </section>



    <section class="charts-section">
      <div class="card">
        <h4>Total Sales by Category (Pie Chart)</h4>
        <canvas id="categoryPieChart"></canvas>
      </div>
      <div class="card">
        <h4>Daily Sales Comparison (Line Chart)</h4>
        <canvas id="dailyCategoryChart"></canvas>
      </div>
      <div class="card">
        <h4>Top 5 Highest Grossing Products (Bar Chart)</h4>
        <canvas id="topProductsChart"></canvas>
      </div>
    </section>

  </main>
  <footer class="Log-In-Pov-footer">
    <p>© 2025 Tropang Maselan Motorshop</p>
  </footer>

  <script>
    // Pie Chart
    const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
    new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: <?= json_encode($pie_labels) ?>,
        datasets: [{
          data: <?= json_encode($pie_data) ?>,
          backgroundColor: [
            '<?= $cat_colors['Product'] ?>',
            '<?= $cat_colors['Service'] ?>',
            '<?= $cat_colors['Motorpart'] ?>'
          ]
        }]
      }
    });

    // Line Chart
    const ctxLine = document.getElementById('dailyCategoryChart').getContext('2d');
    new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: <?= json_encode($dates) ?>,
        datasets: <?= json_encode(array_map(function ($c) use ($cat_data, $cat_colors) {
                    return [
                      'label' => $c,
                      'data' => $cat_data[$c],
                      'borderColor' => $cat_colors[$c],
                      'fill' => false
                    ];
                  }, $cats)) ?>
      },
      options: {
        responsive: true
      }
    });

    // Bar Chart
    const ctxBar = document.getElementById('topProductsChart').getContext('2d');
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: <?= json_encode(array_column($top5, 'item_name')) ?>,
        datasets: [{
          label: 'Sales',
          data: <?= json_encode(array_column($top5, 'total_sale')) ?>,
          backgroundColor: <?= json_encode($top5_colors) ?>
        }]
      },
      options: {
        responsive: true
      }
    });

    // Month filter
    const monthFilter = document.getElementById('monthFilter');
    const resetBtn = document.getElementById('resetMonth');
    monthFilter.addEventListener('change', () => {
      window.location.href = `?month=${monthFilter.value}`
    });
    resetBtn.addEventListener('click', () => {
      window.location.href = window.location.pathname
    });
  </script>

</body>

</html>