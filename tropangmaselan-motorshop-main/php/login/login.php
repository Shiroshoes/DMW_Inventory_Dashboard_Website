<?php
session_start();

// --- Redirect already logged-in users ---
if (isset($_SESSION['user_id'])) {
  if ($_SESSION['role'] === 'Admin') {
    header("Location: /website/tropangmaselan-motorshop-main/php/Admin/Home/html/home.php");
  } else {
    header("Location: ../Cashier/Home/html/home.php");
  }
  exit();
}

// --- Database connection ---
$host = 'localhost';
$dbname = 'tropangmaselandb';
$dbuser = 'root';
$dbpass = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  ]);
} catch (PDOException $e) {
  die("DB connect failed: " . htmlspecialchars($e->getMessage()));
}

// --- Initialize error ---
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($username === '' || $password === '') {
    $error = "Please fill out all fields.";
  } else {
    // --- Fetch user by username ---
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
      // --- Set session variables ---
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['fullname'] = $user['fullname'];

      session_regenerate_id(true);

      // --- Redirect based on role ---
      if ($user['role'] === 'Admin') {
        header("Location: /website/tropangmaselan-motorshop-main/php/Admin/Home/html/home.php");
      } else {
        header("Location: ../Cashier/Home/html/home.php");
      }
      exit;
    } else {
      $error = "Invalid username or password.";
    }
  }
}

// --- Disable caching for security ---
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Login | Tropang Maselan Motorshop</title>
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png">
  <style>
    /* --- Basic styling --- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Open Sans", sans-serif;
    }

    body {
      background-color: #2a508c;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: #fff;
    }

    .card {
      background-color: #fff;
      color: #2a508c;
      width: 400px;
      padding: 35px 30px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
      text-align: center;
    }

    h2 {
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 15px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      text-align: left;
    }

    label {
      font-weight: 600;
      font-size: 14px;
    }

    input {
      width: 100%;
      padding: 10px 12px;
      border-radius: 25px;
      border: 2px solid #cfd8e3;
      font-size: 15px;
      outline: none;
      transition: border-color 0.3s;
    }

    input:focus {
      border-color: #2a508c;
    }

    .password-wrapper {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #2a508c;
      cursor: pointer;
      font-weight: bold;
      font-size: 13px;
    }

    button[type="submit"] {
      background-color: #2a508c;
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 12px 0;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 10px;
    }

    button[type="submit"]:hover {
      background-color: #3c6fb7;
    }

    .err {
      color: red;
      font-size: 14px;
      margin-bottom: 8px;
      text-align: center;
    }

    .footer {
      font-size: 12px;
      color: #777;
      margin-top: 20px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="card">
    <h2>Tropang Maselan Login</h2>
    <?php if ($error): ?>
      <div class="err"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Username</label>
      <input name="username" required>
      <label>Password</label>
      <div class="password-wrapper">
        <input name="password" id="password" type="password" required>
        <button type="button" class="toggle-password" onclick="togglePassword()">👁</button>
      </div>
      <button type="submit">Login</button>
    </form>
    <div class="footer">© 2025 Tropang Maselan Motorshop</div>
  </div>
  <script>
    function togglePassword() {
      const pwField = document.getElementById('password');
      pwField.type = pwField.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>

</html>