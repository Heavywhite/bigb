<?php
// admin_settings.php
session_start();

// Initialize settings in session for demo (replace with DB in real app)
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'site_name' => 'BigB Online Store',
        'contact_email' => 'support@bigb.com',
        'contact_phone' => '+256 700 000000',
        'payment_methods' => ['paypal' => true, 'stripe' => false, 'cod' => true],
        'shipping_options' => ['standard' => true, 'express' => true],
        'admin_password' => 'admin123', // For demo only, never store plain passwords in real apps
    ];
}

$settings = &$_SESSION['settings'];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = trim($_POST['site_name'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $payment_paypal = isset($_POST['payment_paypal']);
    $payment_stripe = isset($_POST['payment_stripe']);
    $payment_cod = isset($_POST['payment_cod']);
    $shipping_standard = isset($_POST['shipping_standard']);
    $shipping_express = isset($_POST['shipping_express']);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($site_name === '' || $contact_email === '') {
        $error = 'Site name and contact email are required.';
    } elseif ($new_password !== '' && $new_password !== $confirm_password) {
        $error = 'New password and confirm password do not match.';
    } else {
        $settings['site_name'] = $site_name;
        $settings['contact_email'] = $contact_email;
        $settings['contact_phone'] = $contact_phone;
        $settings['payment_methods'] = [
            'paypal' => $payment_paypal,
            'stripe' => $payment_stripe,
            'cod' => $payment_cod,
        ];
        $settings['shipping_options'] = [
            'standard' => $shipping_standard,
            'express' => $shipping_express,
        ];
        if ($new_password !== '') {
            $settings['admin_password'] = $new_password; // In real app, hash password
        }
        $message = 'Settings saved successfully.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
  <style>
    form.settings-form {
      max-width: 600px;
      margin: 1rem auto;
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.3rem;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
      width: 100%;
      padding: 0.5rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }
    .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .checkbox-group label {
      font-weight: normal;
    }
    .btn-primary {
      background-color: #FF8800;
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
    }
    .btn-primary:hover {
      background-color: #E67500;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin Sidebar">
    <div class="sidebar-header">
      <h2 class="logo">BigB Admin</h2>
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
    </div>
    <nav class="sidebar-nav" role="navigation" aria-label="Admin Navigation">
      <a href="admin_dashboard.php" class="nav-item">
        <span class="nav-icon">📊</span>
        <span class="nav-text">Dashboard</span>
      </a>
      <a href="admin_products.php" class="nav-item">
        <span class="nav-icon">📦</span>
        <span class="nav-text">Products</span>
      </a>
      <a href="admin_orders.php" class="nav-item">
        <span class="nav-icon">🛒</span>
        <span class="nav-text">Orders</span>
      </a>
      <a href="admin_customers.php" class="nav-item">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Customers</span>
      </a>
      <a href="admin_categories.php" class="nav-item">
        <span class="nav-icon">📂</span>
        <span class="nav-text">Categories</span>
      </a>
      <a href="admin_inventory.php" class="nav-item">
        <span class="nav-icon">📋</span>
        <span class="nav-text">Inventory</span>
      </a>
      <a href="admin_analytics.php" class="nav-item">
        <span class="nav-icon">📈</span>
        <span class="nav-text">Analytics</span>
      </a>
      <a href="admin_promo.php" class="nav-item">
        <span class="nav-icon">🎟️</span>
        <span class="nav-text">Promo Codes</span>
      </a>
      <a href="admin_reviews.php" class="nav-item">
        <span class="nav-icon">⭐</span>
        <span class="nav-text">Reviews</span>
      </a>
      <a href="admin_settings.php" class="nav-item active" aria-current="page">
        <span class="nav-icon">⚙️</span>
        <span class="nav-text">Settings</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="index.php" class="nav-item">
        <span class="nav-icon">🏠</span>
        <span class="nav-text">View Store</span>
      </a>
      <a href="#" class="nav-item" onclick="adminLogout()">
        <span class="nav-icon">🚪</span>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main" id="mainContent">
    <header class="admin-header">
      <div class="header-left">
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">☰</button>
        <h1>Settings</h1>
      </div>
    </header>

    <div class="dashboard-content" style="margin-top:1rem;">
      <?php if ($message): ?>
        <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:8px; margin-bottom:1rem;">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:8px; margin-bottom:1rem;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="settings-form" novalidate>
        <fieldset>
          <legend><strong>Site Information</strong></legend>
          <div class="form-group">
            <label for="siteName">Site Name *</label>
            <input type="text" id="siteName" name="site_name" required value="<?= htmlspecialchars($settings['site_name']) ?>" />
          </div>
          <div class="form-group">
            <label for="contactEmail">Contact Email *</label>
            <input type="email" id="contactEmail" name="contact_email" required value="<?= htmlspecialchars($settings['contact_email']) ?>" />
          </div>
          <div class="form-group">
            <label for="contactPhone">Contact Phone</label>
            <input type="text" id="contactPhone" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>" />
          </div>
        </fieldset>

        <fieldset>
          <legend><strong>Payment Methods</strong></legend>
          <div class="checkbox-group">
            <label><input type="checkbox" name="payment_paypal" <?= $settings['payment_methods']['paypal'] ? 'checked' : '' ?> /> PayPal</label>
            <label><input type="checkbox" name="payment_stripe" <?= $settings['payment_methods']['stripe'] ? 'checked' : '' ?> /> Stripe</label>
            <label><input type="checkbox" name="payment_cod" <?= $settings['payment_methods']['cod'] ? 'checked' : '' ?> /> Cash on Delivery</label>
          </div>
        </fieldset>

        <fieldset>
          <legend><strong>Shipping Options</strong></legend>
          <div class="checkbox-group">
            <label><input type="checkbox" name="shipping_standard" <?= $settings['shipping_options']['standard'] ? 'checked' : '' ?> /> Standard Shipping</label>
            <label><input type="checkbox" name="shipping_express" <?= $settings['shipping_options']['express'] ? 'checked' : '' ?> /> Express Shipping</label>
          </div>
        </fieldset>

        <fieldset>
          <legend><strong>Security</strong></legend>
          <div class="form-group">
            <label for="newPassword">New Admin Password</label>
            <input type="password" id="newPassword" name="new_password" autocomplete="new-password" />
          </div>
          <div class="form-group">
            <label for="confirmPassword">Confirm New Password</label>
            <input type="password" id="confirmPassword" name="confirm_password" autocomplete="new-password" />
          </div>
          <small>Leave password fields blank to keep current password.</small>
        </fieldset>

        <button type="submit" class="btn-primary">Save Settings</button>
      </form>
    </div>
  </main>

  <script>
    // Sidebar toggle
    document.getElementById('sidebarToggle').addEventListener('click', () => {
      document.getElementById('adminSidebar').classList.toggle('active');
    });
    document.getElementById('mobileToggle')?.addEventListener('click', () => {
      document.getElementById('adminSidebar').classList.toggle('active');
    });

    // Logout function
    function adminLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'index.php';
      }
    }
  </script>
</body>
</html>