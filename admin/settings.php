<?php
require_once __DIR__ . '/includes/admin_auth.php';
$admin_page_title = 'Site Settings';

$settings = get_settings();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $site_name = trim($_POST['site_name'] ?? '');
    $hotline = trim($_POST['hotline'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $og_image = $settings['og_image'] ?? '';
    $favicon_path = $settings['favicon_path'] ?? '';

    // Favicon upload
    if (!empty($_FILES['favicon']['name'])) {
        $ext = strtolower(pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'ico', 'jpg', 'jpeg'], true)) {
            $filename = 'favicon_' . uniqid() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $filename;
            if (move_uploaded_file($_FILES['favicon']['tmp_name'], $dest)) {
                $favicon_path = 'uploads/' . $filename;
            }
        }
    }
    // OG image upload
    if (!empty($_FILES['og_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['og_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            $filename = 'og_' . uniqid() . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $filename;
            if (move_uploaded_file($_FILES['og_image']['tmp_name'], $dest)) {
                $og_image = 'uploads/' . $filename;
            }
        }
    }

    if (empty($settings)) {
        $stmt = $pdo->prepare(
            'INSERT INTO site_settings (site_name, hotline, email, address, meta_description, og_image, favicon_path)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$site_name, $hotline, $email, $address, $meta_description, $og_image, $favicon_path]);
    } else {
        $stmt = $pdo->prepare(
            'UPDATE site_settings SET site_name=?, hotline=?, email=?, address=?, meta_description=?, og_image=?, favicon_path=? WHERE id=?'
        );
        $stmt->execute([$site_name, $hotline, $email, $address, $meta_description, $og_image, $favicon_path, $settings['id']]);
    }

    flash('admin_success', 'Site settings updated.');
    redirect('settings.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($current, $admin['password'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $admin['id']]);
        flash('admin_success', 'Password changed successfully.');
        redirect('settings.php');
    }
}

$settings = get_settings(); // refresh after possible insert
require __DIR__ . '/includes/admin_header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= clean($error) ?></div><?php endif; ?>

<div class="admin-form-grid">
    <div class="admin-card" style="padding:24px;">
        <h3 style="margin-bottom:16px;">General Settings</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_settings" value="1">
            <div class="form-grid">
                <div class="form-group"><label>Site Name</label><input type="text" name="site_name" value="<?= clean($settings['site_name'] ?? '') ?>" required></div>
                <div class="form-group"><label>Hotline</label><input type="text" name="hotline" value="<?= clean($settings['hotline'] ?? '') ?>"></div>
            </div>
            <div class="form-grid">
                <div class="form-group"><label>Support Email</label><input type="email" name="email" value="<?= clean($settings['email'] ?? '') ?>"></div>
                <div class="form-group"><label>Address</label><input type="text" name="address" value="<?= clean($settings['address'] ?? '') ?>"></div>
            </div>
            <div class="form-group">
                <label>Meta Description (used for SEO)</label>
                <textarea name="meta_description"><?= clean($settings['meta_description'] ?? '') ?></textarea>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Favicon</label>
                    <?php if (!empty($settings['favicon_path'])): ?><img src="../<?= clean($settings['favicon_path']) ?>" style="width:32px; margin-bottom:8px;" onerror="this.style.display='none'"><?php endif; ?>
                    <input type="file" name="favicon" accept=".png,.ico,.jpg,.jpeg">
                </div>
                <div class="form-group">
                    <label>Open Graph Share Image</label>
                    <?php if (!empty($settings['og_image'])): ?><img src="../<?= clean($settings['og_image']) ?>" style="width:100px; border-radius:6px; margin-bottom:8px;" onerror="this.style.display='none'"><?php endif; ?>
                    <input type="file" name="og_image" accept=".png,.jpg,.jpeg,.webp">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <div class="admin-card" style="padding:24px;">
        <h3 style="margin-bottom:16px;">Change Admin Password</h3>
        <form method="POST">
            <input type="hidden" name="change_password" value="1">
            <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
            <div class="form-group"><label>New Password</label><input type="password" name="new_password" minlength="8" required></div>
            <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" minlength="8" required></div>
            <button type="submit" class="btn btn-primary btn-block">Change Password</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
