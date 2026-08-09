<?php
require_once __DIR__ . '/includes/admin_auth.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    flash('admin_error', 'Order not found.');
    redirect('orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = $_POST['order_status'] ?? $order['order_status'];
    $payment_status = $_POST['payment_status'] ?? $order['payment_status'];

    $stmt = $pdo->prepare('UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?');
    $stmt->execute([$order_status, $payment_status, $id]);
    flash('admin_success', 'Order updated.');
    redirect('order_detail.php?id=' . $id);
}

$itemStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

$admin_page_title = 'Order ' . $order['tracking_id'];
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-form-grid">
    <div class="admin-card" style="padding:24px;">
        <h3 style="margin-bottom:16px;">Items</h3>
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td><?= clean($it['product_name']) ?></td>
                    <td><?= (int) $it['quantity'] ?></td>
                    <td><?= fmt_price($it['price']) ?></td>
                    <td><?= fmt_price($it['subtotal']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary-row total" style="margin-top:14px;"><span>Total</span><span><?= fmt_price($order['total_amount']) ?></span></div>

        <h3 style="margin:24px 0 12px;">Customer &amp; Delivery</h3>
        <p><strong>Name:</strong> <?= clean($order['customer_name']) ?></p>
        <p><strong>Phone:</strong> <?= clean($order['customer_phone']) ?></p>
        <p><strong>Address:</strong> <?= clean($order['delivery_address']) ?></p>
        <?php if ($order['delivery_instructions']): ?><p><strong>Instructions:</strong> <?= clean($order['delivery_instructions']) ?></p><?php endif; ?>
    </div>

    <div class="admin-card" style="padding:24px;">
        <h3 style="margin-bottom:14px;">Update Status</h3>
        <form method="POST">
            <div class="form-group">
                <label>Order Status</label>
                <select name="order_status">
                    <?php foreach (['Pending','Processing','Completed','Cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['order_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Payment Status</label>
                <select name="payment_status">
                    <?php foreach (['Pending','Paid'] as $s): ?>
                        <option value="<?= $s ?>" <?= $order['payment_status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
        </form>
        <a href="orders.php" class="btn btn-outline btn-block mt-2">← Back to Orders</a>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
