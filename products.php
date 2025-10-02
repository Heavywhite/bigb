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
    <!-- 1. Header Navigation -->
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">BigB</a>
            
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search products, brands...">
                <button class="search-btn">🔍</button>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <!-- <li><a href="#products">Products</a></li> -->
                <li><a href="#support">Support</a></li>
                <li><a href="index.php" class="login-trigger">Logout</a></li>
            <li><a href="cart.php">Cart</a></li>
            </ul>

            <div class="cart-icon" id="cart-icon">
                🛒
                <span class="cart-count">0</span>
            </div>

            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- 2. Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Uganda's Premier Online Marketplace</h1>
            <p>Discover, purchase, and track everyday products with ease. Shop from trusted local and international brands.</p>
            <a href="#products" class="cta-button">Shop Now</a>
        </div>
    </section>
    <!-- 4. Featured Products -->
    <section class="featured-products" id="products">
        <h2 class="section-title">Products</h2>
        <div class="products-grid">
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">📱</div>
                <div class="product-info">
                    <h3 class="product-title">Samsung Galaxy A54</h3>
                    <div class="product-price">UGX 1,200,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Samsung Galaxy A54', 1200000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="fashion">
                <div class="product-image">👕</div>
                <div class="product-info">
                    <h3 class="product-title">Cotton T-Shirt</h3>
                    <div class="product-price">UGX 25,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Cotton T-Shirt', 25000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="home">
                <div class="product-image">🏠</div>
                <div class="product-info">
                    <h3 class="product-title">Kitchen Blender</h3>
                    <div class="product-price">UGX 180,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Kitchen Blender', 180000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="beauty">
                <div class="product-image">💄</div>
                <div class="product-info">
                    <h3 class="product-title">Skincare Set</h3>
                    <div class="product-price">UGX 75,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Skincare Set', 75000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="sports">
                <div class="product-image">⚽</div>
                <div class="product-info">
                    <h3 class="product-title">Football Jersey</h3>
                    <div class="product-price">UGX 45,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Football Jersey', 45000)">Add to Cart</button>
                </div>
            </div>
            <div class="product-card" data-category="electronics">
                <div class="product-image">🎧</div>
                <div class="product-info">
                    <h3 class="product-title">Wireless Earbuds</h3>
                    <div class="product-price">UGX 150,000</div>
                    <button class="add-to-cart-btn" onclick="addToCart('Wireless Earbuds', 150000)">Add to Cart</button>
                </div>
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

    <!-- 8. Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>BigB</h3>
                <p>Uganda's premier online marketplace connecting consumers with quality products from trusted sellers.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#categories">Categories</a></li>
                    <!-- <li><a href="#products">Products</a></li>-->
                    <li><a href="#support">Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Customer Care</h3>
                <ul>
                    <li><a href="faq-page.html">FAQ</a></li>
                    <li><a href="shipping-page.html">Shipping Info</a></li>
                    <li><a href="returns.html">Returns</a></li>
                    <li><a href="track-order-page.html">Track Order</a></li>
                    <li><a href="reviews.html">Reviews</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <ul>
                    <li><a href="services and privacy policy.html">Privacy Policy</a></li>
                    <li><a href="services and privacy policy.html">Terms of Service</a></li>
                    <li><a href="services and privacy policy.html">Seller Agreement</a></li>
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

    <script>
        // Utility: Show toast notification
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message') || document.getElementById('toastMessage');
    if (toastMessage) toastMessage.textContent = message;
    if (toast) {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    } else {
        alert(message); // fallback
    }
}

// Update cart count in header
function updateCartCount() {
    const cartCountElem = document.querySelector('.cart-count');
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartCountElem) {
        cartCountElem.textContent = totalCount;
    }
}

// Add product to cart
function addToCart(productName, productPrice) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Check if product already in cart
    const existingProductIndex = cart.findIndex(item => item.name === productName);
    if (existingProductIndex !== -1) {
        // Increase quantity
        cart[existingProductIndex].quantity += 1;
    } else {
        // Add new product
        cart.push({
            name: productName,
            price: productPrice,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showToast(`Added "${productName}" to cart!`);
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
});
    </script>
</body>
</html>