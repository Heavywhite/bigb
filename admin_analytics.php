<?php
require_once 'connect.php';

session_start();

// For demo, static data simulating analytics
$salesData = [
    ['date' => '2024-05-01', 'sales' => 1200000],
    ['date' => '2024-05-02', 'sales' => 1500000],
    ['date' => '2024-05-03', 'sales' => 900000],
    ['date' => '2024-05-04', 'sales' => 1800000],
    ['date' => '2024-05-05', 'sales' => 2000000],
    ['date' => '2024-05-06', 'sales' => 1700000],
    ['date' => '2024-05-07', 'sales' => 2200000],
];

$topProducts = [
    ['name' => 'Samsung Galaxy A54', 'units' => 150],
    ['name' => 'MacBook Pro 13"', 'units' => 80],
    ['name' => 'Nike Air Max Sneakers', 'units' => 120],
    ['name' => 'Leather Sofa Set', 'units' => 30],
    ['name' => 'Wireless Headphones', 'units' => 90],
];

$customerGrowth = [
    ['month' => 'Jan', 'customers' => 20],
    ['month' => 'Feb', 'customers' => 35],
    ['month' => 'Mar', 'customers' => 50],
    ['month' => 'Apr', 'customers' => 65],
    ['month' => 'May', 'customers' => 80],
];

// Key metrics
$totalSales = array_sum(array_column($salesData, 'sales'));
$totalOrders = 350; // example
$totalCustomers = 120; // example
$avgOrderValue = $totalSales / $totalOrders;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Analytics - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
  <style>
    .analytics-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      flex: 1 1 200px;
      padding: 1.5rem;
      text-align: center;
    }
    .card h3 {
      margin: 0 0 0.5rem;
      font-size: 1.25rem;
      color: #333;
    }
    .card p {
      font-size: 1.5rem;
      font-weight: 700;
      margin: 0;
      color: #FF8800;
    }
    .charts-container {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
    }
    .chart-box {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      flex: 1 1 300px;
      padding: 1rem;
    }
    canvas {
      max-width: 100%;
      height: 300px;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
      <a href="admin_analytics.php" class="nav-item active" aria-current="page">
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
        <h1>Analytics Dashboard</h1>
      </div>
    </header>

    <section class="analytics-cards" aria-label="Key metrics">
      <div class="card" tabindex="0">
        <h3>Total Sales</h3>
        <p>UGX <?= number_format($totalSales) ?></p>
      </div>
      <div class="card" tabindex="0">
        <h3>Total Orders</h3>
        <p><?= $totalOrders ?></p>
      </div>
      <div class="card" tabindex="0">
        <h3>Total Customers</h3>
        <p><?= $totalCustomers ?></p>
      </div>
      <div class="card" tabindex="0">
        <h3>Avg Order Value</h3>
        <p>UGX <?= number_format($avgOrderValue, 2) ?></p>
      </div>
    </section>

    <section class="charts-container" aria-label="Analytics charts">
      <div class="chart-box">
        <h3>Sales Over Time</h3>
        <canvas id="salesChart" role="img" aria-label="Line chart showing sales over time"></canvas>
      </div>
      <div class="chart-box">
        <h3>Top Products</h3>
        <canvas id="topProductsChart" role="img" aria-label="Bar chart showing top selling products"></canvas>
      </div>
      <div class="chart-box">
        <h3>Customer Growth</h3>
        <canvas id="customerGrowthChart" role="img" aria-label="Line chart showing customer growth over months"></canvas>
      </div>
    </section>
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

    // Prepare data for charts
    const salesData = <?= json_encode($salesData) ?>;
    const topProducts = <?= json_encode($topProducts) ?>;
    const customerGrowth = <?= json_encode($customerGrowth) ?>;

    // Sales Over Time Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: salesData.map(d => d.date),
        datasets: [{
          label: 'Sales (UGX)',
          data: salesData.map(d => d.sales),
          borderColor: '#FF8800',
          backgroundColor: 'rgba(255, 136, 0, 0.2)',
          fill: true,
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: value => 'UGX ' + value.toLocaleString()
            }
          }
        }
      }
    });

    // Top Products Chart
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(topProductsCtx, {
      type: 'bar',
      data: {
        labels: topProducts.map(p => p.name),
        datasets: [{
          label: 'Units Sold',
          data: topProducts.map(p => p.units),
          backgroundColor: '#FF8800',
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { mode: 'nearest' }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 20 }
          }
        }
      }
    });

    // Customer Growth Chart
    const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
    const customerGrowthChart = new Chart(customerGrowthCtx, {
      type: 'line',
      data: {
        labels: customerGrowth.map(c => c.month),
        datasets: [{
          label: 'New Customers',
          data: customerGrowth.map(c => c.customers),
          borderColor: '#FF8800',
          backgroundColor: 'rgba(255, 136, 0, 0.2)',
          fill: true,
          tension: 0.3,
          pointRadius: 5,
          pointHoverRadius: 7,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { mode: 'index', intersect: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { stepSize: 10 }
          }
        }
      }
    });
  </script>
</body>
</html>