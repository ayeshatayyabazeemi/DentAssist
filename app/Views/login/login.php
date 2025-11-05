<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <!-- Include Google Font and Boxicons via <link> -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css" />
</head>
<body>

  <div class="login-container">
    <h2 class="login-title">Welcome Back</h2>
<form id="loginForm" action="<?= base_url('login/auth') ?>" method="POST" class="login-form">

      <?= csrf_field() ?>

      <div class="input-group">
        <i class='bx bx-user'></i>
        <input type="text" name="username" id="username" placeholder="Username" required>
      </div>

      <div class="input-group">
        <i class='bx bx-lock'></i>
        <input type="password" name="password" id="password" placeholder="Password" required>
      </div>

      <div class="role-select">
        <label for="role">Login as:</label>
        <select name="role" id="role" required>
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

  <script src="assets/js/login.js"></script>
</body>
</html>
