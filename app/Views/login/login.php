<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>

  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">

  <!-- Your Login Page CSS -->
  <link rel="stylesheet" href="assets/css/login.css" />

  <!-- Your Landing CSS for header -->
  <link rel="stylesheet" href="assets/css/landing.css" />
</head>
<body>

  <!-- ===================== HEADER ADDED ===================== -->
  <nav class="navbar">
    <div class="container">
      <div class="logo">DentAssist</div>

      <ul class="nav-links">
        <li><a href="<?= base_url('/'); ?>#home">Home</a></li>
        <li><a href="<?= base_url('/'); ?>#features">Features</a></li>
        <li><a href="<?= base_url('/'); ?>#about">About</a></li>
        <li><a href="<?= base_url('/'); ?>#contact">Contact</a></li>
      </ul>

      <!-- Since this is the login page, login button can stay -->
      <a href="<?= base_url('/login'); ?>" class="btn-login">Login</a>

      <div class="menu-toggle" id="menuToggle">☰</div>
    </div>
  </nav>
  <!-- ===================== HEADER END ===================== -->

  <main class="login-page">
  <div class="login-container">
    <h2 class="login-title">Welcome Back</h2>

    <form id="loginForm" action="<?= base_url('login/auth') ?>" method="POST">
      <?= csrf_field() ?>

      <div class="input-group">
        <i class='bx bx-user'></i>
        <input type="text" name="username" placeholder="Username" required>
      </div>

      <div class="input-group">
        <i class='bx bx-lock'></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="role-select">
        <label for="role">Login as:</label>
        <select name="role" required>
          <option value="">Select Role</option>
          <option value="admin">Admin</option>
          <option value="doctor">Doctor</option>
          <option value="receptionist">Receptionist</option>
        </select>
      </div>

      <button type="submit" class="login-btn">Login</button>
    </form>

    <div id="errorBox" class="message error"></div>
    <div id="successBox" class="message success"></div>
  </div>
</main>


  <!-- Login JS -->
  <script src="assets/js/login.js"></script>

  <!-- Header JS (same as landing page) -->
  <script src="assets/js/landing.js"></script>
</body>
</html>
