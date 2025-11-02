<?php
session_start();

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
  header("Location: /website/tropangmaselan-motorshop-main/php/login/login.php");
  exit();
}

// --- Disable caching ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tropang Maselan Motorshop Management System</title>
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" />

  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Cashier/Home/css/home.css" />
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">

  <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet" />
</head>

<body>
  <!-- ===== Header ===== -->
  <header class="main-header">
    <div class="logo">
      <img class="staff-activity-logo-icon" src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Tropang Maselan Logo">
    </div>

    <div class="menu-toggle">☰</div>

    <nav aria-label="Main navigation">
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Home/html/home.php" class="active">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/Record/record.php">Record</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/dashboard/dashboard.php">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Cashier/activitylog/activitylog.php">Activity Log</a>
    </nav>

    <div class="header-actions">
      <span>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <button class="logout-btn" onclick="confirmLogout()">Logout</button>
    </div>
  </header>

  <script>
    const menuToggle = document.querySelector('.menu-toggle');
    const header = document.querySelector('.main-header');

    menuToggle.addEventListener('click', () => {
      header.classList.toggle('show-menu');
    });

    // Confirm logout function
    function confirmLogout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = '/website/tropangmaselan-motorshop-main/php/logout.php';
      }
    }
  </script>


  <!-- ===== MAIN ===== -->
  <main>
    <!-- === WELCOME SECTION === -->
    <section class="welcome-section">
      <div class="welcome-content">
        <h1>Welcome to the Tropang Maselan Motorshop Management System</h1>
        <p>
          This platform is designed exclusively for our team — owners and staff —
          to record daily sales, monitor performance, and manage operations more efficiently.
        </p>
        <button class="btn-tutorial">

          <a href="/website/tropangmaselan-motorshop-main/php/Cashier/tutorial/tutorial.php" style="text-decoration: none; color: inherit;">
            Tutorial
          </a></button>
      </div>
    </section>
    <div class="custom-shape-divider-top-1759716716">
      <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
        <path d="M0,0V7.23C0,65.52,268.63,112.77,600,112.77S1200,65.52,1200,7.23V0Z" class="shape-fill"></path>
      </svg>
    </div>

    <!-- === HOME SECTION === -->
    <section class="home-section">
      <h2>HOME</h2>
      <div class="home-content">
        <div class="home-left">
          <div class="icon-mail">
            <svg xmlns="http://www.w3.org/2000/svg" class="mail-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5.4 19h13.2a2 2 0 002-2V7a2 2 0 00-2-2H5.4a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
          </div>
          <p>
            With an integrated dashboard, you’ll get a clear view of our shop’s progress,
            helping us make better decisions and ensure that every detail of our work
            reflects the Tropang Maselan standard we stand for.
          </p>
          <div class="placeholder-box">
            <img src="/website/tropangmaselan-motorshop-main/Assets/garage.avif"
              style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
          </div>

          <p class="important-text">
            Most importantly, this platform is here to make our daily tasks easier.
            Less paperwork, fewer mistakes, and more time to focus on what matters most
            — serving our customers and taking pride in our craft.
          </p>
        </div>

        <div class="vertical-line"></div>

        <div class="home-right">
          <img src="/website/tropangmaselan-motorshop-main/Assets/graph.png" alt="Dashboard charts" class="dashboard-image" />
          <p>
            This system isn’t just a tool — it’s a reflection of teamwork. Every staff entry,
            every sales update, and every recorded transaction contributes to the growth
            of Tropang Maselan. Together, we build a stronger foundation for better
            decision-making and smoother operations.
          </p>
          <div class="placeholder-box small">
            <img type="image/png" src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo" style="width: 150px; height: 150px;">
          </div>
        </div>
      </div>
      <p class="footer-note">
        Let’s continue to move forward as one team, making Tropang Maselan not only a trusted
        motorshop but also a model of discipline, order, and growth.
      </p>
    </section>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="Log-In-Pov-footer">
    <p>© 2025 Tropang Maselan Motorshop</p>

    <p>For Further Inqueries Click this Lenk: <a href="https://www.facebook.com/mellllll.90" target="_blank" style="color: white;">Facebook</a></p>

  </footer>
</body>

</html>