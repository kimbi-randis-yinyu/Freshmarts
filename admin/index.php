<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Dashboard';

$revenue = $pdo->query("SELECT COALESCE(SUM(total_amount),0) AS total FROM orders WHERE payment_status = 'Paid'")->fetch()['total'];
$order_count = $pdo->query('SELECT COUNT(*) AS c FROM orders')->fetch()['c'];
$inquiry_count = $pdo->query("SELECT COUNT(*) AS c FROM contact_messages WHERE status = 'Unread'")->fetch()['c'];
$product_count = $pdo->query("SELECT COUNT(*) AS c FROM products WHERE stock_status = 'in_stock'")->fetch()['c'];

$recent_orders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 6')->fetchAll();

require __DIR__ . '/includes/admin_header.php';

?>

<meta name="viewport" content="width=device-width,initial-scale=1.0">
  


<div class="stat-grid">
    <div class="stat-card"><div class="label">Total Revenue (Paid)</div><div class="value"><?= fmt_price($revenue) ?></div></div>
    <div class="stat-card"><div class="label">Total Orders</div><div class="value"><?= (int) $order_count ?></div></div>
    <div class="stat-card"><div class="label">Unread Inquiries</div><div class="value"><?= (int) $inquiry_count ?></div></div>
    <div class="stat-card"><div class="label">Active Products</div><div class="value"><?= (int) $product_count ?></div></div>
</div>

<div class="admin-card">
    <div class="admin-card-head">
        <h3>Recent Orders</h3>
        <a href="orders.php" style="font-size:13px; font-weight:700; color:var(--color-primary);">View all</a>
    </div>
    <table class="admin-table recent-orders">
        <thead><tr><th>Tracking ID</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php if (!empty($recent_orders)): ?>
       
        
        <?php foreach ($recent_orders as $o): ?>
            <tr>
                <td data-label="Tracking ID"><a href="order_detail.php?id=<?php echo (int) $o['id'] ?>"><?php echo clean($o['tracking_id']) ?></a></td>
                <td data-label="Customer"><?= clean($o['customer_name']) ?></td>
                <td data-label="Total">$<?= fmt_price($o['total_amount']) ?></td>
                <td data-label="Payment"><?= clean($o['payment_method']) ?></td>
                <td data-label="Status"><span class="tag-pill tag-<?= strtolower($o['order_status']) ?>"><?= clean($o['order_status']) ?></span></td>
                <td data-label="Date"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php else:?>
        <tr><td colspan="6" style="text-align:center; color:var(--color-text-muted);">No orders yet.</td></tr>
        <?php endif;?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>

