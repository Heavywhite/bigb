<?php
session_start(); 
require_once 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BigB Uganda</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <a href="index.php" class="logo">BigB</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#categories">Categories</a></li>
                <li><a href="products.php">Products</a></li>
                <li><a href="index.php#support">Support</a></li>
                <li><a href="account.html" class="account-link">My Account</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="page-title">Shopping Cart</h1>

        <!-- Cart Content -->
        <div class="cart-container" id="cartContainer">
            <!-- Cart Items -->
            <div class="cart-items">
                <h2>Your Items (<span id="itemCount">0</span>)</h2>
                <div id="cartItemsList">
                    <!-- Cart items will be dynamically loaded here -->
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-line">
                    <span>Subtotal:</span>
                    <span id="subtotal">UGX 0</span>
                </div>
                
                <div class="summary-line">
                    <span>Shipping:</span>
                    <span id="shipping">UGX 15,000</span>
                </div>
                
                <div class="summary-line">
                    <span>Tax:</span>
                    <span id="tax">UGX 0</span>
                </div>
                
                <div class="summary-line">
                    <span>Discount:</span>
                    <span id="discount">UGX 0</span>
                </div>
                
                <div class="summary-line total">
                    <span>Total:</span>
                    <span id="total">UGX 0</span>
                </div>

                <div class="promo-code">
                    <input type="text" class="promo-input" placeholder="Enter promo code" id="promoInput">
                    <button class="apply-promo" onclick="applyPromoCode()">Apply Code</button>
                </div>

                <button class="checkout-btn" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>

        <!-- Empty Cart State -->
        <div class="empty-cart" id="emptyCart" style="display: none;">
            <div class="empty-cart-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Add some amazing products to get started!</p>
            <a href="products.php" class="continue-shopping">Continue Shopping</a>
        </div>
    </main>

    <!-- Payment Modal -->
    <div class="payment-modal" id="paymentModal">
        <div class="payment-content">
            <button class="payment-close" onclick="closePaymentModal()">&times;</button>
            
            <div class="payment-header">
                <h2>Complete Your Order</h2>
                <div class="order-total" id="paymentTotal">UGX 0</div>
            </div>

            <div class="payment-methods">
                <!-- MTN Mobile Money -->
                <div class="payment-method" onclick="selectPaymentMethod('mtn')" data-method="mtn">
                    <div class="payment-icon">📱</div>
                    <div class="payment-info">
                        <h3>MTN Mobile Money</h3>
                        <p>Pay securely with your MTN MoMo account</p>
                    </div>
                </div>

                <!-- Airtel Money -->
                <div class="payment-method" onclick="selectPaymentMethod('airtel')" data-method="airtel">
                    <div class="payment-icon">💳</div>
                    <div class="payment-info">
                        <h3>Airtel Money</h3>
                        <p>Quick and secure Airtel Money payments</p>
                    </div>
                </div>

                <!-- Card Payment -->
                <div class="payment-method" onclick="selectPaymentMethod('card')" data-method="card">
                    <div class="payment-icon">💰</div>
                    <div class="payment-info">
                        <h3>Debit/Credit Card</h3>
                        <p>Visa, Mastercard - Secured by Paystack</p>
                    </div>
                </div>

                <!-- Cash on Delivery -->
                <div class="payment-method" onclick="selectPaymentMethod('cod')" data-method="cod">
                    <div class="payment-icon">🚚</div>
                    <div class="payment-info">
                        <h3>Cash on Delivery</h3>
                        <p>Pay when your order arrives</p>
                    </div>
                </div>
            </div>

            <!-- MTN Mobile Money Form -->
            <div class="payment-form" id="mtnForm">
                <div class="form-group">
                    <label for="mtnPhone">MTN Phone Number</label>
                    <input type="tel" id="mtnPhone" class="form-input" placeholder="+256 77X XXX XXX" required>
                </div>
                <button class="process-payment" onclick="processMTNPayment()">
                    Pay with MTN MoMo
                </button>
            </div>

            <!-- Airtel Money Form -->
            <div class="payment-form" id="airtelForm">
                <div class="form-group">
                    <label for="airtelPhone">Airtel Phone Number</label>
                    <input type="tel" id="airtelPhone" class="form-input" placeholder="+256 70X XXX XXX" required>
                </div>
                <button class="process-payment" onclick="processAirtelPayment()">
                    Pay with Airtel Money
                </button>
            </div>

            <!-- Card Payment Form -->
            <div class="payment-form" id="cardForm">
                <div class="form-group">
                    <label for="cardNumber">Card Number</label>
                    <input type="text" id="cardNumber" class="form-input" placeholder="1234 5678 9012 3456" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryDate">Expiry Date</label>
                        <input type="text" id="expiryDate" class="form-input" placeholder="MM/YY" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" class="form-input" placeholder="123" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cardName">Cardholder Name</label>
                    <input type="text" id="cardName" class="form-input" placeholder="Niwe Isaac" required>
                </div>
                <button class="process-payment" onclick="processCardPayment()">
                    Pay with Card
                </button>
            </div>

            <!-- Cash on Delivery Form -->
            <div class="payment-form" id="codForm">
                <div class="form-group">
                    <label for="deliveryAddress">Delivery Address</label>
                    <textarea id="deliveryAddress" class="form-input" rows="3" placeholder="Enter your full delivery address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="deliveryPhone">Contact Phone</label>
                    <input type="tel" id="deliveryPhone" class="form-input" placeholder="+256 XXX XXX XXX" required>
                </div>
                <button class="process-payment" onclick="processCODPayment()">
                    Confirm Cash on Delivery
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <span id="toastMessage">Message</span>
    </div>
<script>
    // Format number as UGX currency string
function formatUGX(amount) {
    return 'UGX ' + amount.toLocaleString('en-UG');
}

// Render cart items in the cart page
function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsList = document.getElementById('cartItemsList');
    const itemCountElem = document.getElementById('itemCount');
    const subtotalElem = document.getElementById('subtotal');
    const shippingElem = document.getElementById('shipping');
    const taxElem = document.getElementById('tax');
    const discountElem = document.getElementById('discount');
    const totalElem = document.getElementById('total');
    const emptyCartElem = document.getElementById('emptyCart');
    const cartContainer = document.getElementById('cartContainer');

    if (!cartItemsList || !itemCountElem || !subtotalElem || !shippingElem || !taxElem || !discountElem || !totalElem) {
        console.error('Cart page elements missing');
        return;
    }

    if (cart.length === 0) {
        emptyCartElem.style.display = 'block';
        cartContainer.style.display = 'none';
        return;
    } else {
        emptyCartElem.style.display = 'none';
        cartContainer.style.display = 'flex';
    }

    cartItemsList.innerHTML = '';

    let subtotal = 0;
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        const itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item';
        itemDiv.innerHTML = `
            <div class="cart-item-info">
                <h3>${item.name}</h3>
                <p>Price: ${formatUGX(item.price)}</p>
                <p>Quantity: 
                    <button onclick="changeQuantity(${index}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="changeQuantity(${index}, 1)">+</button>
                </p>
                <p>Total: ${formatUGX(itemTotal)}</p>
            </div>
            <button class="remove-btn" onclick="removeItem(${index})">Remove</button>
        `;
        cartItemsList.appendChild(itemDiv);
    });

    itemCountElem.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    subtotalElem.textContent = formatUGX(subtotal);

    // Example fixed shipping, tax, discount
    const shipping = 15000;
    const tax = 0;
    const discount = 0;

    shippingElem.textContent = formatUGX(shipping);
    taxElem.textContent = formatUGX(tax);
    discountElem.textContent = formatUGX(discount);

    const total = subtotal + shipping + tax - discount;
    totalElem.textContent = formatUGX(total);

    // Update payment modal total if exists
    const paymentTotal = document.getElementById('paymentTotal');
    if (paymentTotal) {
        paymentTotal.textContent = formatUGX(total);
    }
}

// Change quantity of a cart item
function changeQuantity(index, delta) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (!cart[index]) return;

    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
}

// Remove item from cart
function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (!cart[index]) return;

    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
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

// Initialize cart page
document.addEventListener('DOMContentLoaded', () => {
    renderCart();
    updateCartCount();
});

// Format number as UGX currency string
function formatUGX(amount) {
    return 'UGX ' + amount.toLocaleString('en-UG');
}

// Toast notification
function showToast(message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    if (toastMessage) toastMessage.textContent = message;
    if (toast) {
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    } else {
        alert(message);
    }
}

// Cart rendering and calculations
let currentDiscount = 0;

function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsList = document.getElementById('cartItemsList');
    const itemCountElem = document.getElementById('itemCount');
    const subtotalElem = document.getElementById('subtotal');
    const shippingElem = document.getElementById('shipping');
    const taxElem = document.getElementById('tax');
    const discountElem = document.getElementById('discount');
    const totalElem = document.getElementById('total');
    const emptyCartElem = document.getElementById('emptyCart');
    const cartContainer = document.getElementById('cartContainer');

    if (!cartItemsList || !itemCountElem || !subtotalElem || !shippingElem || !taxElem || !discountElem || !totalElem) {
        console.error('Cart page elements missing');
        return;
    }

    if (cart.length === 0) {
        emptyCartElem.style.display = 'block';
        cartContainer.style.display = 'none';
        return;
    } else {
        emptyCartElem.style.display = 'none';
        cartContainer.style.display = 'flex';
    }

    cartItemsList.innerHTML = '';

    let subtotal = 0;
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        const itemDiv = document.createElement('div');
        itemDiv.className = 'cart-item';
        itemDiv.innerHTML = `
            <div class="cart-item-info">
                <h3>${item.name}</h3>
                <p>Price: ${formatUGX(item.price)}</p>
                <p>Quantity: 
                    <button onclick="changeQuantity(${index}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button onclick="changeQuantity(${index}, 1)">+</button>
                </p>
                <p>Total: ${formatUGX(itemTotal)}</p>
            </div>
            <button class="remove-btn" onclick="removeItem(${index})">Remove</button>
        `;
        cartItemsList.appendChild(itemDiv);
    });

    itemCountElem.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);
    subtotalElem.textContent = formatUGX(subtotal);

    const shipping = 15000;
    const tax = 0;
    const discount = currentDiscount;

    shippingElem.textContent = formatUGX(shipping);
    taxElem.textContent = formatUGX(tax);
    discountElem.textContent = formatUGX(discount);

    const total = subtotal + shipping + tax - discount;
    totalElem.textContent = formatUGX(total);

    // Update payment modal total if exists
    const paymentTotal = document.getElementById('paymentTotal');
    if (paymentTotal) {
        paymentTotal.textContent = formatUGX(total);
    }
}

// Change quantity of a cart item
function changeQuantity(index, delta) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (!cart[index]) return;

    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
}

// Remove item from cart
function removeItem(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (!cart[index]) return;

    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    renderCart();
    updateCartCount();
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

// Promo code application
function applyPromoCode() {
    const promoInput = document.getElementById('promoInput');
    const code = promoInput.value.trim().toUpperCase();

    if (!code) {
        showToast('Please enter a promo code.');
        return;
    }

    // Example promo codes
    const promos = {
        'BIGB10': 10000,  // UGX 10,000 discount
        'FREESHIP': 15000 // Shipping discount
    };

    if (promos[code] !== undefined) {
        currentDiscount = promos[code];
        showToast(`Promo code applied! Discount: ${formatUGX(currentDiscount)}`);
        renderCart();
    } else {
        showToast('Invalid promo code.');
    }
}

// Proceed to checkout: open payment modal if cart not empty
function proceedToCheckout() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) {
        showToast('Your cart is empty.');
        return;
    }
    openPaymentModal();
}

// Payment modal controls
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) modal.style.display = 'block';
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) modal.style.display = 'none';
}

// Payment method selection
function selectPaymentMethod(method) {
    const methods = ['mtn', 'airtel', 'card', 'cod'];
    methods.forEach(m => {
        const form = document.getElementById(m + 'Form');
        const methodDiv = document.querySelector(`.payment-method[data-method="${m}"]`);
        if (form) form.style.display = (m === method) ? 'block' : 'none';
        if (methodDiv) {
            if (m === method) {
                methodDiv.classList.add('selected');
            } else {
                methodDiv.classList.remove('selected');
            }
        }
    });
}

// Payment processing stubs (replace with real integration)
function processMTNPayment() {
    const phone = document.getElementById('mtnPhone').value.trim();
    if (!phone) {
        showToast('Please enter your MTN phone number.');
        return;
    }
    alert(`MTN payment initiated for ${phone}.`);
    closePaymentModal();
    clearCartAfterPayment();
}

function processAirtelPayment() {
    const phone = document.getElementById('airtelPhone').value.trim();
    if (!phone) {
        showToast('Please enter your Airtel phone number.');
        return;
    }
    alert(`Airtel payment initiated for ${phone}.`);
    closePaymentModal();
    clearCartAfterPayment();
}

function processCardPayment() {
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const expiryDate = document.getElementById('expiryDate').value.trim();
    const cvv = document.getElementById('cvv').value.trim();
    const cardName = document.getElementById('cardName').value.trim();

    if (!cardNumber || !expiryDate || !cvv || !cardName) {
        showToast('Please fill in all card details.');
        return;
    }
    alert('Card payment processed.');
    closePaymentModal();
    clearCartAfterPayment();
}

function processCODPayment() {
    const address = document.getElementById('deliveryAddress').value.trim();
    const phone = document.getElementById('deliveryPhone').value.trim();

    if (!address || !phone) {
        showToast('Please enter delivery address and phone.');
        return;
    }
    alert('Cash on Delivery confirmed.');
    closePaymentModal();
    clearCartAfterPayment();
}

// Clear cart after successful payment
function clearCartAfterPayment() {
    localStorage.removeItem('cart');
    currentDiscount = 0;
    renderCart();
    updateCartCount();
    showToast('Thank you for your purchase!');
}

// Close modal when clicking outside content
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
};

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    renderCart();
    updateCartCount();
    // Hide all payment forms initially
    selectPaymentMethod(null);
});
</script>
</body>
</html>