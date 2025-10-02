<?php
// admin_promo.php
session_start();

// Initialize promo codes data in session for demo (replace with DB in real app)
if (!isset($_SESSION['promo_codes'])) {
    $_SESSION['promo_codes'] = [
        [
            'id' => 1,
            'code' => 'BIGB10',
            'description' => '10% off on all products',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'expiry_date' => '2024-12-31',
            'usage_limit' => 100,
            'used' => 25,
            'active' => true,
        ],
        [
            'id' => 2,
            'code' => 'FREESHIP',
            'description' => 'Free shipping on orders over UGX 100,000',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'expiry_date' => '2024-08-31',
            'usage_limit' => 50,
            'used' => 10,
            'active' => true,
        ],
    ];
}

$promoCodes = &$_SESSION['promo_codes'];

// Handle POST requests for add/edit/delete
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');
        $discount_type = $_POST['discount_type'] ?? 'percentage';
        $discount_value = floatval($_POST['discount_value'] ?? 0);
        $expiry_date = $_POST['expiry_date'] ?? '';
        $usage_limit = intval($_POST['usage_limit'] ?? 0);
        $active = isset($_POST['active']) ? true : false;

        if ($code === '') {
            $error = 'Promo code is required.';
        } elseif ($discount_value < 0) {
            $error = 'Discount value must be zero or positive.';
        } elseif ($expiry_date === '') {
            $error = 'Expiry date is required.';
        } else {
            if ($action === 'add') {
                $newId = count($promoCodes) ? max(array_column($promoCodes, 'id')) + 1 : 1;
                $promoCodes[] = [
                    'id' => $newId,
                    'code' => $code,
                    'description' => $description,
                    'discount_type' => $discount_type,
                    'discount_value' => $discount_value,
                    'expiry_date' => $expiry_date,
                    'usage_limit' => $usage_limit,
                    'used' => 0,
                    'active' => $active,
                ];
                $message = "Promo code \"$code\" added successfully.";
            } elseif ($action === 'edit') {
                $editId = intval($_POST['id'] ?? 0);
                foreach ($promoCodes as &$promo) {
                    if ($promo['id'] === $editId) {
                        $promo['code'] = $code;
                        $promo['description'] = $description;
                        $promo['discount_type'] = $discount_type;
                        $promo['discount_value'] = $discount_value;
                        $promo['expiry_date'] = $expiry_date;
                        $promo['usage_limit'] = $usage_limit;
                        $promo['active'] = $active;
                        $message = "Promo code \"$code\" updated successfully.";
                        break;
                    }
                }
                unset($promo);
            }
        }
    } elseif ($action === 'delete') {
        $deleteId = intval($_POST['id'] ?? 0);
        foreach ($promoCodes as $key => $promo) {
            if ($promo['id'] === $deleteId) {
                $deletedCode = $promo['code'];
                unset($promoCodes[$key]);
                $promoCodes = array_values($promoCodes);
                $message = "Promo code \"$deletedCode\" deleted successfully.";
                break;
            }
        }
    }
}

// Pagination and filtering variables
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// Filter promo codes
$filtered = array_filter($promoCodes, function ($p) use ($search) {
    return $search === '' || stripos($p['code'], $search) !== false || stripos($p['description'], $search) !== false;
});

$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages) $page = $totalPages > 0 ? $totalPages : 1;

$offset = ($page - 1) * $perPage;
$promoPage = array_slice($filtered, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Promo Codes Management - BigB Admin</title>
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
    .form-group {
      margin-bottom: 1rem;
    }
    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 0.3rem;
    }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="date"],
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.5rem;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }
    .form-actions {
      text-align: right;
      margin-top: 1rem;
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
    .status-active {
      color: #28a745;
      font-weight: 600;
    }
    .status-inactive {
      color: #dc3545;
      font-weight: 600;
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
        <h1>Promo Codes Management</h1>
      </div>
      <div class="header-right">
        <form method="get" action="admin_promo.php" style="display:flex; gap:0.5rem; align-items:center;">
          <input type="text" name="search" placeholder="Search promo codes..." value="<?= htmlspecialchars($search) ?>" aria-label="Search promo codes" />
          <button type="submit" class="btn-secondary" aria-label="Search">Search</button>
        </form>
        <button class="btn-primary" id="addPromoBtn" aria-haspopup="dialog" aria-controls="promoModal" style="margin-left:1rem;">➕ Add New Promo Code</button>
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

      <!-- Promo Codes Table -->
      <div class="table-container" role="region" aria-label="Promo codes table">
        <table class="data-table" aria-describedby="promoTableDesc">
          <caption id="promoTableDesc" class="sr-only">List of promo codes with details and actions</caption>
          <thead>
            <tr>
              <th>Code</th>
              <th>Description</th>
              <th>Discount</th>
              <th>Expiry Date</th>
              <th>Usage</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($promoPage)): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:1rem;">No promo codes found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($promoPage as $promo): ?>
                <tr>
                  <td><?= htmlspecialchars($promo['code']) ?></td>
                  <td><?= htmlspecialchars($promo['description']) ?></td>
                  <td>
                    <?php
                      if ($promo['discount_type'] === 'percentage') {
                        echo htmlspecialchars($promo['discount_value']) . '%';
                      } else {
                        echo 'UGX ' . number_format($promo['discount_value']);
                      }
                    ?>
                  </td>
                  <td><?= htmlspecialchars($promo['expiry_date']) ?></td>
                  <td><?= intval($promo['used']) ?> / <?= intval($promo['usage_limit']) ?></td>
                  <td>
                    <span class="<?= $promo['active'] ? 'status-active' : 'status-inactive' ?>">
                      <?= $promo['active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td>
                    <button class="icon-btn" aria-label="Edit promo code <?= htmlspecialchars($promo['code']) ?>" onclick="openEditModal(<?= $promo['id'] ?>)">✏️</button>
                    <form method="post" action="admin_promo.php" style="display:inline;" onsubmit="return confirm('Delete promo code <?= addslashes($promo['code']) ?>?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= $promo['id'] ?>" />
                      <button type="submit" class="icon-btn delete" aria-label="Delete promo code <?= htmlspecialchars($promo['code']) ?>">🗑️</button>
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

  <!-- Add/Edit Promo Code Modal -->
  <div class="modal-overlay" id="promoModal" role="dialog" aria-modal="true" aria-labelledby="promoModalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="promoModalTitle">Add New Promo Code</h2>
        <button class="modal-close" id="promoModalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <form id="promoForm" method="post" action="admin_promo.php" novalidate>
        <input type="hidden" name="action" id="formAction" value="add" />
        <input type="hidden" name="id" id="promoId" value="" />
        <div class="form-group">
          <label for="promoCode">Promo Code *</label>
          <input type="text" id="promoCode" name="code" required maxlength="20" />
        </div>
        <div class="form-group">
          <label for="promoDescription">Description</label>
          <textarea id="promoDescription" name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label for="discountType">Discount Type *</label>
          <select id="discountType" name="discount_type" required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed Amount (UGX)</option>
          </select>
        </div>
        <div class="form-group">
          <label for="discountValue">Discount Value *</label>
          <input type="number" id="discountValue" name="discount_value" min="0" step="0.01" required />
        </div>
        <div class="form-group">
          <label for="expiryDate">Expiry Date *</label>
          <input type="date" id="expiryDate" name="expiry_date" required />
        </div>
        <div class="form-group">
          <label for="usageLimit">Usage Limit</label>
          <input type="number" id="usageLimit" name="usage_limit" min="0" />
          <small>0 means unlimited</small>
        </div>
        <div class="form-group">
          <label>
            <input type="checkbox" id="promoActive" name="active" />
            Active
          </label>
        </div>
        <div class="form-actions">
          <button type="button" class="btn-secondary" id="promoCancelBtn">Cancel</button>
          <button type="submit" class="btn-primary">Save Promo Code</button>
        </div>
      </form>
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
    const promoModal = document.getElementById('promoModal');
    const promoModalCloseBtn = document.getElementById('promoModalCloseBtn');
    const promoCancelBtn = document.getElementById('promoCancelBtn');
    const promoForm = document.getElementById('promoForm');
    const formAction = document.getElementById('formAction');
    const promoIdInput = document.getElementById('promoId');
    const promoCodeInput = document.getElementById('promoCode');
    const promoDescriptionInput = document.getElementById('promoDescription');
    const discountTypeSelect = document.getElementById('discountType');
    const discountValueInput = document.getElementById('discountValue');
    const expiryDateInput = document.getElementById('expiryDate');
    const usageLimitInput = document.getElementById('usageLimit');
    const promoActiveCheckbox = document.getElementById('promoActive');

    // Promo codes data from PHP
    const promoCodes = <?= json_encode(array_values($promoCodes)) ?>;

    // Open Add Promo Modal
    document.getElementById('addPromoBtn').addEventListener('click', () => {
      openModal('add');
    });

    // Open modal for add or edit
    function openModal(mode, promo = null) {
      promoModal.classList.add('active');
      promoModal.setAttribute('aria-hidden', 'false');
      if (mode === 'add') {
        document.getElementById('promoModalTitle').textContent = 'Add New Promo Code';
        formAction.value = 'add';
        promoIdInput.value = '';
        promoForm.reset();
        promoActiveCheckbox.checked = true;
      } else if (mode === 'edit' && promo) {
        document.getElementById('promoModalTitle').textContent = 'Edit Promo Code';
        formAction.value = 'edit';
        promoIdInput.value = promo.id;
        promoCodeInput.value = promo.code;
        promoDescriptionInput.value = promo.description;
        discountTypeSelect.value = promo.discount_type;
        discountValueInput.value = promo.discount_value;
        expiryDateInput.value = promo.expiry_date;
        usageLimitInput.value = promo.usage_limit;
        promoActiveCheckbox.checked = promo.active;
      }
      promoCodeInput.focus();
    }

    // Load promo data for editing
    function openEditModal(id) {
      const promo = promoCodes.find(p => p.id === id);
      if (promo) {
        openModal('edit', promo);
      }
    }

    // Close modal
    function closeModal() {
      promoModal.classList.remove('active');
      promoModal.setAttribute('aria-hidden', 'true');
      promoForm.reset();
      promoIdInput.value = '';
    }
    promoModalCloseBtn.addEventListener('click', closeModal);
    promoCancelBtn.addEventListener('click', closeModal);

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && promoModal.classList.contains('active')) {
        closeModal();
      }
    });

    // Simple client-side validation (optional)
    promoForm.addEventListener('submit', (e) => {
      if (!promoCodeInput.value.trim()) {
        alert('Promo code is required.');
        promoCodeInput.focus();
        e.preventDefault();
        return false;
      }
      if (!discountValueInput.value || discountValueInput.value < 0) {
        alert('Discount value must be zero or positive.');
        discountValueInput.focus();
        e.preventDefault();
        return false;
      }
      if (!expiryDateInput.value) {
        alert('Expiry date is required.');
        expiryDateInput.focus();
        e.preventDefault();
        return false;
      }
    });
  </script>
</body>
</html>