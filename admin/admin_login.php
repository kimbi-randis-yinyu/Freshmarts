<?php
require_once __DIR__ . '/../config/functions.php';

if (is_admin()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "admin"');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect('index.php');
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Admin Login — FreshMart</title>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body style="background:var(--color-bg-alt);">
<div class="container auth-wrap adminBorder">
    <div class="form-card">
        <h1>Admin Login</h1>
        <p class="sub">FreshMart control panel</p>
        <?php if ($error): ?><p style="color:var(--color-danger); margin-bottom:14px;"><?= clean($error) ?></p><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Email Address</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
        <p class="mt-2" style="font-size:13px; text-align:center;"><a href="../index.php" style="color:var(--color-primary);">← Back to site</a></p>
    </div>
</div>
</body></html>
