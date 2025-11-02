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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daily Record</title>
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerCashierPOV.css">

  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Admin/Record/record.css" />

  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" />

  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/Admin/Record/record-category.css">

</head>

<body class="Log-In-Pov-body">

  <!-- HEADER -->
  <header class="main-header">
    <div class="logo">
      <img class="staff-activity-logo-icon" src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Logo">
    </div>

    <div class="menu-toggle">☰</div>
    <nav aria-label="Main navigation">
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Home/html/home.php">Home</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/Record/record.php" class="active">Record</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/dashboard/dashboard.php">Dashboard</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/activitylog/activitylog.php">Activity Log</a>
      <a href="/website/tropangmaselan-motorshop-main/php/Admin/addaccount/staffactivity.php">Account List</a>
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

    function confirmLogout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = '/website/tropangmaselan-motorshop-main/php/logout.php';
      }
    }
  </script>

  <!-- MAIN CONTENT -->
  <main class="Log-In-Pov-content">
    <section class="Log-In-Pov-form-section">
      <h2>Daily Record</h2>
      <form class="Log-In-Pov-record-form">
        <!-- Category dropdown -->
        <select class="category-input" required>
          <option value="">Select category</option>
          <option value="Product">Product</option>
          <option value="Service">Service</option>
          <option value="Motorpart">Motor Part</option>
        </select>


        <input type="text" id="itemInput" placeholder="Item Name" required />
        <input type="number" id="qtyInput" placeholder="Quantity" required />
        <input type="number" id="priceInput" placeholder="Price" required />
        <input type="date" id="dateInput" required />

        <div class="Log-In-Pov-form-actions">
          <button type="button" class="Log-In-Pov-enter-btn">Enter</button>
          <button type="reset" class="Log-In-Pov-clear-btn">Clear</button>
        </div>

        <div class="Log-In-Pov-total-row">
          <label>Total:</label>
          <input type="text" id="totalInput" readonly />
        </div>

        <div class="Log-In-Pov-save-actions">
          <button type="button" class="Log-In-Pov-save-btn">Save</button>
          <button type="button" class="Log-In-Pov-edit-btn">Edit</button>
          <button type="button" class="Log-In-Pov-delete-btn">Delete</button>
        </div>
      </form>
    </section>

    <section class="Log-In-Pov-list-section">
      <h2>Recorded List</h2>
      <table>
        <thead>
          <tr>
            <th>DATE</th>
            <th>CATEGORY</th>
            <th>ITEM</th>
            <th>QTY</th>
            <th>PRICE</th>
            <th>TOTAL</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="Log-In-Pov-empty">No records yet</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <footer class="Log-In-Pov-footer">
    <p>© 2025 Tropang Maselan Motorshop</p>
  </footer>

  <script>
    let selectedRow = null;

    const categoryInput = document.getElementById('categoryInput');
    const itemInput = document.getElementById('itemInput');
    const qtyInput = document.getElementById('qtyInput');
    const priceInput = document.getElementById('priceInput');
    const dateInput = document.getElementById('dateInput');
    const totalInput = document.getElementById('totalInput');
    const table = document.querySelector('.Log-In-Pov-list-section table tbody');

    // Auto total calculation
    function updateTotal() {
      const qty = parseFloat(qtyInput.value) || 0;
      const price = parseFloat(priceInput.value) || 0;
      totalInput.value = (qty * price).toFixed(2);
    }
    qtyInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);

    // Enter / Add row
    document.querySelector('.Log-In-Pov-enter-btn').addEventListener('click', () => {
      if (!categoryInput.value || !itemInput.value || !qtyInput.value || !priceInput.value || !dateInput.value)
        return alert("Fill all fields");

      if (selectedRow) {
        // Update row
        selectedRow.cells[0].innerText = dateInput.value;
        selectedRow.cells[1].innerText = categoryInput.value;
        selectedRow.cells[2].innerText = itemInput.value;
        selectedRow.cells[3].innerText = qtyInput.value;
        selectedRow.cells[4].innerText = parseFloat(priceInput.value).toFixed(2);
        selectedRow.cells[5].innerText = totalInput.value;
        selectedRow = null;
      } else {
        // Add new row
        if (table.querySelector('.Log-In-Pov-empty')) table.innerHTML = '';
        const row = table.insertRow();
        row.insertCell(0).innerText = dateInput.value;
        row.insertCell(1).innerText = categoryInput.value;
        row.insertCell(2).innerText = itemInput.value;
        row.insertCell(3).innerText = qtyInput.value;
        row.insertCell(4).innerText = parseFloat(priceInput.value).toFixed(2);
        row.insertCell(5).innerText = totalInput.value;

        row.addEventListener('click', () => {
          selectedRow = row;
          dateInput.value = row.cells[0].innerText;
          categoryInput.value = row.cells[1].innerText;
          itemInput.value = row.cells[2].innerText;
          qtyInput.value = row.cells[3].innerText;
          priceInput.value = row.cells[4].innerText;
          totalInput.value = row.cells[5].innerText;
        });
      }

      // Clear form
      itemInput.value = '';
      qtyInput.value = '';
      priceInput.value = '';
      dateInput.value = '';
      categoryInput.value = '';
      totalInput.value = '';
    });

    // Clear
    document.querySelector('.Log-In-Pov-clear-btn').addEventListener('click', () => {
      itemInput.value = '';
      qtyInput.value = '';
      priceInput.value = '';
      dateInput.value = '';
      categoryInput.value = '';
      totalInput.value = '';
      selectedRow = null;
    });

    // Delete
    document.querySelector('.Log-In-Pov-delete-btn').addEventListener('click', () => {
      if (!selectedRow) return alert("Select a row first");
      selectedRow.remove();
      if (!table.rows.length) table.innerHTML = '<tr><td colspan="6" class="Log-In-Pov-empty">No records yet</td></tr>';
      selectedRow = null;
    });

    // Save via AJAX
    document.querySelector('.Log-In-Pov-save-btn').addEventListener('click', () => {
      const rows = Array.from(table.rows).filter(r => !r.querySelector('.Log-In-Pov-empty'));
      if (!rows.length) return alert("No rows to save");

      const data = rows.map(r => ({
        record_date: r.cells[0].innerText,
        category: r.cells[1].innerText,
        item_name: r.cells[2].innerText,
        quantity: r.cells[3].innerText,
        price: r.cells[4].innerText
      }));

      fetch('/website/tropangmaselan-motorshop-main/php/Admin/Record/save_records.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      }).then(res => res.json()).then(resp => {
        if (resp.success) {
          alert("Records saved!");
          table.innerHTML = '';
          resp.saved.forEach(r => {
            const row = table.insertRow();
            row.insertCell(0).innerText = r.record_date;
            row.insertCell(1).innerText = r.category;
            row.insertCell(2).innerText = r.item_name;
            row.insertCell(3).innerText = r.quantity;
            row.insertCell(4).innerText = parseFloat(r.price).toFixed(2);
            row.insertCell(5).innerText = parseFloat(r.total).toFixed(2);

            row.addEventListener('click', () => {
              selectedRow = row;
              dateInput.value = row.cells[0].innerText;
              categoryInput.value = row.cells[1].innerText;
              itemInput.value = row.cells[2].innerText;
              qtyInput.value = row.cells[3].innerText;
              priceInput.value = row.cells[4].innerText;
              totalInput.value = row.cells[5].innerText;
            });
          });
        } else {
          alert("Error saving records");
        }
      });
    });
  </script>
</body>

</html>