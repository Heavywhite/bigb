      <?php
require_once 'connect.php';
session_start();

// Initialize products data in session for demo (replace with DB in real app)
if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        [
            'id' => 1,
            'thumbnail' => '📱',
            'name' => 'Samsung Galaxy A54',
            'meta' => '128GB, 6GB RAM',
            'sku' => 'SAM-A54-128',
            'category' => 'electronics',
            'price' => 850000,
            'stock' => 15,
            'lowStockThreshold' => 5,
            'status' => 'active',
            'featured' => false,
            'description' => '',
            'brand' => '',
            'images' => [],
        ],
        [
            'id' => 2,
            'thumbnail' => '💻',
            'name' => 'MacBook Pro 13"',
            'meta' => 'M2, 8GB RAM, 256GB SSD',
            'sku' => 'APL-MBP13-M2',
            'category' => 'electronics',
            'price' => 4500000,
            'stock' => 5,
            'lowStockThreshold' => 5,
            'status' => 'active',
            'featured' => false,
            'description' => '',
            'brand' => '',
            'images' => [],
        ],
        // Add more sample products as needed
    ];
}

$products = &$_SESSION['products'];

// Helper functions
function formatUGX($amount) {
    return 'UGX ' . number_format($amount);
}

function capitalizeFirstLetter($string) {
    return ucfirst($string);
}

// Handle POST requests for add/edit/delete
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        // Validate required fields
        $name = trim($_POST['name'] ?? '');
        $sku = trim($_POST['sku'] ?? '');
        $category = $_POST['category'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $lowStockThreshold = intval($_POST['low_stock'] ?? 5);
        $status = isset($_POST['active']) ? 'active' : 'inactive';
        $featured = isset($_POST['featured']);
        $description = trim($_POST['description'] ?? '');
        $brand = trim($_POST['brand'] ?? '');

        if ($name === '' || $sku === '' || $category === '' || $price < 0 || $stock < 0) {
            $error = 'Please fill in all required fields with valid values.';
        } else {
            if ($action === 'add') {
                // Add new product
                $newId = count($products) ? max(array_column($products, 'id')) + 1 : 1;
                $products[] = [
                    'id' => $newId,
                    'thumbnail' => '🆕',
                    'name' => $name,
                    'meta' => '',
                    'sku' => $sku,
                    'category' => $category,
                    'price' => $price,
                    'stock' => $stock,
                    'lowStockThreshold' => $lowStockThreshold,
                    'status' => $status,
                    'featured' => $featured,
                    'description' => $description,
                    'brand' => $brand,
                    'images' => [], // Image upload not implemented here
                ];
                $message = "Product \"$name\" added successfully.";
            } elseif ($action === 'edit') {
                // Edit existing product
                $editId = intval($_POST['id'] ?? 0);
                foreach ($products as &$p) {
                    if ($p['id'] === $editId) {
                        $p['name'] = $name;
                        $p['sku'] = $sku;
                        $p['category'] = $category;
                        $p['price'] = $price;
                        $p['stock'] = $stock;
                        $p['lowStockThreshold'] = $lowStockThreshold;
                        $p['status'] = $status;
                        $p['featured'] = $featured;
                        $p['description'] = $description;
                        $p['brand'] = $brand;
                        break;
                    }
                }
                unset($p);
                $message = "Product \"$name\" updated successfully.";
            }
        }
    } elseif ($action === 'delete') {
        $deleteId = intval($_POST['id'] ?? 0);
        foreach ($products as $key => $p) {
            if ($p['id'] === $deleteId) {
                $deletedName = $p['name'];
                unset($products[$key]);
                $products = array_values($products); // reindex
                $message = "Product \"$deletedName\" deleted successfully.";
                break;
            }
        }
    } elseif ($action === 'export') {
        // Export CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="products_export.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'SKU', 'Category', 'Price (UGX)', 'Stock', 'Status', 'Featured']);
        foreach ($products as $p) {
            fputcsv($output, [
                $p['id'],
                $p['name'],
                $p['sku'],
                $p['category'],
                $p['price'],
                $p['stock'],
                $p['status'],
                $p['featured'] ? 'Yes' : 'No',
            ]);
        }
        fclose($output);
        exit;
    }
}

// Pagination and filtering variables
$search = trim($_GET['search'] ?? '');
$filterCategory = $_GET['category'] ?? '';
$filterStock = $_GET['stock'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;

// Filter products
$filtered = array_filter($products, function ($p) use ($search, $filterCategory, $filterStock, $filterStatus) {
    $matchesSearch = $search === '' || stripos($p['name'], $search) !== false || stripos($p['sku'], $search) !== false;
    $matchesCategory = $filterCategory === '' || $p['category'] === $filterCategory;
    $matchesStatus = $filterStatus === '' || $p['status'] === $filterStatus;
    $matchesStock = true;
    if ($filterStock === 'in_stock') {
        $matchesStock = $p['stock'] > $p['lowStockThreshold'];
    } elseif ($filterStock === 'low_stock') {
        $matchesStock = $p['stock'] > 0 && $p['stock'] <= $p['lowStockThreshold'];
    } elseif ($filterStock === 'out_of_stock') {
        $matchesStock = $p['stock'] === 0;
    }
    return $matchesSearch && $matchesCategory && $matchesStatus && $matchesStock;
});

$totalFiltered = count($filtered);
$totalPages = ceil($totalFiltered / $perPage);
if ($page > $totalPages) $page = $totalPages > 0 ? $totalPages : 1;

$offset = ($page - 1) * $perPage;
$productsPage = array_slice($filtered, $offset, $perPage);

// Stats for header
$totalProducts = count($products);
$activeProducts = count(array_filter($products, fn($p) => $p['status'] === 'active'));
$lowStockCount = count(array_filter($products, fn($p) => $p['stock'] > 0 && $p['stock'] <= $p['lowStockThreshold']));
$outOfStockCount = count(array_filter($products, fn($p) => $p['stock'] === 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Products Management - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
  <style>
    /* Additional styles for form modal */
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
      <a href="admin_products.php" class="nav-item active" aria-current="page">
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
        <h1>Products Management</h1>
      </div>
      <div class="header-right">
        <form method="get" action="admin_products.php" style="display:flex; gap:0.5rem; align-items:center;">
          <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" aria-label="Search products" />
          <select name="category" aria-label="Filter by category">
            <option value="">All Categories</option>
            <option value="electronics" <?= $filterCategory === 'electronics' ? 'selected' : '' ?>>Electronics</option>
            <option value="fashion" <?= $filterCategory === 'fashion' ? 'selected' : '' ?>>Fashion</option>
            <option value="home" <?= $filterCategory === 'home' ? 'selected' : '' ?>>Home & Living</option>
            <option value="beauty" <?= $filterCategory === 'beauty' ? 'selected' : '' ?>>Beauty & Health</option>
            <option value="sports" <?= $filterCategory === 'sports' ? 'selected' : '' ?>>Sports</option>
            <option value="books" <?= $filterCategory === 'books' ? 'selected' : '' ?>>Books</option>
          </select>
          <select name="stock" aria-label="Filter by stock status">
            <option value="">All Stock Status</option>
            <option value="in_stock" <?= $filterStock === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
            <option value="low_stock" <?= $filterStock === 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
            <option value="out_of_stock" <?= $filterStock === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
          </select>
          <select name="status" aria-label="Filter by status">
            <option value="">All Status</option>
            <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          </select>
          <button type="submit" class="btn-secondary" aria-label="Apply filters">Filter</button>
        </form>
        <form method="post" action="admin_products.php" style="margin-left:1rem;">
          <input type="hidden" name="action" value="export" />
          <button type="submit" class="btn-secondary" aria-label="Export products CSV">📥 Export</button>
        </form>
        <button class="btn-primary" id="addProductBtn" aria-haspopup="dialog" aria-controls="productModal" style="margin-left:1rem;">➕ Add New Product</button>
      </div>
    </header>

    <div class="dashboard-content" style="margin-top:1rem;">
      <!-- Stats -->
      <div class="products-stats" style="margin-bottom:1rem;">
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
          <p class="stat-number warning"><?= $lowStockCount ?></p>
        </div>
        <div class="stat-box" tabindex="0">
          <h4>Out of Stock</h4>
          <p class="stat-number danger"><?= $outOfStockCount ?></p>
        </div>
      </div>

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

      <!-- Products Table -->
      <div class="table-container" role="region" aria-label="Products table">
        <table class="data-table" aria-describedby="tableDescription">
          <caption id="tableDescription" class="sr-only">List of products with details and actions</caption>
          <thead>
            <tr>
              <th>Product</th>
              <th>SKU</th>
              <th>Category</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($productsPage)): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:1rem;">No products found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($productsPage as $product): ?>
                <tr>
                  <td>
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                      <div style="font-size:1.5rem;"><?= htmlspecialchars($product['thumbnail']) ?></div>
                      <div>
                        <div style="font-weight:600;"><?= htmlspecialchars($product['name']) ?></div>
                        <div style="font-size:0.85rem; color:#666;"><?= htmlspecialchars($product['meta']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td><?= htmlspecialchars($product['sku']) ?></td>
                  <td><?= capitalizeFirstLetter(htmlspecialchars($product['category'])) ?></td>
                  <td><?= formatUGX($product['price']) ?></td>
                  <td>
                    <?php
                      $stockClass = 'in-stock';
                      if ($product['stock'] === 0) $stockClass = 'out-of-stock';
                      elseif ($product['stock'] <= $product['lowStockThreshold']) $stockClass = 'low-stock';
                    ?>
                    <span class="stock-badge <?= $stockClass ?>"><?= $product['stock'] ?></span>
                  </td>
                  <td>
                    <span class="status-badge <?= $product['status'] === 'active' ? 'active' : 'inactive' ?>">
                      <?= capitalizeFirstLetter(htmlspecialchars($product['status'])) ?>
                    </span>
                  </td>
                  <td>
                    <button class="icon-btn" aria-label="Edit <?= htmlspecialchars($product['name']) ?>" onclick="openEditModal(<?= $product['id'] ?>)">✏️</button>
                    <form method="post" action="admin_products.php" style="display:inline;" onsubmit="return confirm('Delete product <?= addslashes($product['name']) ?>?');">
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="id" value="<?= $product['id'] ?>" />
                      <button type="submit" class="icon-btn delete" aria-label="Delete <?= htmlspecialchars($product['name']) ?>">🗑️</button>
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

  <!-- Add/Edit Product Modal -->
  <div class="modal-overlay" id="productModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Add New Product</h2>
        <button class="modal-close" id="modalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <form id="productForm" method="post" action="admin_products.php" novalidate>
        <input type="hidden" name="action" id="formAction" value="add" />
        <input type="hidden" name="id" id="productId" value="" />
        <div class="form-group">
          <label for="productName">Product Name *</label>
          <input type="text" id="productName" name="name" required />
        </div>
        <div class="form-group">
          <label for="productSKU">SKU *</label>
          <input type="text" id="productSKU" name="sku" required />
        </div>
        <div class="form-group">
          <label for="productCategory">Category *</label>
          <select id="productCategory" name="category" required>
            <option value="">Select Category</option>
            <option value="electronics">Electronics</option>
            <option value="fashion">Fashion</option>
            <option value="home">Home & Living</option>
            <option value="beauty">Beauty & Health</option>
            <option value="sports">Sports</option>
            <option value="books">Books</option>
          </select>
        </div>
        <div class="form-group">
          <label for="productPrice">Price (UGX) *</label>
          <input type="number" id="productPrice" name="price" min="0" step="1" required />
        </div>
        <div class="form-group">
          <label for="productStock">Stock Quantity *</label>
          <input type="number" id="productStock" name="stock" min="0" step="1" required />
        </div>
        <div class="form-group">
          <label for="productLowStock">Low Stock Threshold</label>
          <input type="number" id="productLowStock" name="low_stock" min="0" step="1" value="5" />
        </div>
        <div class="form-group">
          <label for="productStatus">Status</label>
          <select id="productStatus" name="active">
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
        <div class="form-group">
          <label for="productFeatured">
            <input type="checkbox" id="productFeatured" name="featured" />
            Featured Product
          </label>
        </div>
        <div class="form-group">
          <label for="productDescription">Description</label>
          <textarea id="productDescription" name="description" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label for="productBrand">Brand</label>
          <input type="text" id="productBrand" name="brand" />
        </div>
        <div class="form-actions">
          <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn-primary">Save Product</button>
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

    // Modal elements
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('productForm');
    const formAction = document.getElementById('formAction');
    const productIdInput = document.getElementById('productId');
    const cancelBtn = document.getElementById('cancelBtn');
    const modalCloseBtn = document.getElementById('modalCloseBtn');

    // Form fields
    const fields = {
      name: document.getElementById('productName'),
      sku: document.getElementById('productSKU'),
      category: document.getElementById('productCategory'),
      price: document.getElementById('productPrice'),
      stock: document.getElementById('productStock'),
      low_stock: document.getElementById('productLowStock'),
      active: document.getElementById('productStatus'),
      featured: document.getElementById('productFeatured'),
      description: document.getElementById('productDescription'),
      brand: document.getElementById('productBrand'),
    };

    // Open Add Product Modal
    document.getElementById('addProductBtn').addEventListener('click', () => {
      openModal('add');
    });

    // Close modal
    function closeModal() {
      modal.classList.remove('active');
      modal.setAttribute('aria-hidden', 'true');
      form.reset();
      productIdInput.value = '';
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
    function openModal(mode, product = null) {
      modal.classList.add('active');
      modal.setAttribute('aria-hidden', 'false');
      if (mode === 'add') {
        modalTitle.textContent = 'Add New Product';
        formAction.value = 'add';
        productIdInput.value = '';
        form.reset();
        fields.active.value = '1';
        fields.featured.checked = false;
      } else if (mode === 'edit' && product) {
        modalTitle.textContent = 'Edit Product';
        formAction.value = 'edit';
        productIdInput.value = product.id;
        fields.name.value = product.name;
        fields.sku.value = product.sku;
        fields.category.value = product.category;
        fields.price.value = product.price;
        fields.stock.value = product.stock;
        fields.low_stock.value = product.lowStockThreshold;
        fields.active.value = product.status === 'active' ? '1' : '0';
        fields.featured.checked = product.featured;
        fields.description.value = product.description;
        fields.brand.value = product.brand;
      }
      fields.name.focus();
    }

    // Load product data for editing
    function openEditModal(id) {
      // Find product data from PHP embedded JSON
      const products = <?= json_encode(array_values($products)) ?>;
      const product = products.find(p => p.id === id);
      if (product) {
        openModal('edit', product);
      }
    }

    // Simple logout function
    function adminLogout() {
      if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'index.php'; // Adjust as needed
      }
    }
  </script>
</body>
</html>