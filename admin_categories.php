<?php
// admin_categories.php
session_start();

// Initialize categories data in session for demo (replace with DB in real app)
if (!isset($_SESSION['categories'])) {
    $_SESSION['categories'] = [
        ['id' => 1, 'name' => 'Electronics', 'description' => 'Phones, laptops, and gadgets'],
        ['id' => 2, 'name' => 'Fashion', 'description' => 'Clothing and accessories'],
        ['id' => 3, 'name' => 'Home & Living', 'description' => 'Furniture and home decor'],
        ['id' => 4, 'name' => 'Beauty & Health', 'description' => 'Cosmetics and health products'],
        ['id' => 5, 'name' => 'Sports', 'description' => 'Sports equipment and apparel'],
        ['id' => 6, 'name' => 'Books', 'description' => 'Books and stationery'],
    ];
}

$categories = &$_SESSION['categories'];

// Handle POST requests for add/edit/delete
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $error = 'Category name is required.';
        } else {
            if ($action === 'add') {
                $newId = count($categories) ? max(array_column($categories, 'id')) + 1 : 1;
                $categories[] = ['id' => $newId, 'name' => $name, 'description' => $description];
                $message = "Category \"$name\" added successfully.";
            } elseif ($action === 'edit') {
                $editId = intval($_POST['id'] ?? 0);
                foreach ($categories as &$cat) {
                    if ($cat['id'] === $editId) {
                        $cat['name'] = $name;
                        $cat['description'] = $description;
                        $message = "Category \"$name\" updated successfully.";
                        break;
                    }
                }
                unset($cat);
            }
        }
    } elseif ($action === 'delete') {
        $deleteId = intval($_POST['id'] ?? 0);
        foreach ($categories as $key => $cat) {
            if ($cat['id'] === $deleteId) {
                $deletedName = $cat['name'];
                unset($categories[$key]);
                $categories = array_values($categories);
                $message = "Category \"$deletedName\" deleted successfully.";
                break;
            }
        }
    }
}

// Pagination and filtering variables
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// Filter categories
$filtered = array_filter($categories, function ($c) use ($search) {
    return $search === '' || stripos($c['name'], $search) !== false;
});

$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages) $page = $totalPages > 0 ? $totalPages : 1;

$offset = ($page - 1) * $perPage;
$categoriesPage = array_slice($filtered, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categories Management - BigB Admin</title>
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
        <h1>Categories Management</h1>
      </div>
      <div class="header-right">
        <form method="get" action="admin_categories.php" style="display:flex; gap:0.5rem; align-items:center;">
          <input type="text" name="search" placeholder="Search categories..." value="<?= htmlspecialchars($search) ?>" aria-label="Search categories" />
          <button type="submit" class="btn-secondary" aria-label="Search">Search</button>
        </form>
        <button class="btn-primary" id="addCategoryBtn" aria-haspopup="dialog" aria-controls="categoryModal" style="margin-left:1rem;">➕ Add New Category</button>
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

      <!-- Categories Table -->
      <div class="table-container" role="region" aria-label="Categories table">
        <table class="data-table" aria-describedby="categoriesTableDesc">
          <caption id="categoriesTableDesc" class="sr-only">List of categories with details and actions</caption>
          <thead>
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($categoriesPage)): ?>
              <tr>
                <td colspan="3" style="text-align:center; padding:1rem;">No categories found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($categoriesPage as $cat): ?>
                <tr>
                  <td><?= htmlspecialchars($cat['name']) ?></td>
                  <td><?= htmlspecialchars($cat['description']) ?></td>
                  <td>
                    <button class="icon-btn" aria-label="Edit category <?= htmlspecialchars($cat['name']) ?>" onclick="openEditModal(<?= $cat['id'] ?>)">✏️</button>
                    <form method="post" action="admin_categories.php" style="display:inline;" onsubmit="return confirm('Delete category <?= addslashes($cat['name']) ?>?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= $cat['id'] ?>" />
                      <button type="submit" class="icon-btn delete" aria-label="Delete category <?= htmlspecialchars($cat['name']) ?>">🗑️</button>
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

  <!-- Add/Edit Category Modal -->
  <div class="modal-overlay" id="categoryModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Add New Category</h2>
        <button class="modal-close" id="modalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <form id="categoryForm" method="post" action="admin_categories.php" novalidate>
        <input type="hidden" name="action" id="formAction" value="add" />
        <input type="hidden" name="id" id="categoryId" value="" />
        <div class="form-group">
          <label for="categoryName">Category Name *</label>
          <input type="text" id="categoryName" name="name" required />
        </div>
        <div class="form-group">
          <label for="categoryDescription">Description</label>
          <textarea id="categoryDescription" name="description" rows="3"></textarea>
        </div>
        <div class="form-actions">
          <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn-primary">Save Category</button>
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
    const modal = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('categoryForm');
    const formAction = document.getElementById('formAction');
    const categoryIdInput = document.getElementById('categoryId');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    // Form fields
    const fields = {
      name: document.getElementById('categoryName'),
      description: document.getElementById('categoryDescription'),
    };

    // Open Add Category Modal
    document.getElementById('addCategoryBtn').addEventListener('click', () => {
      openModal('add');
    });

    // Close modal
    function closeModal() {
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
      form.reset();
      categoryIdInput.value = '';
    }
    cancelBtn.addEventListener('click', closeModal);
    modalCloseBtn.addEventListener('click', closeModal);

    // Close modal on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        closeModal();
      }
    });

    // Open modal for add or edit
    function openModal(mode, category = null) {
      modal.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      if (mode === 'add') {
        modalTitle.textContent = 'Add New Category';
        formAction.value = 'add';
        categoryIdInput.value = '';
        form.reset();
      } else if (mode === 'edit' && category) {
        modalTitle.textContent = 'Edit Category';
        formAction.value = 'edit';
        categoryIdInput.value = category.id;
        fields.name.value = category.name;
        fields.description.value = category.description;
      }
      fields.name.focus();
    }

    // Load category data for editing
    function openEditModal(id) {
      const categories = <?= json_encode(array_values($categories)) ?>;
      const category = categories.find(c => c.id === id);
      if (category) {
        openModal('edit', category);
      }
    }
  </script>
</body>
</html>