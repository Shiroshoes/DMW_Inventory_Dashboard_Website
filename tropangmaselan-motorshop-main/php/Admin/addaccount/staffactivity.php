<?php
session_start();

// --- Only Admin access ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
  header("Location: /website/tropangmaselan-motorshop-main/php/login/login.php");
  exit();
}

// --- Database connection ---
$pdo = new PDO(
  "mysql:host=localhost;dbname=tropangmaselandb;charset=utf8mb4",
  'root',
  '',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// --- Fetch accounts ---
$stmt = $pdo->query("SELECT user_id, username, fullname, role FROM users ORDER BY role DESC, fullname ASC");
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Account List</title>
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Admin/addaccount/admin_accounts.css">
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png">

  <style>
    /* Show/Hide Password Wrapper */
    .password-wrapper {
      display: flex;
      align-items: center;
    }

    .password-wrapper input {
      flex: 1;
    }

    .password-wrapper button {
      margin-left: 5px;
      padding: 5px 10px;
      cursor: pointer;
      background-color: #2a508c;
      color: #fff;
      border: none;
      border-radius: 4px;
    }

    .password-wrapper button:hover {
      background-color: #1f3b6f;
    }
  </style>
</head>

<body class="Log-In-Pov-body">

  <header class="main-header">
    <div class="logo">
      <img src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo"
        style="height:auto; width:120px;">
    </div>
    <nav>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Home/html/home.php">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Record/record.php">Record</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/dashboard/dashboard.php">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.php">Activity Log</a>
      <a href="#" class="active">Account List</a>
    </nav>
    <div class="header-actions">
      <span>Hello, <?= htmlspecialchars($_SESSION['fullname']) ?></span>
      <button class="logout-btn"
        onclick="if(confirm('Logout?')) window.location.href='/website/tropangmaselan-motorshop-main/php/logout.php'">Logout</button>
    </div>
  </header>

  <main class="Log-In-Pov-content">

    <!-- LEFT PANEL: TABLE -->
    <div class="Log-In-Pov-list-section">
      <h2>Accounts</h2>
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Full Name</th>
            <th>Role</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($accounts): ?>
            <?php foreach ($accounts as $acc): ?>
              <tr data-id="<?= $acc['user_id'] ?>" data-username="<?= htmlspecialchars($acc['username']) ?>"
                data-fullname="<?= htmlspecialchars($acc['fullname']) ?>" data-role="<?= $acc['role'] ?>">
                <td><?= htmlspecialchars($acc['username']) ?></td>
                <td><?= htmlspecialchars($acc['fullname']) ?></td>
                <td><?= $acc['role'] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="Log-In-Pov-empty">No accounts found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- RIGHT PANEL: EDIT/ADD ACCOUNT -->
    <div class="Log-In-Pov-form-section Accountform">
      <h2 id="form-title">Add New Account</h2>
      <form id="accountForm" class="Accountform">
        <input type="hidden" name="user_id" id="user_id">
        <input type="text" name="username" id="username" placeholder="Username" required>

        <!-- PASSWORD WITH SHOW/HIDE -->
        <div class="password-wrapper">
          <input type="password" name="password" id="password" placeholder="Password (leave blank to keep)">
          <button type="button" id="togglePassword" style="height: 38px; width: 70px;">Show</button>
        </div>

        <input type="text" name="fullname" id="fullname" placeholder="Full Name" required>
        <select name="role" id="role" required>
          <option value="Admin">Admin</option>
          <option value="Cashier" selected>Cashier</option>
        </select>

        <div class="Log-In-Pov-form-actions">
          <button type="button" id="saveBtn" class="Log-In-Pov-enter-btn">Save</button>
          <button type="button" id="updateBtn" class="Log-In-Pov-enter-btn">Update</button>
          <button type="button" id="deleteBtn" class="Log-In-Pov-clear-btn">Delete</button>
          <button type="button" id="clearForm" class="Log-In-Pov-clear-btn">Clear</button>
        </div>

      </form>
    </div>

  </main>

  <script>
    const accountForm = document.getElementById('accountForm');
    const clearBtn = document.getElementById('clearForm');
    const formTitle = document.getElementById('form-title');

    const saveBtn = document.getElementById('saveBtn');
    const updateBtn = document.getElementById('updateBtn');
    const deleteBtn = document.getElementById('deleteBtn');

    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Show/Hide password
    togglePasswordBtn.addEventListener('click', () => {
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      togglePasswordBtn.innerText = type === 'password' ? 'Show' : 'Hide';
    });

    // Populate form when row clicked
    document.querySelectorAll('table tbody tr[data-id]').forEach(row => {
      row.addEventListener('click', () => {
        document.getElementById('user_id').value = row.dataset.id;
        document.getElementById('username').value = row.dataset.username;
        document.getElementById('password').value = '';
        document.getElementById('fullname').value = row.dataset.fullname;
        document.getElementById('role').value = row.dataset.role;
        formTitle.innerText = 'Edit Account';
      });
    });

    // Clear form
    clearBtn.addEventListener('click', () => {
      document.getElementById('user_id').value = '';
      document.getElementById('username').value = '';
      document.getElementById('password').value = '';
      document.getElementById('fullname').value = '';
      document.getElementById('role').value = 'Cashier';
      formTitle.innerText = 'Add New Account';
    });

    // Function to send form data
    function sendForm(formData) {
      fetch('save_account.php', {
        method: 'POST',
        body: formData
      }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
        else alert(data.message || "Operation failed");
      });
    }

    // Save new account
    saveBtn.addEventListener('click', () => {
      const formData = new FormData(accountForm);
      formData.append('action', 'save');
      sendForm(formData);
    });

    // Update account
    updateBtn.addEventListener('click', () => {
      if (!document.getElementById('user_id').value) {
        alert("Select an account to update.");
        return;
      }
      const formData = new FormData(accountForm);
      formData.append('action', 'update');
      sendForm(formData);
    });

    // Delete account
    deleteBtn.addEventListener('click', () => {
      const userId = document.getElementById('user_id').value;
      if (!userId) {
        alert("Select an account to delete.");
        return;
      }
      if (confirm("Are you sure you want to delete this account?")) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('action', 'delete');
        sendForm(formData);
      }
    });
  </script>

</body>

</html>