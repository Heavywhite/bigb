<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews Management - BigB Admin</title>
    <link rel="stylesheet" href="bigb_styles.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 70px;
        }

        body {
            background-color: #f5f7fa;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 136, 0, 0.2);
        }

        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-orange);
            text-decoration: none;
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

        .badge {
            background: var(--error-red);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            margin-left: auto;
        }

        .admin-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

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

        .dashboard-content {
            padding: 2rem;
        }

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

        .stat-icon.total { background: linear-gradient(135deg, #2196F3, #1976D2); }
        .stat-icon.pending { background: linear-gradient(135deg, #FFC107, #FFA000); }
        .stat-icon.approved { background: linear-gradient(135deg, #4CAF50, #45a049); }
        .stat-icon.rating { background: linear-gradient(135deg, #FF8800, #E67500); }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .action-bar {
            background: var(--neutral-white);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
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

        .content-card {
            background: var(--neutral-white);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
            font-size: 0.9rem;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--input-bg);
        }

        .review-preview {
            max-width: 400px;
        }

        .review-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .review-text {
            color: #666;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-badge.pending {
            background: #FFF3E0;
            color: #F57C00;
        }

        .status-badge.approved {
            background: #E8F5E9;
            color: #388E3C;
        }

        .status-badge.rejected {
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

        .btn-primary {
            background: var(--accent-orange);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
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
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .review-detail {
            margin-bottom: 2rem;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--input-bg);
        }

        .reviewer-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--accent-orange);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="admin_dashboard.html" class="logo">BigB Admin</a>
        </div>
        
        <nav class="sidebar-nav">
            <a href="admin_dashboard.php" class="nav-item">
                <span>📊</span> Dashboard
            </a>
            <a href="admin_products.php" class="nav-item">
                <span>📦</span> Products
            </a>
            <a href="admin_orders.php" class="nav-item">
                <span>🛒</span> Orders
                <span class="badge">12</span>
            </a>
            <a href="admin_customers.php" class="nav-item">
                <span>👥</span> Customers
            </a>
            <a href="admin_categories.php" class="nav-item">
                <span>📂</span> Categories
            </a>
            <a href="admin_inventory.php" class="nav-item">
                <span>📋</span> Inventory
            </a>
            <a href="admin_reviews.php" class="nav-item active">
                <span>⭐</span> Reviews
                <span class="badge">8</span>
            </a>
            <a href="admin_analytics.php" class="nav-item">
                <span>📈</span> Analytics
            </a>
            <a href="admin_settings.php" class="nav-item">
                <span>⚙️</span> Settings
            </a>
        </nav>
        
        <div style="border-top: 1px solid rgba(255, 136, 0, 0.2); padding: 1rem 0;">
            <a href="index.php" class="nav-item">
                <span>🏠</span> View Store
            </a>
            <a href="logout.php" class="nav-item" 
         onclick="return confirm('Are you sure you want to logout?');" 
         aria-label="Logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <h1>Reviews Management</h1>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="display: flex; align-items: center; background: var(--input-bg); border-radius: 25px; padding: 0.5rem 1rem;">
                    <input type="text" placeholder="Search reviews..." style="border: none; background: none; outline: none; width: 250px;">
                    <button style="background: none; border: none; cursor: pointer;">🔍</button>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='50' fill='%23FF8800'/%3E%3Ctext x='50' y='50' text-anchor='middle' dy='.3em' fill='white' font-size='40' font-family='Arial'%3EA%3C/text%3E%3C/svg%3E" alt="Admin" style="width: 40px; height: 40px; border-radius: 50%;">
                    <span>Admin User</span>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">💬</div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Total Reviews</h3>
                        <div class="stat-value">2,847</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon pending">⏳</div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Pending Approval</h3>
                        <div class="stat-value" style="color: #F57C00;">8</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">✓</div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Approved</h3>
                        <div class="stat-value">2,789</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating">⭐</div>
                    <div>
                        <h3 style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Average Rating</h3>
                        <div class="stat-value">4.6</div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="filter-group">
                    <select class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    <select class="filter-select">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                    <select class="filter-select">
                        <option value="">All Products</option>
                        <option value="electronics">Electronics</option>
                        <option value="fashion">Fashion</option>
                        <option value="home">Home & Living</option>
                    </select>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn-secondary">📥 Export</button>
                    <button class="btn-primary">⚙️ Settings</button>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="content-card">
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Reviewer</th>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--accent-orange); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">J</div>
                                        <div>
                                            <div style="font-weight: 600;">John Mukasa</div>
                                            <div style="font-size: 0.85rem; color: #666;">john@email.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">Samsung Galaxy A54</div>
                                    <div style="font-size: 0.85rem; color: #666;">Electronics</div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.2rem;">
                                        ⭐⭐⭐⭐⭐
                                    </div>
                                </td>
                                <td>
                                    <div class="review-preview">
                                        <div class="review-title">Excellent Product!</div>
                                        <div class="review-text">I purchased the Samsung Galaxy A54 and I'm extremely satisfied...</div>
                                    </div>
                                </td>
                                <td>2 days ago</td>
                                <td><span class="status-badge pending">Pending</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="View" onclick="viewReview(1)">👁️</button>
                                        <button class="icon-btn" title="Approve" onclick="approveReview(1)">✓</button>
                                        <button class="icon-btn" title="Reject" onclick="rejectReview(1)">✗</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--accent-orange); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">S</div>
                                        <div>
                                            <div style="font-weight: 600;">Sarah Namukasa</div>
                                            <div style="font-size: 0.85rem; color: #666;">sarah@email.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">Nike Air Max Sneakers</div>
                                    <div style="font-size: 0.85rem; color: #666;">Fashion</div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.2rem;">
                                        ⭐⭐⭐⭐☆
                                    </div>
                                </td>
                                <td>
                                    <div class="review-preview">
                                        <div class="review-title">Great Quality</div>
                                        <div class="review-text">The Nike Air Max sneakers are of excellent quality and exactly...</div>
                                    </div>
                                </td>
                                <td>5 days ago</td>
                                <td><span class="status-badge approved">Approved</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="View" onclick="viewReview(2)">👁️</button>
                                        <button class="icon-btn" title="Hide" onclick="hideReview(2)">🚫</button>
                                        <button class="icon-btn" title="Delete" onclick="deleteReview(2)">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: var(--accent--orange); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">M</div>
                                        <div>
                                            <div style="font-weight: 600;">Michael Okello</div>
                                            <div style="font-size: 0.85rem; color: #666;">michael@email.com</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">Wooden Dining Table</div>
                                    <div style="font-size: 0.85rem; color: #666;">Home & Living</div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.2rem;">
                                        ⭐⭐⭐☆☆
                                    </div>
                                </td>
                                <td>
                                    <div class="review-preview">
                                        <div class="review-title">Good but could be better</div>
                                        <div class="review-text">The wooden dining table looks great but the finish could be improved...</div>
                                    </div>
                                </td>
                                <td>1 week ago</td>
                                <td><span class="status-badge rejected">Rejected</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="icon-btn" title="View" onclick="viewReview(3)">👁️</button>
                                        <button class="icon-btn" title="Delete" onclick="deleteReview(3)">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Add more review rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Review Detail Modal -->
    <div class="modal-overlay" id="reviewModal">
        <div class="modal-content">
            <button onclick="closeModal()" style="float: right; font-size: 1.5rem; background: none; border: none; cursor: pointer;">✖️</button>
            <div class="review-detail" id="modalReviewDetail">
                <!-- Review details will be dynamically inserted here -->
            </div>
            <div style="text-align: right;">
                <button class="btn-primary" id="approveBtn" onclick="approveCurrentReview()">Approve</button>
                <button class="btn-secondary" id="rejectBtn" onclick="rejectCurrentReview()">Reject</button>
            </div>
        </div>
    </div>

    <script>
        const reviews = {
            1: {
                reviewer: { name: "John Mukasa", email: "john@email.com", initial: "J" },
                product: { name: "Samsung Galaxy A54", category: "Electronics" },
                rating: 5,
                title: "Excellent Product!",
                text: "I purchased the Samsung Galaxy A54 and I'm extremely satisfied with its performance and battery life. Highly recommend!",
                date: "2 days ago",
                status: "pending"
            },
            2: {
                reviewer: { name: "Sarah Namukasa", email: "sarah@email.com", initial: "S" },
                product: { name: "Nike Air Max Sneakers", category: "Fashion" },
                rating: 4,
                title: "Great Quality",
                text: "The Nike Air Max sneakers are of excellent quality and exactly as described. Comfortable and stylish.",
                date: "5 days ago",
                status: "approved"
            },
            3: {
                reviewer: { name: "Michael Okello", email: "michael@email.com", initial: "M" },
                product: { name: "Wooden Dining Table", category: "Home & Living" },
                rating: 3,
                title: "Good but could be better",
                text: "The wooden dining table looks great but the finish could be improved. Overall, a decent purchase.",
                date: "1 week ago",
                status: "rejected"
            }
        };

        let currentReviewId = null;

        function viewReview(id) {
            currentReviewId = id;
            const review = reviews[id];
            if (!review) return;

            const modalDetail = document.getElementById('modalReviewDetail');
            modalDetail.innerHTML = `
                <div class="reviewer-info">
                    <div class="reviewer-avatar">${review.reviewer.initial}</div>
                    <div>
                        <h2>${review.reviewer.name}</h2>
                        <p style="color: #666;">${review.reviewer.email}</p>
                    </div>
                </div>
                <div>
                    <h3>Product: ${review.product.name}</h3>
                    <p style="color: #666; margin-top: 0.25rem;">Category: ${review.product.category}</p>
                </div>
                <div style="margin: 1rem 0;">
                    <strong>Rating:</strong> ${'⭐'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                </div>
                <div>
                    <h3>${review.title}</h3>
                    <p>${review.text}</p>
                </div>
                <div style="margin-top: 1rem; color: #999;">
                    <small>Reviewed: ${review.date}</small>
                </div>
                <div style="margin-top: 1rem;">
                    <strong>Status:</strong> <span class="status-badge ${review.status}">${capitalize(review.status)}</span>
                </div>
            `;

            // Show or hide approve/reject buttons based on status
            document.getElementById('approveBtn').style.display = review.status === 'pending' ? 'inline-block' : 'none';
            document.getElementById('rejectBtn').style.display = review.status === 'pending' ? 'inline-block' : 'none';

            document.getElementById('reviewModal').classList.add('active');
        }
        function adminLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'index.php';
            }
    }
        function closeModal() {
            document.getElementById('reviewModal').classList.remove('active');
            currentReviewId = null;
        }

        function approveReview(id) {
            if (confirm('Are you sure you want to approve this review?')) {
                updateReviewStatus(id, 'approved');
            }
        }

        function rejectReview(id) {
            if (confirm('Are you sure you want to reject this review?')) {
                updateReviewStatus(id, 'rejected');
            }
        }

        function hideReview(id) {
            alert('Hide review functionality is not implemented yet.');
        }

        function deleteReview(id) {
            if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                // For demo, just remove from reviews object and reload page or update UI accordingly
                delete reviews[id];
                alert('Review deleted. Please refresh the page to see changes.');
            }
        }

        function approveCurrentReview() {
            if (currentReviewId !== null) {
                approveReview(currentReviewId);
                closeModal();
            }
        }

        function rejectCurrentReview() {
            if (currentReviewId !== null) {
                rejectReview(currentReviewId);
                closeModal();
            }
        }

        function updateReviewStatus(id, status) {
            if (reviews[id]) {
                reviews[id].status = status;
                alert(`Review status updated to ${capitalize(status)}.`);
                // In real app, update UI or reload data here
            }
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>
</body>
</html>