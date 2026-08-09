<?php
require_once __DIR__ . '/config/functions.php';

$page_title       = 'Fresh Groceries Delivered to You';
$page_description = get_settings()['meta_description'] ?? '';
$page_active       = 'home';

// Categories
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// Featured / deal products (has old_price or badge)
$deals = $pdo->query(
    "SELECT * FROM products WHERE old_price IS NOT NULL OR badge IS NOT NULL ORDER BY created_at DESC LIMIT 4"
)->fetchAll();

// All products for the shop grid (optionally filtered by category)
$cat_filter = $_GET['category'] ?? '';
if ($cat_filter !== '') {
    $stmt = $pdo->prepare(
        "SELECT p.* FROM products p JOIN categories c ON c.id = p.category_id WHERE c.slug = ? ORDER BY p.created_at DESC"
    );
    $stmt->execute([$cat_filter]);
    $products = $stmt->fetchAll();
} else {
    $products = $pdo->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 12')->fetchAll();
}

require __DIR__ . '/includes/header.php';
?>

<!-- ============ Hero ============ -->
<section class="hero" id="home">
    <div class="container hero-grid">
        <div>
            <span class="hero-badge">🌱 100% Fresh &amp; Organic</span>
            <h1>Fresh Groceries<br>Delivered to <span class="accent">Your Door</span></h1>
            <p>Shop the best quality fruits, vegetables, bakery items and everyday essentials — picked fresh and delivered fast across the city.</p>
            <div class="hero-actions">
                <a href="#shop" class="btn btn-primary">Shop Now</a>
                <a href="#about" class="btn btn-outline">Learn More</a>
            </div>
            <div class="hero-features">
                <div class="hero-feature"><strong>🚚 Free Delivery</strong>On orders over $30</div>
                <div class="hero-feature"><strong>✅ Quality Guarantee</strong>100% satisfaction</div>
                <div class="hero-feature"><strong>⏱ Fast Delivery</strong>Same-day service</div>
            </div>
        </div>
        <div class="hero-image">
            <img src="assets/images/hero.jpg" alt="Fresh groceries basket"
                 onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80';">
        </div>
    </div>
</section>

<!-- ============ Categories ============ -->
<section class="section" id="categories">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-tag">Categories</span>
                <h2>Shop by Category</h2>
            </div>
        </div>
        <div class="cat-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="index.php?category=<?= urlencode($cat['slug']) ?>#shop" class="cat-card">
                <img src="<?= clean($cat['image']) ?>" alt="<?= clean($cat['name']) ?>"
                     onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&q=80';">
                <h3><?= clean($cat['name']) ?></h3>
                <span><?= (int) $cat['item_count'] ?> items</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ Deals     src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=200&q=80 ============ -->
<?php if ($deals): ?>
<section class="section section-alt" id="deals">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-tag">Limited Time</span>
                <h2>Today's Best Deals</h2>
            </div>
        </div>
        <div class="product-grid">
            <?php foreach ($deals as $p): include __DIR__ . '/includes/product_card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============ Shop / All Products ============ -->
<section class="section" id="shop">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="section-tag">Our Products</span>
                <h2><?= $cat_filter ? clean(ucwords(str_replace('-', ' ', $cat_filter))) : 'Fresh Picks For You' ?></h2>
            </div>
            <?php if ($cat_filter): ?><a href="index.php#shop" class="section-link">View all</a><?php endif; ?>
        </div>

        <?php if ($products): ?>
        <div class="product-grid">
            <?php foreach ($products as $p): include __DIR__ . '/includes/product_card.php'; endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">No products found in this category yet.</div>
        <?php endif; ?>
    </div>
</section>

<!-- ============ Promo Banners ============ -->
<section class="section section-alt">
    <div class="container promo-grid">
        <div class="promo-card" style="background-image:url('assets/images/promo-1.jpg')">
            <div><span class="tag">Fresh Vegetables</span><h3>Up to 20% Off</h3><a href="index.php?category=vegetables#shop" class="btn btn-accent btn-sm">Shop Now</a></div>
        </div>
        <div class="promo-card" style="background-image:url('assets/images/promo-2.jpg')">
            <div><span class="tag">Bakery</span><h3>Freshly Baked Daily</h3><a href="index.php?category=bakery#shop" class="btn btn-accent btn-sm">Shop Now</a></div>
        </div>
        <div class="promo-card" style="background-image:url('assets/images/promo-3.jpg')">
            <div><span class="tag">Fruits</span><h3>Handpicked &amp; Sweet</h3><a href="index.php?category=fruits#shop" class="btn btn-accent btn-sm">Shop Now</a></div>
        </div>
    </div>
</section>

<!-- ============ About ============ -->
<section class="section" id="about">
    <div class="container about-grid">
        <img src="assets/images/about.jpg" alt="About FreshMart"
             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=700&q=80';">
        <div>
            <span class="section-tag">About Us</span>
            <h2>Bringing Farm-Fresh Quality to Your Table</h2>
            <p style="color:var(--color-text-muted); margin:14px 0 18px;">
                FreshMart partners directly with local farmers and bakeries to bring you the freshest produce
                and baked goods, delivered quickly and reliably.
            </p>
            <ul class="check-list">
                <li>Sourced from trusted local farms</li>
                <li>Same-day delivery across the city</li>
                <li>Secure MoMo, card &amp; cash-on-delivery payment</li>
                <li>Friendly, responsive customer support</li>
            </ul>
        </div>
    </div>
</section>

<!-- ============ Why Choose Us ============ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-head"><div><span class="section-tag">Why FreshMart</span><h2>Why Shop With Us</h2></div></div>
        <div class="why-grid">
            <div class="why-card"><div class="icon">🚚</div><h3>Fast Delivery</h3><p style="color:var(--color-text-muted); font-size:14px; margin-top:8px;">Same-day delivery on orders placed before 3pm.</p></div>
            <div class="why-card"><div class="icon">🌿</div><h3>Fresh Quality</h3><p style="color:var(--color-text-muted); font-size:14px; margin-top:8px;">Every item checked for freshness before it ships.</p></div>
            <div class="why-card"><div class="icon">💳</div><h3>Flexible Payment</h3><p style="color:var(--color-text-muted); font-size:14px; margin-top:8px;">Pay with MoMo, Orange Money, card, or cash on delivery.</p></div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
