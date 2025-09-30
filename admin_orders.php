<?php
require_once 'connect.php';

session_start();

// Initialize orders data in session for demo (replace with DB in real app)
if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = [
        [
            'id' => 101,
            'customer' => 'John Doe',
            'date' => '2024-06-01',
            'status' => 'Processing',
            'total' => 1500000,
            'items' => [
                ['name' => 'Samsung Galaxy A54', 'qty' => 1, 'price' => 850000],
                ['name' => 'Nike Air Max Sneakers', 'qty' => 2, 'price' => 325000],
            ],
            'shipping_address' => '123 Main St, Kampala',
            'payment_method' => 'Credit Card',
        ],
        [
            'id' => 102,
            'customer' => 'Jane Smith',
            'date' => '2024-06-02',
            'status' => 'Shipped',
            'total' => 320000,
            'items' => [
                ['name' => 'Leather Sofa Set', 'qty' => 1, 'price' => 1200000],
            ],
            'shipping_address' => '456 Elm St, Entebbe',
            'payment_method' => 'Mobile Money',
        ],
        [
            'id' => 103,
            'customer' => 'Alice Johnson',
            'date' => '2024-06-03',
            'status' => 'Delivered',
            'total' => 4500000,
            'items' => [
                ['name' => 'MacBook Pro 13"', 'qty' => 1, 'price' => 4500000],
            ],
            'shipping_address' => '789 Oak St, Jinja',
            'payment_method' => 'PayPal',
        ],
    ];
}

$orders = &$_SESSION['orders'];

// Helper functions
function formatUGX($amount) {
    return 'UGX ' . number_format($amount);
}

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

// Handle POST requests for update status or delete
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $orderId = intval($_POST['id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
        if (in_array($newStatus, $validStatuses)) {
            foreach ($orders as &$order) {
                if ($order['id'] === $orderId) {
                    $order['status'] = $newStatus;
                    $message = "Order #$orderId status updated to $newStatus.";
                    break;
                }
            }
            unset($order);
        } else {
            $error = 'Invalid status selected.';
        }
    } elseif ($action === 'delete') {
        $orderId = intval($_POST['id'] ?? 0);
        foreach ($orders as $key => $order) {
            if ($order['id'] === $orderId) {
                unset($orders[$key]);
                $orders = array_values($orders);
                $message = "Order #$orderId deleted successfully.";
                break;
            }
        }
    }
}

// Pagination and filtering variables
$search = trim($_GET['search'] ?? '');
$filterStatus = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// Filter orders
$filtered = array_filter($orders, function ($o) use ($search, $filterStatus) {
    $matchesSearch = $search === '' || stripos($o['customer'], $search) !== false || stripos((string)$o['id'], $search) !== false;
    $matchesStatus = $filterStatus === '' || $o['status'] === $filterStatus;
    return $matchesSearch && $matchesStatus;
});

$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages) $page = $totalPages > 0 ? $totalPages : 1;

$offset = ($page - 1) * $perPage;
$ordersPage = array_slice($filtered, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Orders Management - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
  <style>
    /* Modal styles */
    .modal-overlay {
      position: fixed;
      top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
      padding: 1rem;
    }
    .modal-overlay.active {
      display: flex;
    }
    .modal-content {
      background: white;
      border-radius: 12px;
      max-width: 600px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      padding: 1.5rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    .modal-close {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
    }
    .order-items {
      border-collapse: collapse;
      width: 100%;
      margin-top: 1rem;
    }
    .order-items th, .order-items td {
      border: 1px solid #ddd;
      padding: 0.5rem;
      text-align: left;
    }
    .order-items th {
      background-color: #f8f8f8;
    }
    .btn-primary, .btn-secondary {
      padding: 0.5rem 1rem;
      border-radius: 12px;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 1rem;
    }
    .btn-primary {
      background-color: #FF8800;
      color: white;
    }
    .btn-primary:hover {
      background-color: #E67500;
    }
    .btn-secondary {
      background-color: #eee;
      color: #333;
      margin-right: 0.5rem;
    }
    .btn-secondary:hover {
      background-color: #ddd;
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
      <a href="admin_orders.php" class="nav-item active" aria-current="page">
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
        <h1>Orders Management</h1>
      </div>
      <div class="header-right">
        <form method="get" action="admin_orders.php" style="display:flex; gap:0.5rem; align-items:center;">
          <input type="text" name="search" placeholder="Search orders..." value="<?= htmlspecialchars($search) ?>" aria-label="Search orders" />
          <select name="status" aria-label="Filter by order status">
            <option value="">All Statuses</option>
            <option value="Pending" <?= $filterStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Processing" <?= $filterStatus === 'Processing' ? 'selected' : '' ?>>Processing</option>
            <option value="Shipped" <?= $filterStatus === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="Delivered" <?= $filterStatus === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="Cancelled" <?= $filterStatus === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
          <button type="submit" class="btn-secondary" aria-label="Apply filters">Filter</button>
        </form>
      </div>
    </header>

    <div class="dashboard-content" style="margin-top:1rem;">
      <!-- Messages -->
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

      <!-- Orders Table -->
      <div class="table-container" role="region" aria-label="Orders table">
        <table class="data-table" aria-describedby="ordersTableDesc">
          <caption id="ordersTableDesc" class="sr-only">List of customer orders with details and actions</caption>
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($ordersPage)): ?>
              <tr>
                <td colspan="6" style="text-align:center; padding:1rem;">No orders found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($ordersPage as $order): ?>
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
                  <td>
                    <button class="icon-btn" aria-label="View order #<?= htmlspecialchars($order['id']) ?>" onclick="openOrderModal(<?= $order['id'] ?>)">👁️</button>
                    <form method="post" action="admin_orders.php" style="display:inline;" onsubmit="return confirm('Delete order #<?= addslashes($order['id']) ?>?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= $order['id'] ?>" />
                      <button type="submit" class="icon-btn delete" aria-label="Delete order #<?= htmlspecialchars($order['id']) ?>">🗑️</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Pagination">
          <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="pagination-btn" aria-label="Previous page">Previous</a>
          <?php else: ?>
            <span class="pagination-btn disabled" aria-disabled="true">Previous</span>
          <?php endif; ?>

          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <?php if ($p == $page): ?>
              <span class="pagination-btn active" aria-current="page"><?= $p ?></span>
            <?php else: ?>
              <a href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>" class="pagination-btn"><?= $p ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="pagination-btn" aria-label="Next page">Next</a>
          <?php else: ?>
            <span class="pagination-btn disabled" aria-disabled="true">Next</span>
          <?php endif; ?>
        </nav>
      <?php endif; ?>
    </div>
  </main>

  <!-- Order Details Modal -->
  <div class="modal-overlay" id="orderModal" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="orderModalTitle">Order Details</h2>
        <button class="modal-close" id="orderModalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <div id="orderDetailsContent">
        <!-- Filled dynamically -->
      </div>
    </div>
  </div>

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

    // Modal elements
    const orderModal = document.getElementById('orderModal');
    const orderModalCloseBtn = document.getElementById('orderModalCloseBtn');
    const orderDetailsContent = document.getElementById('orderDetailsContent');

    // Orders data from PHP
    const orders = <?= json_encode(array_values($orders)) ?>;

    // Open order modal and populate details
    function openOrderModal(id) {
      const order = orders.find(o => o.id === id);
      if (!order) return;

      let html = `
        <p><strong>Order ID:</strong> #${order.id}</p>
        <p><strong>Customer:</strong> ${order.customer}</p>
        <p><strong>Date:</strong> ${order.date}</p>
        <p><strong>Status:</strong> <span class="status-badge ${order.status.toLowerCase()}">${order.status}</span></p>
        <p><strong>Total:</strong> UGX ${order.total.toLocaleString()}</p>
        <p><strong>Shipping Address:</strong> ${order.shipping_address}</p>
        <p><strong>Payment Method:</strong> ${order.payment_method}</p>
        <h3>Items</h3>
        <table class="order-items" aria-label="Order items">
          <thead          <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Price (UGX)</th>
            <th>Subtotal (UGX)</th>
          </tr>
          </thead>
          <tbody>
      `;

      order.items.forEach(item => {
        const subtotal = item.qty * item.price;
        html += `
          <tr>
            <td>${item.name}</td>
            <td>${item.qty}</td>
            <td>${item.price.toLocaleString()}</td>
            <td>${subtotal.toLocaleString()}</td>
          </tr>
        `;
      });

      html += `
          </tbody>
        </table>
        <form method="post" action="admin_orders.php" style="margin-top:1rem;">
          <input type="hidden" name="action" value="update_status" />
          <input type="hidden" name="id" value="${order.id}" />
          <label for="orderStatusSelect">Update Status:</label>
          <select id="orderStatusSelect" name="status" required>
            <option value="Pending" ${order.status === 'Pending' ? 'selected' : ''}>Pending</option>
            <option value="Processing" ${order.status === 'Processing' ? 'selected' : ''}>Processing</option>
            <option value="Shipped" ${order.status === 'Shipped' ? 'selected' : ''}>Shipped</option>
            <option value="Delivered" ${order.status === 'Delivered' ? 'selected' : ''}>Delivered</option>
            <option value="Cancelled" ${order.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
          </select>
          <button type="submit" class="btn-primary" style="margin-left:1rem;">Update</button>
        </form>
      `;

      orderDetailsContent.innerHTML = html;
      orderModal.classList.add('active');
      orderModal.setAttribute('aria-hidden', 'false');
    }

    // Close modal
    orderModalCloseBtn.addEventListener('click', () => {
      orderModal.classList.remove('active');
      orderModal.setAttribute('aria-hidden', 'true');
      orderDetailsContent.innerHTML = '';
    });

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && orderModal.classList.contains('active')) {
        orderModal.classList.remove('active');
        orderModal.setAttribute('aria-hidden', 'true');
        orderDetailsContent.innerHTML = '';
      }
    });
  </script>
</body>
</html>