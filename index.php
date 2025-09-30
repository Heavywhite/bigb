<?php
require_once 'connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BigB - Uganda's Premier Online Marketplace</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Navigation -->
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">BigB</a>
            
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search products, brands...">
                <button class="search-btn">🔍</button>
            </div>

            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <!--<li><a href="products.php">Products</a></li>-->
                <li><a href="#support">Support</a></li>
                <li><a href="log_index.php" >Login</a></li>
               <!-- <li><a href="cart.html">Cart</a></li>-->
            </ul>

            <!--<div class="cart-icon" id="cart-icon">
                🛒
                <span class="cart-count">0</span>
            </div>-->

            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Uganda's Premier Online Marketplace</h1>
            <p>Discover, purchase, and track everyday products with ease. Shop from trusted local and international brands.</p>
            <a href="#products" class="cta-button">Shop Now</a>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories" id="categories">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <div class="category-card" onclick="filterProducts('electronics')">
                <div class="category-icon">📱</div>
                <h3>Electronics</h3>
                <p>Phones, laptops, accessories</p>
            </div>
            <div class="category-card" onclick="filterProducts('fashion')">
                <div class="category-icon">👕</div>
                <h3>Fashion</h3>
                <p>Clothing, shoes, accessories</p>
            </div>
            <div class="category-card" onclick="filterProducts('home')">
                <div class="category-icon">🏠</div>
                <h3>Home & Living</h3>
                <p>Furniture, decor, appliances</p>
            </div>
            <div class="category-card" onclick="filterProducts('beauty')">
                <div class="category-icon">💄</div>
                <h3>Beauty</h3>
                <p>Cosmetics, skincare, health</p>
            </div>
            <div class="category-card" onclick="filterProducts('sports')">
                <div class="category-icon">⚽</div>
                <h3>Sports</h3>
                <p>Equipment, apparel, fitness</p>
            </div>
            <div class="category-card" onclick="filterProducts('books')">
                <div class="category-icon">📚</div>
                <h3>Books</h3>
                <p>Education, fiction, magazines</p>
            </div>
            <div class="category-card" onclick="filterProducts('electronics')">
                <div class="category-icon">📱</div>
                <h3>Electronics</h3>
                <p>Phones, laptops, accessories</p>
            </div>
            <div class="category-card" onclick="filterProducts('fashion')">
                <div class="category-icon">👕</div>
                <h3>Fashion</h3>
                <p>Clothing, shoes, accessories</p>
            </div>
            <div class="category-card" onclick="filterProducts('home')">
                <div class="category-icon">🏠</div>
                <h3>Home & Living</h3>
                <p>Furniture, decor, appliances</p>
            </div>
            <div class="category-card" onclick="filterProducts('beauty')">
                <div class="category-icon">💄</div>
                <h3>Beauty</h3>
                <p>Cosmetics, skincare, health</p>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-products" id="products">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" ><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn"><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" ><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn"><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn"><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn"><a href="log_index.html">Purchase item</a></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Methods -->
    <section class="payment-methods">
        <h2 class="section-title">Secure Payment Options</h2>
        <div class="payment-grid">
            <div class="payment-card">
                <div class="payment-icon">📱</div>
                <h3>MTN Mobile Money</h3>
                <p>Pay with your MTN MoMo account</p>
            </div>
            <div class="payment-card">
                <div class="payment-icon">💳</div>
                <h3>Airtel Money</h3>
                <p>Quick payments with Airtel Money</p>
            </div>
            <div class="payment-card">
                <div class="payment-icon">💰</div>
                <h3>Card Payment</h3>
                <p>Visa & Mastercard accepted</p>
            </div>
            <div class="payment-card">
                <div class="payment-icon">🚚</div>
                <h3>Cash on Delivery</h3>
                <p>Pay when you receive your order</p>
            </div>
        </div>
    </section>

    <!-- 7. Customer Support -->
    <section class="support" id="support">
        <h2>24/7 Customer Support</h2>
        <p>We're here to help you every step of the way</p>
        
        <div class="support-grid">
            <div class="support-card">
                <div class="support-icon">💬</div>
                <h3>Live Chat</h3>
                <p>Get instant help from our support team</p>
                <button class="cta-button" onclick="startChat()">Start Chat</button>
            </div>
            <div class="support-card">
                <div class="support-icon">📞</div>
                <h3>Call Support</h3>
                <p>Speak directly with our experts</p>
                <p><strong>0800-123-456</strong></p>
            </div>
            <div class="support-card">
                <div class="support-icon">✉️</div>
                <h3>Email Support</h3>
                <p>Send us your questions anytime</p>
                <p><strong>support@bigb.ug</strong></p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>BigB</h3>
                <p>Uganda's premier online marketplace connecting consumers with quality products from trusted sellers.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#support">Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Customer Care</h3>
                <ul>
                    <li><a href="faq-page.html">FAQ</a></li>
                    <li><a href="shipping-page.html">Shipping Info</a></li>
                    <li><a href="returns-page.html">Returns</a></li>
                    <li><a href="track-order-page.html">Track Order</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <ul>
                    <li><a href="services and privacy policy.html">Privacy Policy</a></li>
                    <li><a href="services and privacy policy.html">Terms of Service</a></li>
                    <li><a href="services and privacy policy.html">Seller Agreement</a></li><br>
                    <li><a href="admin_auth.php"><b><i>Login as Admin</i></b></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 BigB Uganda. All rights reserved. | Made with ❤️ for Uganda</p>
        </div>
    </footer>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <span id="toast-message">Item added to cart!</span>
        <button onclick="viewCart()">View Cart</button>
    </div>

    <script src="script.js"></script>
</body>
</html>