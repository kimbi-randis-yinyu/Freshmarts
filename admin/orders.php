<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Orders';

$status_filter = $_GET['status'] ?? '';
$valid_statuses = ['Pending', 'Processing', 'Completed', 'Cancelled'];

if ($status_filter && in_array($status_filter, $valid_statuses, true)) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE order_status = ? ORDER BY created_at DESC');
    $stmt->execute([$status_filter]);
    $orders = $stmt->fetchAll();
} else {
    $orders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
}

require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-head">
        <h3>All Orders (<?= count($orders) ?>)</h3>
        <div style="display:flex; gap:8px;">
            <a href="orders.php" class="btn btn-outline btn-sm <?= !$status_filter ? 'btn-primary' : '' ?>">All</a>
            <?php foreach ($valid_statuses as $s): ?>
                <a href="orders.php?status=<?= urlencode($s) ?>" class="btn btn-outline btn-sm"><?= $s ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <table class="admin-table">
        <thead><tr><th>Tracking ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php if (!$orders): ?>
            <tr><td colspan="8" style="text-align:center; color:var(--color-text-muted);">No orders found.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= clean($o['tracking_id']) ?></td>
                <td><?= clean($o['customer_name']) ?></td>
                <td><?= clean($o['customer_phone']) ?></td>
                <td><?= fmt_price($o['total_amount']) ?></td>
                <td><?= clean($o['payment_method']) ?> · <?= clean($o['payment_status']) ?></td>
                <td><span class="tag-pill tag-<?= strtolower($o['order_status']) ?>"><?= clean($o['order_status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                <td class="action-links"><a href="order_detail.php?id=<?= (int) $o['id'] ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
