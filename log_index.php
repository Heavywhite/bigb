<?php
//include 'connect.php';  // Uncomment if needed for index.php (e.g., for other DB queries)
session_start();
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
    echo "<script>alert(" . json_encode($error_message) . ");</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BIGb online shopping</title>
  <link rel="stylesheet" href="login.style.css" />
  <style>
    /* Container for password input + eye icon */
    .password-wrapper {
      position: relative;
      width: 100%;
    }
    .password-wrapper input[type="password"],
    .password-wrapper input[type="text"] {
      width: 100%;
      padding-right: 35px; /* space for eye icon */
      box-sizing: border-box;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      width: 20px;
      height: 20px;
      fill: #666;
      user-select: none;
    }
    .toggle-password:hover {
      fill: #ff6124;
    }
  </style>
</head>
<body>
  <!--<header>
    <div class="hea"> 
       <img src="logo.svg" width="350px" height="150px" > 
    </div> 
   </header>-->

  <div class="hero">
    <div class="form-box">
      <div class="button-box">
        <div id="btn"></div>
        <button type="button" class="toggle-btn" onclick="login()">Log In</button>
        <button type="button" class="toggle-btn" onclick="register()">Register</button>
      </div>
      <form id="Login" class="input-group" action="login.php" method="POST" autocomplete="off">
        <input id="login-input" type="text" name="input" class="input-field" placeholder="Enter email or phone" required />
        
        <div class="password-wrapper">
          <input id="login-password" type="password" name="password" class="input-field" placeholder="Enter password" required />
          <svg class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" fill="none" stroke="#666" stroke-width="2"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="#666" stroke-width="2"/>
          </svg>
        </div>

        <input type="checkbox" class="check-box" /><span>Remember Me</span>
        <button type="submit" class="submit-btn">Log in</button>
      </form>

      <form id="register" class="input-group" action="register.php" method="POST" autocomplete="off">
        <input id="name" type="text" name="name" class="input-field" placeholder="Enter name" required />
        <input id="email" type="email" name="email" class="input-field" placeholder="Enter email" required />
        <input id="phone" type="tel" name="phone" class="input-field" placeholder="+256" required />

        <div class="password-wrapper">
          <input id="register-password" type="password" name="password" class="input-field" placeholder="Enter password" required />
          <svg class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" fill="none" stroke="#666" stroke-width="2"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="#666" stroke-width="2"/>
          </svg>
        </div>

        <div class="password-wrapper">
          <input id="confirm_password" type="password" name="confirm_password" class="input-field" placeholder="Confirm password" required />
          <svg class="toggle-password" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" fill="none" stroke="#666" stroke-width="2"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="#666" stroke-width="2"/>
          </svg>
        </div>

        <!--<input type="checkbox" class="check-box" required><span>I agree to the <a href="#" class="link">terms and policies</a> </span>-->
        <br /><br />
        <button type="submit" class="submit-btn">Register</button>
      </form>
    </div>
  </div>
  <br /><br />

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

    // Password toggle logic for all toggle-password icons
    document.querySelectorAll('.toggle-password').forEach(function (eyeIcon) {
      eyeIcon.addEventListener('click', function () {
        // The input is the previous sibling of the SVG inside the wrapper
        const input = this.previousElementSibling;
        if (input.type === "password") {
          input.type = "text";
          // Change icon to "eye-off" (improved SVG for better rendering)
          this.innerHTML = `
            <path d="M2 2 L22 22" stroke="#666" stroke-width="2" fill="none"/>
            <path d="M12 7c-3.3 0-6 2.7-6 6s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6z" fill="none" stroke="#666" stroke-width="2"/>
            <circle cx="12" cy="12" r="3" fill="none" stroke="#666" stroke-width="2"/>
          `;
        } else {
          input.type = "password";
          // Change icon back to "eye"
          this.innerHTML = `
            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" fill="none" stroke="#666" stroke-width="2"/>
            <circle cx="12" cy="12" r="2.5" fill="none" stroke="#666" stroke-width="2"/>
          `;
        }
      });
    });
  </script>
</body>
</html>