<?php
// admin_customers.php
session_start();

// Initialize customers data in session for demo (replace with DB in real app)
if (!isset($_SESSION['customers'])) {
    $_SESSION['customers'] = [
        [
            'id' => 1,
            'name' => 'Niwe Isaac',
            'email' => 'john@example.com',
            'phone' => '+256700123456',
            'joined' => '2024-05-20',
            'orders_count' => 5,
            'address' => '123 Main St, Kampala',
        ],
        [
            'id' => 2,
            'name' => 'Hzyf Sgy',
            'email' => 'Hzyf@example.com',
            'phone' => '+256701234567',
            'joined' => '2024-05-22',
            'orders_count' => 3,
            'address' => '456 Elm St, Entebbe',
        ],
        [
            'id' => 3,
            'name' => 'Collins Tum',
            'email' => 'Tum@example.com',
            'phone' => '+256702345678',
            'joined' => '2024-05-25',
            'orders_count' => 7,
            'address' => '789 Oak St, Jinja',
        ],
        // Add more sample customers as needed
    ];
}

$customers = &$_SESSION['customers'];

// Handle POST requests for delete
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $deleteId = intval($_POST['id'] ?? 0);
        foreach ($customers as $key => $customer) {
            if ($customer['id'] === $deleteId) {
                unset($customers[$key]);
                $customers = array_values($customers);
                $message = "Customer \"{$customer['name']}\" deleted successfully.";
                break;
            }
        }
    }
}

// Pagination and filtering variables
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// Filter customers
$filtered = array_filter($customers, function ($c) use ($search) {
    return $search === '' || stripos($c['name'], $search) !== false || stripos($c['email'], $search) !== false;
});

$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages) $page = $totalPages > 0 ? $totalPages : 1;

$offset = ($page - 1) * $perPage;
$customersPage = array_slice($filtered, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customers Management - BigB Admin</title>
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
      max-width: 500px;
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
      <a href="admin_orders.php" class="nav-item">
        <span class="nav-icon">🛒</span>
        <span class="nav-text">Orders</span>
      </a>
      <a href="admin_customers.php" class="nav-item">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Customers</span>
      </a>
      <a href="admin_categories.php" class="nav-item active" aria-current="page">
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
      <a href="index.php" class="nav-item" onclick="adminLogout()">
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
        <h1>Customers Management</h1>
      </div>
      <div class="header-right">
        <form method="get" action="admin_customers.php" style="display:flex; gap:0.5rem; align-items:center;">
          <input type="text" name="search" placeholder="Search customers..." value="<?= htmlspecialchars($search) ?>" aria-label="Search customers" />
          <button type="submit" class="btn-secondary" aria-label="Search">Search</button>
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

      <!-- Customers Table -->
      <div class="table-container" role="region" aria-label="Customers table">
        <table class="data-table" aria-describedby="customersTableDesc">
          <caption id="customersTableDesc" class="sr-only">List of customers with details and actions</caption>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Joined</th>
              <th>Orders</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($customersPage)): ?>
              <tr>
                <td colspan="6" style="text-align:center; padding:1rem;">No customers found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($customersPage as $customer): ?>
                <tr>
                  <td><?= htmlspecialchars($customer['name']) ?></td>
                  <td><?= htmlspecialchars($customer['email']) ?></td>
                  <td><?= htmlspecialchars($customer['phone']) ?></td>
                  <td><?= htmlspecialchars($customer['joined']) ?></td>
                  <td><?= intval($customer['orders_count']) ?></td>
                  <td>
                    <button class="icon-btn" aria-label="View details of <?= htmlspecialchars($customer['name']) ?>" onclick="openCustomerModal(<?= $customer['id'] ?>)">👁️</button>
                    <form method="post" action="admin_customers.php" style="display:inline;" onsubmit="return confirm('Delete customer <?= addslashes($customer['name']) ?>?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= $customer['id'] ?>" />
                      <button type="submit" class="icon-btn delete" aria-label="Delete <?= htmlspecialchars($customer['name']) ?>">🗑️</button>
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

  <!-- Customer Details Modal -->
  <div class="modal-overlay" id="customerModal" role="dialog" aria-modal="true" aria-labelledby="customerModalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="customerModalTitle">Customer Details</h2>
        <button class="modal-close" id="customerModalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <div id="customerDetailsContent">
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
    const customerModal = document.getElementById('customerModal');
    const customerModalCloseBtn = document.getElementById('customerModalCloseBtn');
    const customerDetailsContent = document.getElementById('customerDetailsContent');

    // Customers data from PHP
    const customers = <?= json_encode(array_values($customers)) ?>;

    // Open customer modal and populate details
    function openCustomerModal(id) {
      const customer = customers.find(c => c.id === id);
      if (!customer) return;

      let html = `
        <p><strong>Name:</strong> ${customer.name}</p>
        <p><strong>Email:</strong> ${customer.email}</p>
        <p><strong>Phone:</strong> ${customer.phone}</p>
        <p><strong>Joined:</strong> ${customer.joined}</p>
        <p><strong>Orders Count:</strong> ${customer.orders_count}</p>
        <p><strong>Address:</strong> ${customer.address}</p>
      `;

      customerDetailsContent.innerHTML = html;
      customerModal.classList.add('active');
      customerModal.setAttribute('aria-hidden', 'false');
    }

    // Close modal
    customerModalCloseBtn.addEventListener('click', () => {
      customerModal.classList.remove('active');
      customerModal.setAttribute('aria-hidden', 'true');
      customerDetailsContent.innerHTML = '';
    });

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && customerModal.classList.contains('active')) {
        customerModal.classList.remove('active');
        customerModal.setAttribute('aria-hidden', 'true');
        customerDetailsContent.innerHTML = '';
      }
    });
  </script>
</body>
</html>