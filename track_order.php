<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Track Your Order';
$page_active = 'track';

$order = null;
$items = [];
$searched = false;

if (isset($_GET['tracking_id']) && trim($_GET['tracking_id']) !== '') {
    $searched = true;
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE tracking_id = ?');
    $stmt->execute([trim($_GET['tracking_id'])]);
    $order = $stmt->fetch();
    if ($order) {
        $itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $itemStmt->execute([$order['id']]);
        $items = $itemStmt->fetchAll();
    }
}

$status_steps = ['Pending', 'Processing', 'Completed'];
$current_idx = -1;
if ($order) {
    $current_idx = array_search($order['order_status'], $status_steps, true);
    if ($current_idx === false) $current_idx = -1;
}
$progress_pct = $current_idx < 0 ? 0 : ($current_idx / (count($status_steps) - 1)) * 100;

require __DIR__ . '/includes/header.php';
?>

<section class="track-page">
    <div class="container track-wrap">
        <div class="section-head">
            <div>
                <span class="section-tag">Order Tracking</span>
                <h2>Track Your Order</h2>
            </div>
        </div>

        <div class="track-search">
            <form method="GET">
                <div class="form-group">
                    <label for="tracking_id">Enter your Tracking ID</label>
                    <input type="text" id="tracking_id" name="tracking_id"
                           placeholder="e.g. FM-7F2K9A"
                           value="<?= clean($_GET['tracking_id'] ?? '') ?>" required
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">Track Order</button>
            </form>
        </div>

        <?php if ($searched && !$order): ?>
            <div class="empty-state" style="background:#fff;border-radius:var(--radius-lg);border:1px solid var(--color-border);">
                <p style="font-size:18px;margin-bottom:8px;">🔍 No order found</p>
                <p>We couldn’t find an order with that tracking ID. Please double-check and try again.</p>
            </div>
        <?php elseif ($order): ?>
            <div class="track-result">
                <div class="summary-row">
                    <span>Tracking ID</span>
                    <strong><?= clean($order['tracking_id']) ?></strong>
                </div>
                <div class="summary-row">
                    <span>Order Date</span>
                    <span><?= date('M j, Y · g:ia', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="summary-row">
                    <span>Customer</span>
                    <span><?= clean($order['customer_name']) ?></span>
                </div>
                <div class="summary-row">
                    <span>Payment</span>
                    <span><?= clean($order['payment_method']) ?> — <?= clean($order['payment_status']) ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?= fmt_price($order['total_amount']) ?></span>
                </div>

                <?php if ($order['order_status'] === 'Cancelled'): ?>
                    <p class="track-cancelled">This order was cancelled.</p>
                <?php else: ?>
                    <div class="track-progress">
                        <div class="track-progress-bar">
                            <div class="fill" style="width: <?= (int) $progress_pct ?>%;"></div>
                        </div>
                        <div class="track-steps">
                            <?php foreach ($status_steps as $i => $step): ?>
                                <?php
                                $cls = '';
                                if ($current_idx > $i) $cls = 'done';
                                elseif ($current_idx === $i) $cls = 'active';
                                ?>
                                <div class="track-step <?= $cls ?>">
                                    <div class="dot"><?= $current_idx >= $i ? '✓' : ($i + 1) ?></div>
                                    <div class="label"><?= $step ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($items): ?>
                <div class="track-items">
                    <h3>Items ordered</h3>
                    <?php foreach ($items as $it): ?>
                        <div class="summary-row">
                            <span><?= clean($it['product_name']) ?> × <?= (int) $it['quantity'] ?></span>
                            <span><?= fmt_price($it['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
