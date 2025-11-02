<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tutorial</title>
  <link rel="icon" type="image/png" href="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" />

  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/header/CSS/headerUnknown.css" />
  <link rel="stylesheet" href="/website/tropangmaselan-motorshop-main/php/tutorial/tutorial.css" />
</head>

<body>
  <!-- ===== Header ===== -->
  <header class="main-header">
    <div class="logo">
      <img class="staff-activity-logo-icon" src="/website/tropangmaselan-motorshop-main/Assets/tropangmaselanLogo.png" alt="Tropang Maselan Logo">
    </div>

    <div class="menu-toggle">☰</div>

    <nav aria-label="Main navigation">
      <a href="/website/tropangmaselan-motorshop-main/php/Home/html/home.php">Home</a>
    </nav>

    <div class="header-actions">
      <button class="logout-btn" onclick="window.location.href='/website/tropangmaselan-motorshop-main/php/login/login.php'">
        Log in
      </button>
    </div>
  </header>

  <script>
    const menuToggle = document.querySelector('.menu-toggle');
    const header = document.querySelector('.main-header');

    menuToggle.addEventListener('click', () => {
      header.classList.toggle('show-menu');
    });
  </script>


  <!-- ===== superman body ===== -->
  <section class="AboutUs-about-hero">
    <h1>Welcome to the Tropang Maselan Motorshop Management System</h1>
    <button class="AboutUs-btn-tutorial">Tutorial</button>
  </section>

  <!-- ===== STEP-BY-STEP SECTION ===== -->
  <section class="AboutUs-steps-section">
    <p class="para">This website is a private site use specifically by Tropang Maselan motorshop, this deal with inventory/recording of daily sale of the shop</p>
    <h2>Step-by-Step</h2>

    <div class="AboutUs-step">
      <div class="AboutUs-step-text">
        <h3>Step 1</h3>
        <p>When using, the Staff/Admin must login first so that the other navigation will appear
          Moreover when adding a staff account the admin is the only one to have the authority in creating,
          deleting, and edit, an acount .</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/loginImage.png" alt="Login Form Image" style="height: 180px;">
      </div>
    </div>

    <div class="AboutUs-step reverse">
      <div class="AboutUs-step-text">
        <h3>Step 2</h3>
        <p>After Login, an additional navigation will appear at the top, in there the Staff will see Record, Dashboard, and Activity log. though the same as the Admin but the difference is that the admin has the additional navigation called Staff Activity</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/navigationImage.png" alt="Navigation Image" style="height: 180px;">
      </div>
    </div>

    <div class="AboutUs-step">
      <div class="AboutUs-step-text">
        <h3>Step 3</h3>
        <p>In this path, the Staff/Admin can delete, update a daily sale record, after selecting an items in list box. When adding a new record click new record button, and when adding a new item the Staff/Admin first must fill up then click enter then click the save button to save the record sal</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/recordimage.png" alt="Record Form Image" style="height: 180px;">
      </div>
    </div>

    <div class="AboutUs-step reverse">
      <div class="AboutUs-step-text">
        <h3>Step 4</h3>
        <p>Dashboard Form
          In this path, the Staff/Admin can see an updated dashboard of there sales both daily and monthly, the staff/admin can also view other statistics using the calendar, however the staff/admin can only see the record of that month</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/dashboardimage.png" alt="Dashboard Form Image" style="height: 180px;">
      </div>
    </div>

    <div class="AboutUs-step">
      <div class="AboutUs-step-text">
        <h3>Step 5</h3>
        <p>Activity log Form

          In this form, the Staff/Admin can view the recorded daily sale, the staff/admin can also edit, or delete that recorded daily sale. Moreover, for more specific view of recorded daily sale in the past, the staff/admin can use the calendar</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/activityimage.png" alt="Activity Log Form Image" style="height: 180px;">
      </div>
    </div>

    <div class="AboutUs-step reverse">
      <div class="AboutUs-step-text">
        <h3>Step 6</h3>
        <p>Staff Activity Form
          In this path, the Admin can view the work/activity of the staff, the admin can also use the calendar to view other dates, the Admin can also view the staff list in there they can edit/update, and delete a staff account, in the right side the admin can add a new account by clicking the button after clicking the admin will immediately transfer to account register form, in there the admin can fill-up the form and select specific roles of that account.</p>
      </div>
      <div class="AboutUs-step-img">
        <img src="/website/tropangmaselan-motorshop-main/Assets/accountlistimage.png" alt="Staff Activity Form Image" style="height: 180px;">
      </div>
    </div>

  </section>

  <!-- ===== PAA nya ===== -->
  <footer class="AboutUs-about-footer">
    <p>
      Tropang Maselan Motorshop Management System — building efficiency and teamwork
      for a stronger motorshop community.
    </p>
    <a href="/website/tropangmaselan-motorshop-main/php/Home/html/home.php" class="AboutUs-footer-bottom">Back to Home</a>
  </footer>
</body>

</html>