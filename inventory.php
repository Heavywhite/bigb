<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - BigB Admin</title>
    <link rel="stylesheet" href="bigb_styles.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        body {
            background-color: #f5f7fa;
        }

        /* Admin Sidebar */
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-black);
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 136, 0, 0.2);
        }

        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-orange);
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            background: rgba(255, 136, 0, 0.1);
            color: var(--accent-orange);
        }

        .nav-item.active {
            background: rgba(255, 136, 0, 0.2);
            color: var(--accent-orange);
            border-left: 3px solid var(--accent-orange);
        }

        .nav-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .badge {
            background: var(--error-red);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: auto;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Admin Header */
        .admin-header {
            background: var(--neutral-white);
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-left h1 {
            font-size: 1.5rem;
            color: var(--text-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--input-bg);
            border-radius: 25px;
            padding: 0.5rem 1rem;
        }

        .search-box input {
            border: none;
            background: none;
            outline: none;
            width: 250px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        /* Dashboard Content */
        .dashboard-content {
            padding: 2rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--neutral-white);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .stat-icon.low {
            background: linear-gradient(135deg, #FFC107, #FFA000);
        }

        .stat-icon.out {
            background: linear-gradient(135deg, #f44336, #D32F2F);
        }

        .stat-icon.value {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .stat-details h3 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Action Bar */
        .action-bar {
            background: var(--neutral-white);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--input-bg);
            cursor: pointer;
        }

        .btn-primary {
            background: var(--accent-orange);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--orange-hover);
        }

        .btn-secondary {
            background: transparent;
            color: var(--accent-orange);
            border: 2px solid var(--accent-orange);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        /* Content Card */
        .content-card {
            background: var(--neutral-white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--input-bg);
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--input-bg);
            color: var(--text-dark);
        }

        .data-table tbody tr:hover {
            background: var(--input-bg);
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-thumbnail {
            width: 40px;
            height: 40px;
            background: var(--input-bg);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stock-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .stock-badge.in-stock {
            background: #E8F5E9;
            color: #388E3C;
        }

        .stock-badge.low-stock {
            background: #FFF3E0;
            color: #F57C00;
        }

        .stock-badge.out-of-stock {
            background: #FFEBEE;
            color: #C62828;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .icon-btn {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            font-size: 1.2rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .icon-btn:hover {
            background: var(--input-bg);
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 1rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: var(--neutral-white);
            border-radius: 15px;
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--input-bg);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-orange);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input {
                width: 150px;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <h2 class="logo">BigB Admin</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php" class="nav-item">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="admin_products.php" class="nav-item">
                <span class="nav-icon">📦</span>
                <span>Products</span>
            </a>
            <a href="admin_orders.php" class="nav-item">
                <span class="nav-icon">🛒</span>
                <span>Orders</span>
                <span class="badge">12</span>
            </a>
            <a href="admin_customers.php" class="nav-item">
                <span class="nav-icon">👥</span>
                <span>Customers</span>
            </a>
            <a href="admin_categories.php" class="nav-item">
                <span class="nav-icon">📂</span>
                <span>Categories</span>
            </a>
            <a href="inventory.php" class="nav-item active">
                <span class="nav-icon">📋</span>
                <span>Inventory</span>
            </a>
            <a href="admin_analytics.php" class="nav-item">
                <span class="nav-icon">📈</span>
                <span>Analytics</span>
            </a>
            <a href="admin_settings.php" class="nav-item">
                <span class="nav-icon">⚙️</span>
                <span>Settings</span>
            </a>
        </nav>
        
        <div class="sidebar-footer" style="border-top: 1px solid rgba(255, 136, 0, 0.2); padding: 1rem 0;">
            <a href="index.php" class="nav-item">
                <span class="nav-icon">🏠</span>
                <span>View Store</span>
            </a>
            <a href="index.php" class="nav-item" onclick="adminLogout()">
                <span class="nav-icon">🚪</span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1>Inventory Management</h1>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <input type="text" placeholder="Search inventory..." id="inventorySearch">
                    <button style="background: none; border: none; cursor: pointer;">🔍</button>
                </div>
                <div class="admin-profile">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23FF8800'/%3E%3Ctext x='50' y='50' text-anchor='middle' dy='.3em' fill='white' font-size='40' font-family='Arial'%3EA%3C/text%3E%3C/svg%3E" alt="Admin" style="width: 40px; height: 40px; border-radius: 50%;">
                    <span>Admin User</span>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">📦</div>
                    <div class="stat-details">
                        <h3>Total Products</h3>
                        <div class="stat-value">892</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon low">⚠️</div>
                    <div class="stat-details">
                        <h3>Low Stock Items</h3>
                        <div class="stat-value" style="color: #F57C00;">15</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon out">❌</div>
                    <div class="stat-details">
                        <h3>Out of Stock</h3>
                        <div class="stat-value" style="color: #C62828;">7</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon value">💰</div>
                    <div class="stat-details">
                        <h3>Total Inventory Value</h3>
                        <div class="stat-value">UGX 285M</div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="filter-group">
                    <select class="filter-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="electronics">Electronics</option>
                        <option value="fashion">Fashion</option>
                        <option value="home">Home & Living</option>
                        <option value="beauty">Beauty & Health</option>
                        <option value="sports">Sports</option>
                        <option value="books">Books</option>
                    </select>
                    <select class="filter-select" id="stockFilter">
                        <option value="">All Stock Status</option>
                        <option value="in_stock">In Stock</option>
                        <option value="low_stock">Low Stock</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                    <button class="btn-secondary" onclick="exportInventory()">📥 Export</button>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn-primary" onclick="showStockAdjustmentModal()">📊 Adjust Stock</button>
                    <button class="btn-primary" onclick="showBulkUpdateModal()">⚡ Bulk Update</button>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="content-card">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Reserved</th>
                                <th>Available</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumbnail">📱</div>
                                        <div>
                                            <div style="font-weight: 600;">Samsung Galaxy A54</div>
                                            <div style="font-size: 0.85rem; color: #666;">128GB, 6GB RAM</div>
                                        </div>
                                    </div>
                                </td>
                                <td>SAM-A54-128</td>
                                <td>Electronics</td>
                                <td style="font-weight: 600;">15</td>
                                <td>2</td>
                                <td>13</td>
                                <td><span class="stock-badge in-stock">In Stock</span></td>
                                <td>2 hours ago</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="Adjust Stock" onclick="adjustStock('SAM-A54-128')">📊</button>
                                        <button class="icon-btn" title="View History" onclick="viewHistory('SAM-A54-128')">📋</button>
                                        <button class="icon-btn" title="Edit" onclick="editProduct('SAM-A54-128')">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumbnail">💻</div>
                                        <div>
                                            <div style="font-weight: 600;">MacBook Pro 13"</div>
                                            <div style="font-size: 0.85rem; color: #666;">M2, 8GB RAM, 256GB SSD</div>
                                        </div>
                                    </div>
                                </td>
                                <td>APL-MBP13-M2</td>
                                <td>Electronics</td>
                                <td style="font-weight: 600; color: #F57C00;">5</td>
                                <td>1</td>
                                <td>4</td>
                                <td><span class="stock-badge low-stock">Low Stock</span></td>
                                <td>5 hours ago</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="Adjust Stock" onclick="adjustStock('APL-MBP13-M2')">📊</button>
                                        <button class="icon-btn" title="View History" onclick="viewHistory('APL-MBP13-M2')">📋</button>
                                        <button class="icon-btn" title="Edit" onclick="editProduct('APL-MBP13-M2')">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumbnail">👟</div>
                                        <div>
                                            <div style="font-weight: 600;">Nike Air Max Sneakers</div>
                                            <div style="font-size: 0.85rem; color: #666;">Multiple sizes</div>
                                        </div>
                                    </div>
                                </td>
                                <td>NKE-AM-001</td>
                                <td>Fashion</td>
                                <td style="font-weight: 600;">25</td>
                                <td>3</td>
                                <td>22</td>
                                <td><span class="stock-badge in-stock">In Stock</span></td>
                                <td>1 day ago</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="Adjust Stock" onclick="adjustStock('NKE-AM-001')">📊</button>
                                        <button class="icon-btn" title="View History" onclick="viewHistory('NKE-AM-001')">📋</button>
                                        <button class="icon-btn" title="Edit" onclick="editProduct('NKE-AM-001')">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumbnail">🛋️</div>
                                        <div>
                                            <div style="font-weight: 600;">Leather Sofa Set</div>
                                            <div style="font-size: 0.85rem; color: #666;">3-seater</div>
                                        </div>
                                    </div>
                                </td>
                                <td>HOME-SOFA-001</td>
                                <td>Home & Living</td>
                                <td style="font-weight: 600; color: #F57C00;">3</td>
                                <td>0</td>
                                <td>3</td>
                                <td><span class="stock-badge low-stock">Low Stock</span></td>
                                <td>3 days ago</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="Adjust Stock" onclick="adjustStock('HOME-SOFA-001')">📊</button>
                                        <button class="icon-btn" title="View History" onclick="viewHistory('HOME-SOFA-001')">📋</button>
                                        <button class="icon-btn" title="Edit" onclick="editProduct('HOME-SOFA-001')">✏️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="item-checkbox"></td>
                                <td>
                                    <div class="product-cell">
                                        <div class="product-thumbnail">🧴</div>
                                        <div>
                                            <div style="font-weight: 600;">Skincare Set Premium</div>
                                            <div style="font-size: 0.85rem; color: #666;">Complete routine</div>
                                        </div>
                                    </div>
                                </td>
                                <td>BEAUTY-SKIN-001</td>
                                <td>Beauty & Health</td>
                                <td style="font-weight: 600; color: #C62828;">0</td>
                                <td>0</td>
                                <td>0</td>
                                <td><span class="stock-badge out-of-stock">Out of Stock</span></td>
                                <td>1 week ago</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="Adjust Stock" onclick="adjustStock('BEAUTY-SKIN-001')">📊</button>
                                        <button class="icon-btn" title="View History" onclick="viewHistory('BEAUTY-SKIN-001')">📋</button>
                                        <button class="icon-btn" title="Edit" onclick="editProduct('BEAUTY-SKIN-001')">✏️</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem;">
                    <button class="btn-secondary">Previous</button>
                    <button class="btn-primary" style="padding: 0.5rem 1rem;">1</button>
                    <button class="btn-secondary">2</button>
                    <button class="btn-secondary">3</button>
                    <button class="btn-secondary">Next</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Stock Adjustment Modal -->
    <div class="modal-overlay" id="stockAdjustmentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Adjust Stock</h2>
                <button class="modal-close" onclick="closeStockAdjustmentModal()">&times;</button>
            </div>
            <form id="stockAdjustmentForm">
                <div class="form-group">
                    <label>Product</label>
                    <select id="adjustProductSelect" required>
                        <option value="">Select Product</option>
                        <option value="SAM-A54-128">Samsung Galaxy A54 (SAM-A54-128)</option>
                        <option value="APL-MBP13-M2">MacBook Pro 13" (APL-MBP13-M2)</option>
                        <option value="NKE-AM-001">Nike Air Max Sneakers (NKE-AM-001)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Adjustment Type</label>
                    <select id="adjustmentType" required>
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        <option value="set">Set Stock Level</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="adjustQuantity" placeholder="Enter quantity" min="0" required>
                </div>
                <div class="form-group">
                    <label>Reason</label>
                    <select id="adjustReason" required>
                        <option value="">Select Reason</option>
                        <option value="purchase">New Purchase</option>
                        <option value="return">Customer Return</option>
                        <option value="damage">Damage/Loss</option>
                        <option value="correction">Stock Correction</option>
                        <option value="transfer">Transfer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <input type="text" id="adjustNotes" placeholder="Additional notes...">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeStockAdjustmentModal()">Cancel</button>
                    <button type="submit" class="btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock History Modal -->
    <div class="modal-overlay" id="stockHistoryModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>Stock Movement History</h2>
                <button class="modal-close" onclick="closeStockHistoryModal()">&times;</button>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                