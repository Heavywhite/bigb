<?php
require_once 'connect.php';

// Sample backend data (replace with real DB queries)
$totalProducts = 892;
$activeProducts = 847;
$lowStock = 15;
$outOfStock = 7;

$recentOrders = [
    ['id' => 101, 'customer' => 'Isaac Ni', 'date' => '2024-06-01', 'status' => 'Processing', 'total' => 1500000],
    ['id' => 102, 'customer' => 'Jane Smith', 'date' => '2024-06-02', 'status' => 'Shipped', 'total' => 320000],
    ['id' => 103, 'customer' => 'Alice Johnson', 'date' => '2024-06-03', 'status' => 'Delivered', 'total' => 4500000],
    ['id' => 104, 'customer' => 'Bob Brown', 'date' => '2024-06-04', 'status' => 'Cancelled', 'total' => 1200000],
];

$recentCustomers = [
    ['name' => 'Isaac Ni', 'email' => 'isaac@example.com', 'joined' => '2024-05-20'],
    ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'joined' => '2024-05-22'],
    ['name' => 'Alice Johnson', 'email' => 'alice@example.com', 'joined' => '2024-05-25'],
    ['name' => 'Bob Brown', 'email' => 'bob@example.com', 'joined' => '2024-05-28'],
];

// Helper function to format currency
function formatUGX($amount) {
    return 'UGX ' . number_format($amount);
}

// Helper function to get status badge class
function orderStatusClass($status) {
    $map = [
        'Pending' => 'pending',
        'Processing' => 'processing',
        'Shipped' => 'shipped',
        'Delivered' => 'delivered',
        'Cancelled' => 'cancelled',
    ];
    return $map[$status] ?? 'pending';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
</head>
<body>
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar" aria-label="Admin Sidebar">
    <div class="sidebar-header">
      <h2 class="logo">BigB Admin</h2>
      <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
    </div>
    <nav class="sidebar-nav" role="navigation" aria-label="Admin Navigation">
      <a href="admin_dashboard.php" class="nav-item active" aria-current="page">
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
      <a href="admin_settings.php" class="nav-item">
        <span class="nav-icon">⚙️</span>
        <span class="nav-text">Settings</span>
      </a>
    </nav>
    <div class="sidebar-footer">
      <a href="index.php" class="nav-item">
        <span class="nav-icon">🏠</span>
        <span class="nav-text">View Store</span>
      </a>
       
      <a href="logout.php" class="nav-item" 
         onclick="return confirm('Are you sure you want to logout?');" 
         aria-label="Logout"
         style="display:flex; align-items:center; padding:0.5rem 0; color:#333; text-decoration:none; font-weight:600; cursor:pointer;">
        <span class="nav-icon" aria-hidden="true" style="margin-right:0.5rem; font-size:1.2rem;">🚪</span>
        <span class="nav-text">Logout</span>
      </a>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main" id="mainContent">
    <header class="admin-header">
      <div class="header-left">
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">☰</button>
        <h1>Dashboard</h1>
      </div>
      <div class="header-right">
        <!-- Optional: search, notifications, profile -->
      </div>
    </header>

    <div class="dashboard-content">
      <!-- Stats Cards -->
      <div class="products-stats">
        <div class="stat-box" tabindex="0">
          <h4>Total Products</h4>
          <p class="stat-number"><?= $totalProducts ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Active Products</h4>
          <p class="stat-number"><?= $activeProducts ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Low Stock</h4>
          <p class="stat-number warning"><?= $lowStock ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Out of Stock</h4>
          <p class="stat-number danger"><?= $outOfStock ?></p>
        </div>
      </div>

      <!-- Recent Orders -->
      <section aria-labelledby="recentOrdersTitle" style="margin-top:2rem;">
        <h2 id="recentOrdersTitle">Recent Orders</h2>
        <div class="table-container">
          <table class="data-table" aria-describedby="recentOrdersDesc">
            <caption id="recentOrdersDesc" class="sr-only">List of recent orders</caption>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td>#<?= htmlspecialchars($order['id']) ?></td>
                  <td><?= htmlspecialchars($order['customer']) ?></td>
                  <td><?= htmlspecialchars($order['date']) ?></td>
                  <td>
                    <span class="status-badge <?= orderStatusClass($order['status']) ?>">
                      <?= htmlspecialchars($order['status']) ?>
                    </span>
                  </td>
                  <td><?= formatUGX($order['total']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Recent Customers -->
      <section aria-labelledby="recentCustomersTitle" style="margin-top:2rem;">
        <h2 id="recentCustomersTitle">Recent Customers</h2>
        <ul class="activity-list" style="list-style:none; padding-left:0;">
          <?php foreach ($recentCustomers as $customer): ?>
            <li class="activity-item" tabindex="0" style="padding:0.75rem 1rem; border-bottom:1px solid #eee;">
              <div class="activity-details">
                <p><strong><?= htmlspecialchars($customer['name']) ?></strong></p>
                <p><?= htmlspecialchars($customer['email']) ?></p>
                <p style="font-size:0.85rem; color:#666;">Joined: <?= htmlspecialchars($customer['joined']) ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    </div>
  </main>

  <script src="admin_script.js"></script>
  <script>
    // Simple logout function
    function adminLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'index.php'; // Adjust as needed
      }
    }
  </script>
</body>
</html>