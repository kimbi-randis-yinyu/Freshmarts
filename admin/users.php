<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Users';

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    // Prevent deleting yourself
    if ($id === (int) ($_SESSION['user_id'] ?? 0)) {
        flash('admin_error', 'You cannot delete your own account.');
        redirect('users.php');
    }
    $stmt = $pdo->prepare('SELECT avatar FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);

    if ($user && !empty($user['avatar']) && strpos($user['avatar'], 'uploads/') === 0) {
        $path = __DIR__ . '/../' . $user['avatar'];
        if (is_file($path)) @unlink($path);
    }
    flash('admin_success', 'User deleted.');
    redirect('users.php');
}

$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-head">
        <h3>All Users (<?= count($users) ?>)</h3>
        <a href="user_form.php" class="btn btn-primary btn-sm">+ Add User</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$users): ?>
            <tr><td colspan="6" style="text-align:center; color:var(--color-text-muted);">No users yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($users as $u): ?>
            <tr>
                <td data-label="Photo">
                    <?php if (!empty($u['avatar'])): ?>
                        <img class="thumb user-avatar-thumb" src="../<?= clean($u['avatar']) ?>" alt="<?= clean($u['name']) ?>"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Crect fill=%22%23e2e8e2%22 width=%2240%22 height=%2240%22/%3E%3Ctext x=%2250%25%22 y=%2255%25%22 text-anchor=%22middle%22 fill=%22%236b7a70%22 font-size=%2216%22%3E👤%3C/text%3E%3C/svg%3E'">
                    <?php else: ?>
                        <span class="avatar-placeholder">👤</span>
                    <?php endif; ?>
                </td>
                <td data-label="Name"><?= clean($u['name']) ?></td>
                <td data-label="Email"><?= clean($u['email']) ?></td>
                <td data-label="Role">
                    <?php if ($u['role'] === 'admin'): ?>
                        <span class="tag-pill tag-completed">Admin</span>
                    <?php else: ?>
                        <span class="tag-pill tag-processing">Customer</span>
                    <?php endif; ?>
                </td>
                <td data-label="Joined"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                <td data-label="Actions" class="action-links">
                    <a href="user_form.php?id=<?= (int) $u['id'] ?>">Edit</a>
                    <?php if ((int) $u['id'] !== (int) ($_SESSION['user_id'] ?? 0)): ?>
                        <a href="users.php?delete=<?= (int) $u['id'] ?>" class="danger"
                           onclick="return confirm('Delete this user permanently?');">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
