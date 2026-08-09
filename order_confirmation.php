<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Order Confirmed';

$tracking_id = $_GET['tracking_id'] ?? '';
$order = null;
$items = [];

if ($tracking_id !== '') {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE tracking_id = ?');
    $stmt->execute([$tracking_id]);
    $order = $stmt->fetch();

    if ($order) {
        $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $itemStmt->execute([$order['id']]);
        $items = $itemStmt->fetchAll();
    }
}

require __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container" style="max-width:700px;">
        <?php if (!$order): ?>
            <div class="empty-state">
                <h2>Order not found</h2>
                <p>We couldn't find an order with that tracking ID.</p>
                <a href="index.php" class="btn btn-primary mt-2">Back to Home</a>
            </div>
        <?php else: ?>
        <div class="form-card text-center">
            <div style="font-size:50px;">✅</div>
            <h2 style="margin:14px 0 6px;">Thank you, <?= clean($order['customer_name']) ?>!</h2>
            <p style="color:var(--color-text-muted);">Your order has been placed successfully.</p>

            <div style="background:var(--color-primary-light); border-radius:var(--radius-md); padding:18px; margin:24px 0;">
                <div style="font-size:13px; color:var(--color-text-muted);">Tracking ID</div>
                <div style="font-size:22px; font-weight:800; color:var(--color-primary);"><?= clean($order['tracking_id']) ?></div>
            </div>

            <div style="text-align:left;">
                <h3 style="margin-bottom:10px;">Order Summary</h3>
                <?php foreach ($items as $it): ?>
                    <div class="summary-row">
                        <span><?= clean($it['product_name']) ?> × <?= (int) $it['quantity'] ?></span>
                        <span><?= fmt_price($it['subtotal']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row total"><span>Total</span><span><?= fmt_price($order['total_amount']) ?></span></div>
            </div>

            <div class="mt-2" style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <a href="track_order.php?tracking_id=<?= urlencode($order['tracking_id']) ?>" class="btn btn-outline">Track Order</a>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
    // Order placed successfully — clear the local cart.
    localStorage.removeItem('freshmart_cart');
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
