<?php
require_once 'db_connect.php';  
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
  <style>
   
    .password-wrapper {
      position: relative;
      width: 100%;
    }
    .password-wrapper input[type="password"],
    .password-wrapper input[type="text"] {
      width: 100%;
      padding-right: 40px;
      box-sizing: border-box;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      width: 24px;
      height: 24px;
      fill: #888;
      user-select: none;
    }
    .toggle-password:hover {
      fill: #ff6124;
    }
  </style>
</head>
<body>

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
      <form id="Login" class="input-group" action="process_admin_login.php" method="POST" style="position:absolute;" autocomplete="off">
        
        <input type="email" name="email" class="input-field" placeholder="Enter email" required autocomplete="off" />
        
        <div class="password-wrapper">
          <input type="password" name="password" id="login-password" class="input-field" placeholder="Enter password" required autocomplete="new-password" />
          
          <svg id="toggleLoginPassword" class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#888"/>
          </svg>
        </div>
        <input type="checkbox" class="check-box" id="rememberMe" name="remember_me" autocomplete="off" />
        
        <label for="rememberMe"><span>Remember Me</span></label>
        <button type="submit" class="submit-btn">Log in</button>
      </form>

      <!-- Registration Form -->
      <form id="register" class="input-group" action="admin_register.php" method="POST" style="position:absolute; width: 320px;" autocomplete="off">
       
        <input type="text" name="name" class="input-field" placeholder="Enter name" required autocomplete="off" />
        
        <input type="email" name="email" class="input-field" placeholder="Enter email" required autocomplete="off" />
        
        
        <div class="password-wrapper">
          <input type="password" name="password" id="register-password" class="input-field" placeholder="Enter password" required autocomplete="new-password" />
          
          <svg id="toggleRegisterPassword" class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#888"/>
          </svg>
        </div>

        <div class="password-wrapper" style="margin-top: 10px;">
          <input type="password" name="confirm_password" id="register-confirm-password" class="input-field" placeholder="Confirm password" required autocomplete="new-password" />
          
          <svg id="toggleRegisterConfirmPassword" class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#888"/>
          </svg>
        </div>

        <label for="role" style="margin-top:10px; color:#555; font-weight:600;">Role</label>
        <select name="role" id="role" class="input-field" required>
         
          <option value="staff" selected>Staff</option>
          <option value="manager">Manager</option>
          <option value="admin">Admin</option>
          <option value="super_admin">Super Admin</option>
        </select>

        <label style="margin-top:10px;">
          <input type="checkbox" name="is_active" checked autocomplete="off" />
          
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

    
    document.querySelectorAll('.toggle-password').forEach(function (eyeIcon) {
      eyeIcon.addEventListener('click', function () {
        const input = this.previousElementSibling;  
        if (input && input.type === "password") {
          input.type = "text";
          
          this.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92C21.08 15.71 22 13.66 22 12c0-5.52-4.48-10-10-10-1.66 0-3.23.38-4.63 1.04L12 7zm0 10c-2.76 0-5-2.24-5-5 0-.65.13-1.26.36-1.83L1.08 5.15l1.41-1.41C4.77 4.62 7.34 4 10 4c5.52 0 10 4.48 10 10 0 1.66-.38 3.23-1.04 4.63l-2.92-2.92C17.74 16.74 18 15.65 18 14.5c0-2.76-2.24-5-5-5z" fill="#888"/>';
        } else if (input) {
          input.type = "password";
          
          this.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="#888"/>';
        }
      });
    });
  </script>
</body>
</html>