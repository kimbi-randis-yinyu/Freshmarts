<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Sign In';

if (is_logged_in()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "customer"');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        redirect('index.php');
    } else {
        $error = 'Invalid email or password.';
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container auth-wrap">
    <div class="form-card">
        <h1>Welcome Back</h1>
        <p class="sub">Sign in to track your orders faster.</p>

        <?php if ($error): ?><p style="color:var(--color-danger); margin-bottom:14px;"><?= clean($error) ?></p><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
        </form>
        <p class="mt-2" style="font-size:14px; text-align:center;">
            Don't have an account? <a href="register.php" style="color:var(--color-primary); font-weight:700;">Create one</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
