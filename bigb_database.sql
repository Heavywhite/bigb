-- BigB E-Commerce Platform Database Schema
-- MySQL Database Structure for Local XAMPP Development

-- Create Database
CREATE DATABASE IF NOT EXISTS bigb_ecommerce 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE bigb_ecommerce;

-- =====================================================
-- USER MANAGEMENT TABLES
-- =====================================================

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    verification_status ENUM('pending', 'email_verified', 'phone_verified', 'fully_verified') DEFAULT 'pending',
    profile_image VARCHAR(500) NULL,
    date_of_birth DATE NULL,
    gender ENUM('male', 'female', 'other') NULL,
    is_active BOOLEAN DEFAULT TRUE,
    login_attempts INT DEFAULT 0,
    last_login_attempt TIMESTAMP NULL,
    last_login_success TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_phone (phone),
    INDEX idx_verification_status (verification_status),
    INDEX idx_created_at (created_at)
);

-- User Addresses Table
CREATE TABLE user_addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('home', 'work', 'other') DEFAULT 'home',
    title VARCHAR(100) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) NULL,
    city VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    region VARCHAR(100) DEFAULT 'Central',
    postal_code VARCHAR(10) NULL,
    landmark VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_default (is_default)
);

-- User Sessions Table (for JWT token management)
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    refresh_token VARCHAR(255) NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_token (session_token),
    INDEX idx_expires_at (expires_at)
);

-- =====================================================
-- PRODUCT MANAGEMENT TABLES
-- =====================================================

-- Categories Table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    icon VARCHAR(50) NULL,
    image VARCHAR(500) NULL,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
);

-- Brands Table
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    logo VARCHAR(500) NULL,
    website_url VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
);

-- Products Table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    short_description VARCHAR(500) NULL,
    category_id INT NOT NULL,
    brand_id INT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    compare_price DECIMAL(12,2) NULL,
    cost_price DECIMAL(12,2) NULL,
    currency VARCHAR(3) DEFAULT 'UGX',
    weight DECIMAL(8,2) NULL,
    dimensions VARCHAR(100) NULL,
    stock_quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    stock_status ENUM('in_stock', 'low_stock', 'out_of_stock', 'discontinued') DEFAULT 'in_stock',
    manage_stock BOOLEAN DEFAULT TRUE,
    is_digital BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    views_count INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_category_id (category_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_slug (slug),
    INDEX idx_sku (sku),
    INDEX idx_is_active (is_active),
    INDEX idx_is_featured (is_featured),
    INDEX idx_stock_status (stock_status),
    INDEX idx_price (price),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (name, description, short_description)
);

-- Product Images Table
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_is_primary (is_primary),
    INDEX idx_sort_order (sort_order)
);

-- Product Attributes Table (for variants like size, color)
CREATE TABLE product_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    type ENUM('text', 'number', 'select', 'multiselect', 'color', 'boolean') DEFAULT 'text',
    is_required BOOLEAN DEFAULT FALSE,
    is_filterable BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_is_filterable (is_filterable)
);

-- Product Attribute Values Table
CREATE TABLE product_attribute_values (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_attribute_id (attribute_id),
    UNIQUE KEY unique_product_attribute (product_id, attribute_id)
);

-- Product Variants Table (for products with multiple options)
CREATE TABLE product_variants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    compare_price DECIMAL(12,2) NULL,
    stock_quantity INT DEFAULT 0,
    weight DECIMAL(8,2) NULL,
    image_url VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_sku (sku),
    INDEX idx_is_active (is_active)
);

-- Product Variant Attributes Table
CREATE TABLE product_variant_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    variant_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value VARCHAR(255) NOT NULL,
    
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES product_attributes(id) ON DELETE CASCADE,
    INDEX idx_variant_id (variant_id),
    INDEX idx_attribute_id (attribute_id)
);

-- =====================================================
-- ORDER MANAGEMENT TABLES
-- =====================================================

-- Orders Table
CREATE TABLE orders (
    id VARCHAR(20) PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded', 'partially_refunded') DEFAULT 'pending',
    payment_method ENUM('mtn_momo', 'airtel_money', 'card', 'bank_transfer', 'cash_on_delivery') NOT NULL,
    payment_reference VARCHAR(255) NULL,
    
    -- Order Totals
    subtotal DECIMAL(12,2) NOT NULL,
    tax_amount DECIMAL(12,2) DEFAULT 0.00,
    shipping_amount DECIMAL(12,2) DEFAULT 0.00,
    discount_amount DECIMAL(12,2) DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'UGX',
    
    -- Billing Information
    billing_name VARCHAR(255) NOT NULL,
    billing_email VARCHAR(255) NOT NULL,
    billing_phone VARCHAR(20) NOT NULL,
    billing_address TEXT NOT NULL,
    billing_city VARCHAR(100) NOT NULL,
    billing_district VARCHAR(100) NOT NULL,
    
    -- Shipping Information
    shipping_name VARCHAR(255) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_district VARCHAR(100) NOT NULL,
    shipping_method VARCHAR(100) DEFAULT 'standard',
    
    -- Additional Information
    notes TEXT NULL,
    tracking_number VARCHAR(255) NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    
    -- Promo Code Information
    promo_code VARCHAR(50) NULL,
    promo_discount_type ENUM('percentage', 'fixed', 'free_shipping') NULL,
    promo_discount_value DECIMAL(10,2) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_payment_method (payment_method),
    INDEX idx_created_at (created_at)
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) NOT NULL,
    product_id INT NOT NULL,
    variant_id INT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    product_data JSON NULL, -- Snapshot of product at time of order
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
);

-- Order Status History Table
CREATE TABLE order_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL,
    comment TEXT NULL,
    notified BOOLEAN DEFAULT FALSE,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- SHOPPING CART TABLES
-- =====================================================

-- Shopping Cart Table (for persistent cart storage)
CREATE TABLE shopping_cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    variant_id INT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id, variant_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
);

-- Wishlist Table
CREATE TABLE wishlists (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist_item (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
);

-- =====================================================
-- PAYMENT MANAGEMENT TABLES
-- =====================================================

-- Payment Transactions Table
CREATE TABLE payment_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) NOT NULL,
    transaction_id VARCHAR(255) UNIQUE NOT NULL,
    payment_method ENUM('mtn_momo', 'airtel_money', 'card', 'bank_transfer', 'cash_on_delivery') NOT NULL,
    payment_gateway VARCHAR(50) NULL, -- paystack, flutterwave, etc.
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'UGX',
    status ENUM('pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    gateway_response JSON NULL,
    reference_number VARCHAR(255) NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_payment_method (payment_method),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- PROMOTION AND DISCOUNT TABLES
-- =====================================================

-- Promo Codes Table
CREATE TABLE promo_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT NULL,
    type ENUM('percentage', 'fixed', 'free_shipping', 'buy_x_get_y') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(12,2) NULL,
    maximum_discount DECIMAL(12,2) NULL,
    usage_limit INT NULL,
    usage_limit_per_user INT NULL,
    used_count INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    starts_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    applicable_categories JSON NULL,
    applicable_products JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_code (code),
    INDEX idx_is_active (is_active),
    INDEX idx_starts_at (starts_at),
    INDEX idx_expires_at (expires_at)
);

-- Promo Code Usage Table
CREATE TABLE promo_code_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promo_code_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id VARCHAR(20) NOT NULL,
    discount_amount DECIMAL(12,2) NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_promo_code_id (promo_code_id),
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id)
);

-- =====================================================
-- REVIEWS AND RATINGS TABLES
-- =====================================================

-- Product Reviews Table
CREATE TABLE product_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    order_id VARCHAR(20) NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255) NULL,
    comment TEXT NULL,
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    helpful_votes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_product_id (product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_is_approved (is_approved),
    INDEX idx_created_at (created_at)
);

-- Review Images Table
CREATE TABLE review_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (review_id) REFERENCES product_reviews(id) ON DELETE CASCADE,
    INDEX idx_review_id (review_id)
);

-- Review Helpfulness Votes Table
CREATE TABLE review_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    is_helpful BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (review_id) REFERENCES product_reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review_vote (review_id, user_id),
    INDEX idx_review_id (review_id),
    INDEX idx_user_id (user_id)
);

-- =====================================================
-- INVENTORY MANAGEMENT TABLES
-- =====================================================

-- Stock Movements Table
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    variant_id INT NULL,
    type ENUM('purchase', 'sale', 'adjustment', 'return', 'damage', 'transfer') NOT NULL,
    quantity INT NOT NULL, -- Positive for incoming, negative for outgoing
    reference_type ENUM('order', 'purchase', 'adjustment', 'return') NULL,
    reference_id VARCHAR(50) NULL,
    notes TEXT NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_product_id (product_id),
    INDEX idx_variant_id (variant_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- SHIPPING AND DELIVERY TABLES
-- =====================================================

-- Shipping Methods Table
CREATE TABLE shipping_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    free_shipping_threshold DECIMAL(12,2) NULL,
    estimated_days_min INT DEFAULT 1,
    estimated_days_max INT DEFAULT 3,
    coverage_areas JSON NULL, -- Array of covered districts/cities
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
);

-- Delivery Tracking Table
CREATE TABLE delivery_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) NOT NULL,
    tracking_number VARCHAR(255) NOT NULL,
    carrier VARCHAR(100) NULL,
    status ENUM('picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned') NOT NULL,
    location VARCHAR(255) NULL,
    notes TEXT NULL,
    estimated_delivery TIMESTAMP NULL,
    actual_delivery TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_tracking_number (tracking_number),
    INDEX idx_status (status)
);

-- =====================================================
-- NOTIFICATION AND COMMUNICATION TABLES
-- =====================================================

-- Email Templates Table
CREATE TABLE email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    html_content TEXT NOT NULL,
    text_content TEXT NULL,
    variables JSON NULL, -- Available template variables
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
);

-- SMS Templates Table  
CREATE TABLE sms_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    variables JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
);

-- Notifications Table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('order_status', 'payment', 'shipping', 'promotion', 'system', 'review') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- =====================================================
-- ANALYTICS AND REPORTING TABLES
-- =====================================================

-- User Activity Log Table
CREATE TABLE user_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(255) NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    data JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id),
    INDEX idx_action (action),
    INDEX idx_entity_type (entity_type),
    INDEX idx_created_at (created_at)
);

-- Product Analytics Table
CREATE TABLE product_analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    date DATE NOT NULL,
    views_count INT DEFAULT 0,
    unique_views_count INT DEFAULT 0,
    add_to_cart_count INT DEFAULT 0,
    add_to_wishlist_count INT DEFAULT 0,
    purchase_count INT DEFAULT 0,
    revenue DECIMAL(12,2) DEFAULT 0.00,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_date (product_id, date),
    INDEX idx_product_id (product_id),
    INDEX idx_date (date)
);

-- =====================================================
-- CONFIGURATION TABLES
-- =====================================================

-- Settings Table
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category VARCHAR(100) NOT NULL,
    key_name VARCHAR(100) NOT NULL,
    value TEXT NULL,
    description TEXT NULL,
    type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_category_key (category, key_name),
    INDEX idx_category (category),
    INDEX idx_is_public (is_public)
);

-- =====================================================
-- ADMIN AND STAFF TABLES
-- =====================================================

-- Admin Users Table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'manager', 'staff') NOT NULL DEFAULT 'staff',
    permissions JSON NULL,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
);

-- =====================================================
-- SAMPLE DATA INSERTION
-- =====================================================

-- Insert Categories
INSERT INTO categories (name, slug, description, icon, is_active, sort_order) VALUES
('Electronics', 'electronics', 'Phones, laptops, and electronic devices', '📱', TRUE, 1),
('Fashion', 'fashion', 'Clothing, shoes, and accessories', '👗', TRUE, 2),
('Home & Living', 'home', 'Furniture, decor, and home appliances', '🏠', TRUE, 3),
('Beauty & Health', 'beauty', 'Cosmetics, skincare, and wellness products', '💄', TRUE, 4),
('Sports & Outdoor', 'sports', 'Sports equipment and outdoor gear', '⚽', TRUE, 5),
('Books & Education', 'books', 'Books, stationery, and educational materials', '📚', TRUE, 6);

-- Insert Brands
INSERT INTO brands (name, slug, description, is_active) VALUES
('Samsung', 'samsung', 'South Korean electronics company', TRUE),
('Apple', 'apple', 'American technology company', TRUE),
('Nike', 'nike', 'American athletic apparel company', TRUE),
('Adidas', 'adidas', 'German athletic apparel company', TRUE),
('Sony', 'sony', 'Japanese electronics company', TRUE),
('HP', 'hp', 'American computer company', TRUE);

-- Insert Sample Products
INSERT INTO products (name, slug, description, short_description, category_id, brand_id, sku, price, compare_price, stock_quantity, is_featured, is_active) VALUES
('Samsung Galaxy A54', 'samsung-galaxy-a54', 'Latest smartphone with amazing camera and performance', '128GB storage, 6GB RAM, Triple camera setup', 1, 1, 'SAM-A54-128', 850000, 950000, 15, TRUE, TRUE),
('iPhone 14', 'iphone-14', 'Apple latest iPhone with advanced features', '128GB storage, A15 Bionic chip, Dual camera', 1, 2, 'APL-IP14-128', 3200000, 3500000, 8, TRUE, TRUE),
('Nike Air Max Sneakers', 'nike-air-max-sneakers', 'Comfortable running shoes for everyday wear', 'Premium quality, multiple sizes available', 2, 3, 'NKE-AM-001', 320000, 380000, 25, TRUE, TRUE),
('MacBook Pro 13"', 'macbook-pro-13', 'Professional laptop for developers and creators', 'M2 chip, 8GB RAM, 256GB SSD', 1, 2, 'APL-MBP13-M2', 4500000, 5000000, 5, TRUE, TRUE),
('Leather Sofa Set', 'leather-sofa-set', 'Premium 3-seater leather sofa for living room', 'Genuine leather, comfortable seating', 3, NULL, 'HOME-SOFA-001', 1200000, 1400000, 3, FALSE, TRUE),
('Coffee Maker Pro', 'coffee-maker-pro', 'Professional espresso machine for home use', 'Multiple brewing options, easy to use', 3, NULL, 'HOME-COFFEE-001', 180000, 220000, 12, FALSE, TRUE),
('Fitness Tracker Watch', 'fitness-tracker-watch', 'Advanced health monitoring smartwatch', 'Heart rate, sleep tracking, GPS', 4, NULL, 'HEALTH-FIT-001', 280000, 320000, 20, TRUE, TRUE),
('Skincare Set Premium', 'skincare-set-premium', 'Complete skincare routine for all skin types', 'Cleanser, toner, moisturizer, serum', 4, NULL, 'BEAUTY-SKIN-001', 150000, 180000, 25, FALSE, TRUE);

-- Insert Product Images
INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order) VALUES
(1, '📱', 'Samsung Galaxy A54', TRUE, 1),
(2, '📱', 'iPhone 14', TRUE, 1),
(3, '👟', 'Nike Air Max Sneakers', TRUE, 1),
(4, '💻', 'MacBook Pro 13"', TRUE, 1),
(5, '🛋️', 'Leather Sofa Set', TRUE, 1),
(6, '☕', 'Coffee Maker Pro', TRUE, 1),
(7, '⌚', 'Fitness Tracker Watch', TRUE, 1),
(8, '🧴', 'Skincare Set Premium', TRUE, 1);

-- Insert Shipping Methods
INSERT INTO shipping_methods (name, description, price, estimated_days_min, estimated_days_max, coverage_areas, is_active) VALUES
('Standard Delivery', 'Regular delivery within Kampala and major cities', 15000, 1, 3, '["Kampala", "Entebbe", "Mukono", "Jinja", "Mbarara"]', TRUE),
('Express Delivery', 'Same day delivery within Kampala', 25000, 0, 1, '["Kampala"]', TRUE),
('Free Delivery', 'Free delivery for orders above UGX 500,000', 0, 2, 5, '["Kampala", "Entebbe", "Mukono"]', TRUE);

-- Insert Promo Codes
INSERT INTO promo_codes (code, description, type, value, minimum_amount, usage_limit, is_active, expires_at) VALUES
('WELCOME10', '10% off your first order', 'percentage', 10.00, 50000, 1000, TRUE, DATE_ADD(NOW(), INTERVAL 3 MONTH)),
('SAVE5000', 'UGX 5,000 off orders above UGX 100,000', 'fixed', 5000.00, 100000, 500, TRUE, DATE_ADD(NOW(), INTERVAL 1 MONTH)),
('FREESHIP', 'Free shipping on any order', 'free_shipping', 0.00, 30000, 200, TRUE, DATE_ADD(NOW(), INTERVAL 2 MONTH)),
('STUDENT15', '15% student discount', 'percentage', 15.00, 75000, NULL, TRUE, DATE_ADD(NOW(), INTERVAL 6 MONTH));

-- Insert Settings
INSERT INTO settings (category, key_name, value, description, type, is_public) VALUES
('general', 'site_name', 'BigB Uganda', 'Website name', 'string', TRUE),
('general', 'site_description', 'Uganda\'s premier e-commerce platform', 'Website description', 'string', TRUE),
('general', 'currency', 'UGX', 'Default currency', 'string', TRUE),
('general', 'tax_rate', '0.18', 'VAT tax rate (18%)', 'number', TRUE),
('general', 'free_shipping_threshold', '500000', 'Minimum amount for free shipping', 'number', TRUE),
('payment', 'mtn_momo_enabled', 'true', 'Enable MTN Mobile Money', 'boolean', TRUE),
('payment', 'airtel_money_enabled', 'true', 'Enable Airtel Money', 'boolean', TRUE),
('payment', 'card_payment_enabled', 'true', 'Enable card payments', 'boolean', TRUE),
('payment', 'cod_enabled', 'true', 'Enable cash on delivery', 'boolean', TRUE),
('email', 'smtp_host', 'smtp.gmail.com', 'SMTP server host', 'string', FALSE),
('email', 'smtp_port', '587', 'SMTP server port', 'number', FALSE),
('email', 'from_email', 'noreply@bigb.ug', 'From email address', 'string', FALSE),
('sms', 'provider', 'africas_talking', 'SMS service provider', 'string', FALSE);

-- Insert Email Templates
INSERT INTO email_templates (name, subject, html_content, text_content, is_active) VALUES
('order_confirmation', 'Order Confirmation - {{order_id}}', 
'<h1>Thank you for your order!</h1><p>Your order {{order_id}} has been confirmed and is being processed.</p><p>Total: {{total_amount}}</p>',
'Thank you for your order! Your order {{order_id}} has been confirmed and is being processed. Total: {{total_amount}}',
TRUE),
('order_shipped', 'Your Order Has Been Shipped - {{order_id}}', 
'<h1>Your order is on the way!</h1><p>Order {{order_id}} has been shipped. Tracking number: {{tracking_number}}</p>',
'Your order is on the way! Order {{order_id}} has been shipped. Tracking number: {{tracking_number}}',
TRUE);

-- Insert SMS Templates
INSERT INTO sms_templates (name, content, is_active) VALUES
('order_confirmation', 'Thank you! Your BigB order {{order_id}} for {{total_amount}} has been confirmed. Track at bigb.ug/orders', TRUE),
('order_shipped', 'Great news! Your BigB order {{order_id}} has shipped. Track: {{tracking_number}}. Delivery in 1-3 days.', TRUE),
('payment_received', 'Payment received for order {{order_id}}. Total: {{total_amount}}. Thank you for shopping with BigB!', TRUE);

-- Insert Admin User
INSERT INTO admin_users (name, email, password_hash, role, is_active) VALUES
('BigB Admin', 'admin@bigb.ug', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', TRUE);

-- =====================================================
-- INDEXES FOR PERFORMANCE OPTIMIZATION
-- =====================================================

-- Additional performance indexes
CREATE INDEX idx_products_featured_active ON products(is_featured, is_active);
CREATE INDEX idx_products_category_active ON products(category_id, is_active);
CREATE INDEX idx_products_price_range ON products(price, is_active);
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_payment_status ON orders(payment_status, created_at);
CREATE INDEX idx_user_activity_user_date ON user_activity_log(user_id, created_at);

-- =====================================================
-- VIEWS FOR COMMON QUERIES
-- =====================================================

-- Products with category and brand info
CREATE VIEW product_details_view AS
SELECT 
    p.*,
    c.name as category_name,
    c.slug as category_slug,
    b.name as brand_name,
    b.slug as brand_slug,
    pi.image_url as primary_image
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = TRUE
WHERE p.is_active = TRUE;

-- Order summary view
CREATE VIEW order_summary_view AS
SELECT 
    o.*,
    u.name as customer_name,
    u.email as customer_email,
    COUNT(oi.id) as item_count,
    GROUP_CONCAT(oi.product_name SEPARATOR ', ') as product_names
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

DELIMITER //

-- Update product rating when review is added
CREATE PROCEDURE UpdateProductRating(IN product_id_param INT)
BEGIN
    DECLARE avg_rating DECIMAL(3,2);
    DECLARE review_count INT;
    
    SELECT AVG(rating), COUNT(*) INTO avg_rating, review_count
    FROM product_reviews 
    WHERE product_id = product_id_param AND is_approved = TRUE;
    
    UPDATE products 
    SET rating_average = COALESCE(avg_rating, 0.00),
        rating_count = review_count
    WHERE id = product_id_param;
END //

-- Update stock after order
CREATE PROCEDURE UpdateStockAfterOrder(IN order_id_param VARCHAR(20))
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE product_id_var INT;
    DECLARE variant_id_var INT;
    DECLARE quantity_var INT;
    
    DECLARE cur CURSOR FOR 
        SELECT product_id, variant_id, quantity 
        FROM order_items 
        WHERE order_id = order_id_param;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO product_id_var, variant_id_var, quantity_var;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- Update product stock
        UPDATE products 
        SET stock_quantity = stock_quantity - quantity_var
        WHERE id = product_id_var;
        
        -- Update variant stock if applicable
        IF variant_id_var IS NOT NULL THEN
            UPDATE product_variants 
            SET stock_quantity = stock_quantity - quantity_var
            WHERE id = variant_id_var;
        END IF;
        
        -- Insert stock movement record
        INSERT INTO stock_movements (product_id, variant_id, type, quantity, reference_type, reference_id)
        VALUES (product_id_var, variant_id_var, 'sale', -quantity_var, 'order', order_id_param);
        
    END LOOP;
    
    CLOSE cur;
    
    -- Update stock status based on quantity
    UPDATE products 
    SET stock_status = CASE 
        WHEN stock_quantity = 0 THEN 'out_of_stock'
        WHEN stock_quantity <= low_stock_threshold THEN 'low_stock'
        ELSE 'in_stock'
    END
    WHERE id IN (SELECT DISTINCT product_id FROM order_items WHERE order_id = order_id_param);
    
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Update product updated_at when stock changes
DELIMITER //
CREATE TRIGGER update_product_timestamp 
BEFORE UPDATE ON products
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- Log user activity on login
DELIMITER //
CREATE TRIGGER log_user_login
AFTER INSERT ON user_sessions
FOR EACH ROW
BEGIN
    INSERT INTO user_activity_log (user_id, session_id, action, ip_address, data)
    VALUES (NEW.user_id, NEW.session_token, 'login', NEW.ip_address, JSON_OBJECT('user_agent', NEW.user_agent));
END //
DELIMITER ;

-- Update order status history
DELIMITER //
CREATE TRIGGER order_status_history
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO order_status_history (order_id, status, comment, created_at)
        VALUES (NEW.id, NEW.status, CONCAT('Status changed from ', OLD.status, ' to ', NEW.status), CURRENT_TIMESTAMP);
    END IF;
END //
DELIMITER ;

-- =====================================================
-- FINAL OPTIMIZATIONS
-- =====================================================

-- Analyze tables for better performance
ANALYZE TABLE users;
ANALYZE TABLE products;
ANALYZE TABLE orders;
ANALYZE TABLE order_items;
ANALYZE TABLE categories;

-- Set MySQL configurations for better performance
SET GLOBAL innodb_buffer_pool_size = 1073741824; -- 1GB
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_type = 1;

-- Create backup user
CREATE USER 'bigb_backup'@'localhost' IDENTIFIED BY 'backup_password_123';
GRANT SELECT, LOCK TABLES ON bigb_ecommerce.* TO 'bigb_backup'@'localhost';

-- Create application user
CREATE USER 'bigb_app'@'localhost' IDENTIFIED BY 'app_password_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON bigb_ecommerce.* TO 'bigb_app'@'localhost';

FLUSH PRIVILEGES;

-- =====================================================
-- MAINTENANCE QUERIES
-- =====================================================

-- Clean up expired sessions (run daily)
-- DELETE FROM user_sessions WHERE expires_at < NOW();

-- Clean up old activity logs (keep 90 days)
-- DELETE FROM user_activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Update product view counts (run hourly)
-- UPDATE products p SET views_count = (SELECT COUNT(*) FROM user_activity_log WHERE action = 'product_view' AND entity_id = p.id);

-- Backup command (run from command line)
-- mysqldump -u bigb_backup -p bigb_ecommerce > bigb_backup_$(date +%Y%m%d).sql

-- =====================================================
-- DATABASE SETUP COMPLETE
-- =====================================================