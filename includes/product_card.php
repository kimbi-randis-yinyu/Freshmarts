<?php
/**
 * Reusable product card. Expects $p (associative array row from `products`).
 * Included in a loop from index.php.
 */
$in_stock = ($p['stock_status'] ?? 'in_stock') === 'in_stock';
?>
<div class="product-card">
    <div class="product-thumb">
        <?php if (!empty($p['badge'])): ?>
            <span class="badge<?= $p['badge'] === 'Best Seller' ? '' : ' badge-danger' ?>"><?= clean($p['badge']) ?></span>
        <?php endif; ?>
        <img src="<?= clean($p['image_url']) ?>" alt="<?= clean($p['name']) ?>"
             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&q=80';">
    </div>
    <div class="product-body">
        <h3><?= clean($p['name']) ?></h3>
        <div class="price-row">
            <span class="price-now"><?= fmt_price($p['price']) ?><small style="font-weight:500;color:var(--color-text-muted);"><?= clean($p['unit']) ?></small></span>
            <?php if (!empty($p['old_price'])): ?><span class="price-old"><?= fmt_price($p['old_price']) ?></span><?php endif; ?>
        </div>
        <div class="rating">
            <span class="stars">★★★★★</span> <?= number_format((float) $p['rating'], 1) ?> (<?= (int) $p['rating_count'] ?>)
        </div>

        <?php if ($in_stock): ?>
        <button class="btn btn-primary btn-block btn-sm order-now-btn"
                data-id="<?= (int) $p['id'] ?>"
                data-name="<?= clean($p['name']) ?>"
                data-price="<?= (float) $p['price'] ?>"
                data-unit="<?= clean($p['unit']) ?>"
                data-image="<?= clean($p['image_url']) ?>">
            Order Now
        </button>
        <?php else: ?>
        <span class="out-of-stock-tag">Out of Stock</span>
        <?php endif; ?>
    </div>
</div>
