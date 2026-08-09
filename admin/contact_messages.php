<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Contact Messages';

if (isset($_GET['delete'])) {
    $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([(int) $_GET['delete']]);
    flash('admin_success', 'Message deleted.');
    redirect('contact_messages.php');
}
if (isset($_GET['mark_read'])) {
    $pdo->prepare("UPDATE contact_messages SET status = 'Read' WHERE id = ?")->execute([(int) $_GET['mark_read']]);
    redirect('contact_messages.php');
}

$messages = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-head"><h3>All Messages (<?= count($messages) ?>)</h3></div>
    <table class="admin-table">
        <thead><tr><th>Status</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (!$messages): ?>
            <tr><td colspan="7" style="text-align:center; color:var(--color-text-muted);">No messages yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($messages as $m): ?>
            <tr>
                <td><span class="tag-pill tag-<?= strtolower($m['status']) ?>"><?= clean($m['status']) ?></span></td>
                <td><?= clean($m['name']) ?></td>
                <td><?= clean($m['email']) ?></td>
                <td><?= clean($m['subject'] ?: '—') ?></td>
                <td style="max-width:280px;"><?= clean(mb_strimwidth($m['message'], 0, 80, '…')) ?></td>
                <td><?= date('M j, Y', strtotime($m['created_at'])) ?></td>
                <td class="action-links">
                    <a href="view_message.php?id=<?=(int)$m['id']?>" class="btn-view">View</a>
                    <?php if ($m['status'] === 'Unread'): ?>
                        <a href="contact_messages.php?mark_read=<?= (int) $m['id'] ?>">Mark Read</a>
                    <?php endif; ?>
                    <a href="contact_messages.php?delete=<?= (int) $m['id'] ?>" class="danger"
                       onclick="return confirm('Delete this message?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
