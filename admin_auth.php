<?php
session_start();

// Redirect logged-in admins to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BIGb online shopping - Admin Login/Register</title>
  <link rel="stylesheet" href="login.style.css" />
  
</head>
<body>
  <style>
    html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
body {
  display: flex;
  justify-content: center; 
  align-items: center;     
  height: 100vh;          
}

  </style>

<?php if (!empty($_SESSION['error'])): ?>
  <div style="color:#721c24; background:#f8d7da; padding:10px; border-radius:8px; max-width:320px; margin: 10px auto;">
    <?= htmlspecialchars($_SESSION['error']) ?>
  </div>
  <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
  <div style="color:#155724; background:#d4edda; padding:10px; border-radius:8px; max-width:320px; margin: 10px auto;">
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

  <div class="hero">
    <div class="form-box" style="position:relative; width:400px; height:520px; margin:auto;">

      <div class="button-box" style="position:relative; width:220px; margin:35px auto 30px; box-shadow: 0 0 20px 9px #ff61241f; border-radius: 30px;">
        <div id="btn"></div>
        <button type="button" class="toggle-btn" onclick="login()">Log In</button>
        <button type="button" class="toggle-btn" onclick="register()">Register</button>
      </div>

      <!-- Login Form -->
      <form id="Login" class="input-group" action="process_login.php" method="POST" style="position:absolute;">
        <input type="email" name="email" class="input-field" placeholder="Enter email" required />
        <input type="password" name="password" class="input-field" placeholder="Enter password" required />
        <input type="checkbox" class="check-box" id="rememberMe" name="remember_me" />
        <label for="rememberMe"><span>Remember Me</span></label>
        <button type="submit" class="submit-btn">Log in</button>
      </form>

      <!-- Registration Form -->
      <form id="register" class="input-group" action="register.php" method="POST" style="position:absolute; width: 320px;">
        <input type="text" name="name" class="input-field" placeholder="Enter name" required />
        <input type="email" name="email" class="input-field" placeholder="Enter email" required />
        <input type="password" name="password" class="input-field" placeholder="Enter password" required />
        <input type="password" name="confirm_password" class="input-field" placeholder="Confirm password" required />

        <label for="role" style="margin-top:10px; color:#555; font-weight:600;">Role</label>
        <select name="role" id="role" class="input-field" required>
          <option value="staff" selected>Staff</option>
          <option value="manager">Manager</option>
          <option value="admin">Admin</option>
          <option value="super_admin">Super Admin</option>
        </select>

        <label style="margin-top:10px;">
          <input type="checkbox" name="is_active" checked />
          Active
        </label>

        <button type="submit" class="submit-btn" style="margin-top:20px;">Register</button>
      </form>

    </div>
  </div>

  <script>
    var x = document.getElementById("Login");
    var y = document.getElementById("register");
    var z = document.getElementById("btn");

    function register() {
      x.style.left = "-400px";
      y.style.left = "50px";
      z.style.left = "110px";
    }

    function login() {
      x.style.left = "50px";
      y.style.left = "450px";
      z.style.left = "0px";
    }
  </script>
</body>
</html>