<?php
/**
 * ONE-TIME SETUP. Run this once in the browser (e.g. http://localhost/freshmart/admin/setup_admin.php)
 * to create your first admin account, then DELETE this file.
 * If an admin already exists, this script refuses to run again.
 */
require_once __DIR__ . '/../config/functions.php';

$existing = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'")->fetch();
if ($existing['c'] > 0) {
    die('An admin account already exists. For security this setup script will not run again. Please delete admin/setup_admin.php.');
}

$error = '';
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Please provide a name, valid email, and a password of at least 8 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "admin")');
        $stmt->execute([$name, $email, $hash]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Setup Admin — FreshMart</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body style="background:var(--color-bg-alt);">
<div class="container auth-wrap">
    <div class="form-card">
        <?php if ($done): ?>
            <h1>✅ Admin account created</h1>
            <p class="sub">You can now sign in.</p>
            <a href="admin_login.php" class="btn btn-primary btn-block mt-2">Go to Admin Login</a>
            <p style="margin-top:16px; font-size:13px; color:var(--color-danger);">
                Important: delete <code>admin/setup_admin.php</code> now for security.
            </p>
        <?php else: ?>
            <h1>Create Admin Account</h1>
            <p class="sub">One-time setup — this page disables itself after use.</p>
            <?php if ($error): ?><p style="color:var(--color-danger); margin-bottom:14px;"><?= clean($error) ?></p><?php endif; ?>
            <form method="POST">
                <div class="form-group"><label>Full Name</label><input type="text" name="name" required></div>
                <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Password (min 8 characters)</label><input type="password" name="password" minlength="8" required></div>
                <button type="submit" class="btn btn-primary btn-block">Create Admin</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body></html>
