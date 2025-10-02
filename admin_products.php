<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Products Management - BigB Admin</title>
  <link rel="stylesheet" href="admin_styles.css" />
</head>
<body>
  <!-- Admin Sidebar -->
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
        <span class="badge" aria-label="12 new orders">12</span>
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
      <a href="index.php" class="nav-item" onclick="adminLogout()">
        <span class="nav-icon">🚪</span>
        <span class="nav-text">Logout</span>
      </a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="admin-main" id="mainContent">
    <!-- Top Header -->
    <header class="admin-header">
      <div class="header-left">
        <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">☰</button>
        <h1>Products Management</h1>
      </div>
      <div class="header-right">
        <div class="search-box">
          <input
            type="text"
            placeholder="Search products..."
            id="productSearch"
            aria-label="Search products"
          />
          <button id="searchBtn" aria-label="Search products">🔍</button>
        </div>
        <button class="notification-btn" id="notificationBtn" aria-label="Notifications">
          🔔
          <span class="notification-badge" aria-live="polite" aria-atomic="true">5</span>
        </button>
        <div class="admin-profile" tabindex="0" aria-label="Admin profile">
          <img
            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23FF8800'/%3E%3Ctext x='50' y='50' text-anchor='middle' dy='.3em' fill='white' font-size='40' font-family='Arial'%3EA%3C/text%3E%3C/svg%3E"
            alt="Admin"
          />
          <span>Admin User</span>
        </div>
      </div>
    </header>

    <!-- Products Content -->
    <div class="dashboard-content">
      <!-- Action Bar -->
      <div class="action-bar">
        <button class="btn-primary" id="addProductBtn" aria-haspopup="dialog" aria-controls="productModal">
          ➕ Add New Product
        </button>
        <div class="filter-group">
          <select class="filter-select" id="categoryFilter" aria-label="Filter by category">
            <option value="">All Categories</option>
            <option value="electronics">Electronics</option>
            <option value="fashion">Fashion</option>
            <option value="home">Home & Living</option>
            <option value="beauty">Beauty & Health</option>
            <option value="sports">Sports</option>
            <option value="books">Books</option>
          </select>
          <select class="filter-select" id="stockFilter" aria-label="Filter by stock status">
            <option value="">All Stock Status</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
          <select class="filter-select" id="statusFilter" aria-label="Filter by status">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <button class="btn-secondary" id="exportBtn" aria-label="Export products">
            📥 Export
          </button>
        </div>
      </div>

      <!-- Products Stats -->
      <div class="products-stats">
        <div class="stat-box">
          <h4>Total Products</h4>
          <p class="stat-number" id="totalProducts">892</p>
        </div>
        <div class="stat-box">
          <h4>Active Products</h4>
          <p class="stat-number" id="activeProducts">847</p>
        </div>
        <div class="stat-box">
          <h4>Low Stock</h4>
          <p class="stat-number warning" id="lowStockProducts">15</p>
        </div>
        <div class="stat-box">
          <h4>Out of Stock</h4>
          <p class="stat-number danger" id="outOfStockProducts">7</p>
        </div>
      </div>

      <!-- Products Table -->
      <div class="content-card">
        <div class="table-container" role="region" aria-label="Products table">
          <table class="data-table" id="productsTable" aria-describedby="tableDescription">
            <caption id="tableDescription" class="sr-only">List of products with details and actions</caption>
            <thead>
              <tr>
                <th><input type="checkbox" id="selectAll" aria-label="Select all products" /></th>
                <th>Product</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="productsTableBody">
              <!-- Rows dynamically generated by JS -->
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav class="pagination" aria-label="Pagination">
          <button class="pagination-btn" id="prevPageBtn" aria-label="Previous page">Previous</button>
          <button class="pagination-btn active" aria-current="page">1</button>
          <button class="pagination-btn">2</button>
          <button class="pagination-btn">3</button>
          <button class="pagination-btn">4</button>
          <button class="pagination-btn">5</button>
          <button class="pagination-btn" id="nextPageBtn" aria-label="Next page">Next</button>
        </nav>
      </div>
    </div>
  </main>

  <!-- Add/Edit Product Modal -->
  <div class="modal-overlay" id="productModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" hidden>
    <div class="modal-content large">
      <div class="modal-header">
        <h2 id="modalTitle">Add New Product</h2>
        <button class="modal-close" id="modalCloseBtn" aria-label="Close modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="productForm" class="product-form" novalidate>
          <div class="form-grid">
            <div class="form-group">
              <label for="productName">Product Name *</label>
              <input type="text" id="productName" name="name" required placeholder="Enter product name" />
            </div>
            <div class="form-group">
              <label for="productSKU">SKU *</label>
              <input type="text" id="productSKU" name="sku" required placeholder="e.g., SAM-A54-128" />
            </div>
          </div>

          <div class="form-group">
            <label for="productDescription">Description</label>
            <textarea id="productDescription" name="description" rows="4" placeholder="Product description"></textarea>
          </div>

          <div class="form-grid">
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
              <label for="productBrand">Brand</label>
              <input type="text" id="productBrand" name="brand" placeholder="Brand name" />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="productPrice">Price (UGX) *</label>
              <input type="number" id="productPrice" name="price" required min="0" placeholder="0" />
            </div>
            <div class="form-group">
              <label for="productComparePrice">Compare Price</label>
              <input type="number" id="productComparePrice" name="compare_price" min="0" placeholder="0" />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label for="productStock">Stock Quantity *</label>
              <input type="number" id="productStock" name="stock" required min="0" placeholder="0" />
            </div>
            <div class="form-group">
              <label for="productLowStock">Low Stock Threshold</label>
              <input type="number" id="productLowStock" name="low_stock" min="0" value="5" placeholder="5" />
            </div>
          </div>

          <div class="form-group">
            <label>Product Images</label>
            <div class="file-upload">
              <input
                type="file"
                name="images"
                multiple
                accept="image/*"
                id="productImages"
                aria-describedby="imageHelp"
              />
              <label for="productImages" class="file-upload-label">📁 Choose Images</label>
            </div>
            <div id="imagePreview" class="image-preview" aria-live="polite"></div>
            <small id="imageHelp" class="sr-only">You can upload multiple images</small>
          </div>

          <div class="form-group checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" name="featured" id="productFeatured" />
              <span>Featured Product</span>
            </label>
            <label class="checkbox-label">
              <input type="checkbox" name="active" id="productActive" checked />
              <span>Active</span>
            </label>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
            <button type="submit" class="btn-primary">Save Product</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="admin_script.js"></script>
</body>
</html>