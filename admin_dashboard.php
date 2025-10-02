<?php
session_start();
require_once 'db_connect.php';

// Authentication check: Only logged-in admins can access
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_auth.php');
    exit();
}


$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    $_SESSION['error'] = 'Database connection failed. Using fallback data.';
    $conn = null;  
}

if ($conn) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// Initialize data with fallbacks
$totalProducts = 892;
$activeProducts = 847;
$lowStock = 15;
$outOfStock = 7;

$recentOrders = [
    ['id' => 101, 'customer' => 'Isaac Ni', 'date' => '2024-06-01', 'status' => 'Processing', 'total' => 1500000],
    ['id' => 102, 'customer' => 'Hzyf Sgy', 'date' => '2024-06-02', 'status' => 'Shipped', 'total' => 320000],
    ['id' => 103, 'customer' => 'Collins Tum', 'date' => '2024-06-03', 'status' => 'Delivered', 'total' => 4500000],
    ['id' => 104, 'customer' => 'Mugisha Najib', 'date' => '2024-06-04', 'status' => 'Cancelled', 'total' => 1200000],
];

$recentCustomers = [  // Will be overridden by DB query for admins
    ['name' => 'Isaac Ni', 'email' => 'isaac@example.com', 'joined' => '2024-05-20'],
    ['name' => 'Hzyf Sgy', 'email' => 'Hzyf@example.com', 'joined' => '2024-05-22'],
    ['name' => 'Collins Tum', 'email' => 'Tum@example.com', 'joined' => '2024-05-25'],
    ['name' => 'Mugisha Najib', 'email' => 'Najib@example.com', 'joined' => '2024-05-28'],
];

// Dynamic data fetches
if ($conn) {
    // Products stats (assumes 'products' table: id, status ENUM('active','inactive'), stock INT)
    $stmt = $conn->prepare("SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN stock < 10 AND stock > 0 THEN 1 ELSE 0 END) AS low_stock,
        SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) AS out_of_stock
        FROM products");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();
        $totalProducts = $stats['total'] ?? $totalProducts;
        $activeProducts = $stats['active'] ?? $activeProducts;
        $lowStock = $stats['low_stock'] ?? $lowStock;
        $outOfStock = $stats['out_of_stock'] ?? $outOfStock;
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Products query failed. Using fallback data.';
    }

    // Recent Orders (assumes 'orders' table: id, customer_name, order_date, status, total; last 4)
    $stmt = $conn->prepare("SELECT id, customer_name AS customer, order_date AS date, status, total 
        FROM orders ORDER BY order_date DESC LIMIT 4");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $recentOrders = [];
        while ($row = $result->fetch_assoc()) {
            $recentOrders[] = $row;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Orders query failed. Using fallback data.';
    }

    // Recent Customers (from admin_users: last 4 joined admins)
    $stmt = $conn->prepare("SELECT name, email, created_at AS joined 
        FROM admin_users ORDER BY created_at DESC LIMIT 4");
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        $recentCustomers = [];
        while ($row = $result->fetch_assoc()) {
            $recentCustomers[] = $row;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Customers query failed. Using fallback data.';
    }

    $conn->close();
}

// Get admin details from session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$admin_role = $_SESSION['admin_role'] ?? 'Staff';

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
  <title>Admin Dashboard - BIGb</title>
  <link rel="stylesheet" href="admin_styles.css" />
  <style>
    /* Main content: Offset for sidebar, full width on mobile */
    .admin-main {
      margin-left: 250px; /* Shift right to avoid sidebar */
      min-height: 100vh; padding: 1rem;
      transition: margin-left 0.3s ease;
    }
    @media (max-width: 768px) {
      .admin-main { margin-left: 0; } /* Full width on mobile */
      .admin-sidebar { width: 100%; transform: translateX(-100%); } /* Hidden by default on mobile */
    }
    
    /* Welcome and messages: Now inside main, with z-index and padding */
    .welcome { 
      background: #e8f5e8; padding: 1rem; border-radius: 5px; margin-bottom: 2rem;
      position: relative; z-index: 10; /* FIXED: Ensure above other elements */
      box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Subtle shadow for visibility */
    }
    .message { 
      padding: 1rem; border-radius: 8px; margin-bottom: 1rem; position: relative; z-index: 10;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
    .error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
    
    /* Other elements (unchanged) */
    .status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.processing { background: #cce5ff; color: #004085; }
    .status-badge.shipped { background: #d1ecf1; color: #0c5460; }
    .status-badge.delivered { background: #d4edda; color: #155724; }
    .status-badge.cancelled { background: #f8d7da; color: #721c24; }
    .activity-item { padding: 0.75rem 1rem; border-bottom: 1px solid #eee; }
    .stat-number.warning { color: #856404; }
    .stat-number.danger { color: #721c24; }
    
    /* Header and content basics */
    .admin-header { background: white; padding: 1rem; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
    .dashboard-content { padding: 1rem 0; }
    .products-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-box { background: white; padding: 1.5rem; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .stat-number { font-size: 2rem; font-weight: bold; margin: 0; }
    .table-container { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ddd; }
    .data-table th { background: #f8f9fa; font-weight: bold; }
    .activity-list { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }
  </style>
</head>
<body>
  <!-- Sidebar (complete) -->
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
      <a href="admin_approve_users.php" class="nav-item">
        <span class="nav-icon">✅</span>
        <span class="nav-text">Approve Users</span>
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
      <a href="inventory.php" class="nav-item">
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
         aria-label="Logout">
        <span class="nav-icon">🚪</span>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main Content (welcome and messages now INSIDE here to avoid overlap) -->
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

    <!-- FIXED: Session messages and welcome now inside main (after header) -->
    <?php if (!empty($_SESSION['success'])): ?>
      <div class="message success"><?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="welcome">
      <p>Welcome back, <strong><?= htmlspecialchars($admin_name) ?></strong>! Your role is <strong><?= htmlspecialchars($admin_role) ?></strong>.</p>
    </div>

    <div class="dashboard-content">
      <!-- Stats Cards -->
      <div class="products-stats">
        <div class="stat-box" tabindex="0">
          <h4>Total Products</h4>
          <p class="stat-number"><?= htmlspecialchars($totalProducts) ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Active Products</h4>
          <p class="stat-number"><?= htmlspecialchars($activeProducts) ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Low Stock</h4>
          <p class="stat-number warning"><?= htmlspecialchars($lowStock) ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Out of Stock</h4>
          <p class="stat-number danger"><?= htmlspecialchars($outOfStock) ?></p>
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
        <h2 id="recentCustomersTitle">Recent Admins</h2>  <!-- Renamed for accuracy (from admin_users) -->
        <ul class="activity-list" style="list-style:none; padding-left:0;">
          <?php foreach ($recentCustomers as $customer): ?>
            <li class="activity-item" tabindex="0">
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
</body>
</html>