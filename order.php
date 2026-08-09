<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Your Order';
$page_active = 'shop';
require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-head"><div><span class="section-tag">Checkout</span><h2>Complete Your Order</h2></div></div>

        <div id="empty-cart-msg" class="empty-state" style="display:none;">
            Your cart is empty. <a href="index.php#shop" class="section-link">Browse products</a> to get started.
        </div>

        <form id="checkout-form" action="process_order.php" method="POST">
            <div class="order-grid" id="order-content">
                <!-- LEFT: cart items + shipping form -->
                <div>
                    <div class="form-card" style="margin-bottom:24px;">
                        <h3 style="margin-bottom:16px;">Your Items</h3>
                        <div id="cart-items-list"></div>
                    </div>

                    <div class="form-card">
                        <h3 style="margin-bottom:16px;">Shipping Details</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="customer_name">Full Name</label>
                                <input type="text" id="customer_name" name="customer_name" required>
                            </div>
                            <div class="form-group">
                                <label for="customer_phone">Phone Number (MoMo format, e.g. +237 6XX XXX XXX)</label>
                                <input type="tel" id="customer_phone" name="customer_phone" placeholder="+237 6XX XXX XXX" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="delivery_address">Delivery Address</label>
                            <input type="text" id="delivery_address" name="delivery_address" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery_instructions">Special Delivery Instructions (optional)</label>
                            <textarea id="delivery_instructions" name="delivery_instructions" placeholder="E.g. gate code, landmark, preferred time..."></textarea>
                        </div>

                        <h3 style="margin:20px 0 14px;">Payment Method</h3>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="payment_method" onclick="alert('Use this number to pay:653315662')" value="MoMo" checked>
                                📱 MTN MoMo / Orange Money
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="payment_method" value="Card">
                                💳 Credit / Debit Card
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="payment_method" value="COD">
                                💵 Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: order summary -->
                <div class="order-summary">
                    <h3 style="margin-bottom:16px;">Order Summary</h3>
                    <div class="summary-row"><span>Subtotal</span><span id="summary-subtotal">$0.00</span></div>
                    <div class="summary-row"><span>Delivery Fee</span><span id="summary-delivery">$2.00</span></div>
                    <div class="summary-row total"><span>Total</span><span id="summary-total">$0.00</span></div>
                    <input type="hidden" name="cart_data" id="cart_data_input">
                    <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Place Order</button>
                    <p style="font-size:12px; color:var(--color-text-muted); margin-top:10px; text-align:center;">
                        By placing this order you agree to our terms of service.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
const DELIVERY_FEE = 2.00;

function renderOrderPage() {
    const cart = getCart();
    const listEl = document.getElementById('cart-items-list');
    const contentEl = document.getElementById('order-content');
    const emptyEl = document.getElementById('empty-cart-msg');

    if (cart.length === 0) {
        contentEl.style.display = 'none';
        emptyEl.style.display = 'block';
        return;
    }
    contentEl.style.display = 'grid';
    emptyEl.style.display = 'none';

    listEl.innerHTML = cart.map(item => `
        <div class="order-item" data-id="${item.id}">
            <img src="${item.image}" alt="${item.name}" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=100&q=80';">
            <div style="flex:1;">
                <strong>${item.name}</strong>
                <div style="font-size:13px;color:var(--color-text-muted);">${fmtPrice(item.price)} ${item.unit}</div>
            </div>
            <div class="qty-control">
                <button type="button" onclick="changeQty('${item.id}', -1)">−</button>
                <input type="text" readonly value="${item.qty}">
                <button type="button" onclick="changeQty('${item.id}', 1)">+</button>
            </div>
            <button type="button" onclick="removeItem('${item.id}')" style="color:var(--color-danger);font-size:18px;margin-left:10px;">×</button>
        </div>
    `).join('');

    updateSummary();
}

function fmtPrice(n) { return '$' + Number(n).toFixed(2); }

function updateSummary() {
    const subtotal = cartTotal();
    const total = subtotal > 0 ? subtotal + DELIVERY_FEE : 0;
    document.getElementById('summary-subtotal').textContent = fmtPrice(subtotal);
    document.getElementById('summary-delivery').textContent = fmtPrice(subtotal > 0 ? DELIVERY_FEE : 0);
    document.getElementById('summary-total').textContent = fmtPrice(total);
    document.getElementById('cart_data_input').value = JSON.stringify(getCart());
}

function changeQty(id, delta) {
    const cart = getCart();
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty = Math.max(1, item.qty + delta);
    saveCart(cart);
    renderOrderPage();
}

function removeItem(id) {
    removeFromCart(id);
    renderOrderPage();
}

document.getElementById('checkout-form').addEventListener('submit', function (e) {
    const cart = getCart();
    if (cart.length === 0) {
        e.preventDefault();
        showToast('Your cart is empty.', 'error');
        return;
    }
    document.getElementById('cart_data_input').value = JSON.stringify(cart);
});

document.addEventListener('DOMContentLoaded', renderOrderPage);
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
