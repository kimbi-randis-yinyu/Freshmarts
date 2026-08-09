/* ============================================================
   FreshMart — main.js
   Handles: mobile nav, cart (localStorage), add-to-cart buttons,
   toasts, contact form AJAX submit.
   ============================================================ */

const CART_KEY = 'freshmart_cart';

/* ---------- Cart helpers (shared across pages) ---------- */
function getCart() {
    try {
        return JSON.parse(localStorage.getItem(CART_KEY)) || [];
    } catch (e) {
        return [];
    }
}

function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    updateCartCount();
}

function addToCart(product, qty = 1) {
    const cart = getCart();
    const existing = cart.find(item => item.id === product.id);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({ ...product, qty });
    }
    saveCart(cart);
}

function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
}

function updateCartQty(productId, qty) {
    const cart = getCart();
    const item = cart.find(i => i.id === productId);
    if (item) {
        item.qty = Math.max(1, qty);
        saveCart(cart);
    }
}

function cartTotal() {
    return getCart().reduce((sum, item) => sum + item.price * item.qty, 0);
}

function updateCartCount() {
    const countEls = document.querySelectorAll('.cart-count');
    const count = getCart().reduce((sum, item) => sum + item.qty, 0);
    countEls.forEach(el => (el.textContent = count));
}

/* ---------- Toast ---------- */
function showToast(message, type = 'success') {
    let toast = document.getElementById('fm-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'fm-toast';
        toast.className = 'toast';
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.className = 'toast show' + (type === 'error' ? ' error' : '');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
}

/* ---------- Mobile nav ---------- */
function initNav() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    if (!hamburger || !navLinks) return;

    let overlay = document.querySelector('.nav-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'nav-overlay';
        document.body.appendChild(overlay);
    }

    function openNav() {
        navLinks.classList.add('open');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeNav() {
        navLinks.classList.remove('open');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', () => {
        if (navLinks.classList.contains('open')) closeNav();
        else openNav();
    });

    overlay.addEventListener('click', closeNav);

    // Close menu when a nav link is clicked so the page navigates cleanly
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            closeNav();
        });
    });
}

/* ---------- Add-to-cart buttons on product cards ---------- */
function initAddToCartButtons() {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                unit: btn.dataset.unit,
                image: btn.dataset.image,
            };
            addToCart(product, 1);
            showToast(`${product.name} added to cart`);
        });
    });

    document.querySelectorAll('.order-now-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                unit: btn.dataset.unit,
                image: btn.dataset.image,
            };
            addToCart(product, 1);
        });
    });
}

/* ---------- Contact form (AJAX) ---------- */
function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(form);
            const res = await fetch('process_contact.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();

            if (data.success) {
                showToast(data.message || 'Message sent successfully!');
                form.reset();
            } else {
                showToast(data.message || 'Something went wrong. Please try again.', 'error');
            }
        } catch (err) {
            showToast('Network error. Please try again.', 'error');
        } finally {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    initNav();
    initAddToCartButtons();
    initContactForm();
});
