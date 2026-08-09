<?php
require_once __DIR__ . '/config/functions.php';
$page_title = 'Create Account';

if (is_logged_in()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        $error = 'Please fill all fields with a valid email and a password of at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "customer")');
            $stmt->execute([$name, $email, $hash]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = 'customer';
            redirect('index.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="container auth-wrap">
    <div class="form-card">
        <h1>Create Your Account</h1>
        <p class="sub">Join FreshMart to check out faster and track your orders.</p>

        <?php if ($error): ?><p style="color:var(--color-danger); margin-bottom:14px;"><?= clean($error) ?></p><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
        <p class="mt-2" style="font-size:14px; text-align:center;">
            Already have an account? <a href="login.php" style="color:var(--color-primary); font-weight:700;">Sign in</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
