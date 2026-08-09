<?php
require_once __DIR__ . '/includes/admin_auth.php';

$editing = isset($_GET['id']);
$user = [
    'id' => 0,
    'name' => '',
    'email' => '',
    'role' => 'customer',
    'avatar' => '',
];

if ($editing) {
    $stmt = $pdo->prepare('SELECT id, name, email, role, avatar FROM users WHERE id = ?');
    $stmt->execute([(int) $_GET['id']]);
    $row = $stmt->fetch();
    if (!$row) {
        flash('admin_error', 'User not found.');
        redirect('users.php');
    }
    $user = $row;
}

$admin_page_title = $editing ? 'Edit User' : 'Add User';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'customer';
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '') {
        flash('admin_error', 'Name and email are required.');
        redirect($editing ? 'user_form.php?id=' . (int) $user['id'] : 'user_form.php');
    }

    // Unique email check
    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $check->execute([$email, (int) $user['id']]);
    if ($check->fetch()) {
        flash('admin_error', 'That email is already in use.');
        redirect($editing ? 'user_form.php?id=' . (int) $user['id'] : 'user_form.php');
    }

    $avatar_path = $user['avatar'] ?? '';

    // Handle avatar upload
    if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed, true)) {
            flash('admin_error', 'Avatar must be JPG, PNG, or WEBP.');
            redirect($editing ? 'user_form.php?id=' . (int) $user['id'] : 'user_form.php');
        }
        if ($_FILES['avatar']['size'] > 4 * 1024 * 1024) {
            flash('admin_error', 'Avatar must be under 4MB.');
            redirect($editing ? 'user_form.php?id=' . (int) $user['id'] : 'user_form.php');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            default      => 'webp',
        };
        $filename = 'avatar_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $destDir = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        $dest = $destDir . $filename;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
            // Delete old avatar if it was uploaded
            if (!empty($avatar_path) && strpos($avatar_path, 'uploads/') === 0) {
                $old = __DIR__ . '/../' . $avatar_path;
                if (is_file($old)) @unlink($old);
            }
            $avatar_path = 'uploads/avatars/' . $filename;
        }
    }

    if ($editing) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, role=?, avatar=?, password=? WHERE id=?');
            $stmt->execute([$name, $email, $role, $avatar_path, $hash, (int) $user['id']]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name=?, email=?, role=?, avatar=? WHERE id=?');
            $stmt->execute([$name, $email, $role, $avatar_path, (int) $user['id']]);
        }
        // Keep session name in sync if editing self
        if ((int) $user['id'] === (int) ($_SESSION['user_id'] ?? 0)) {
            $_SESSION['user_name'] = $name;
            $_SESSION['role'] = $role;
        }
        flash('admin_success', 'User updated successfully.');
    } else {
        if ($password === '') {
            flash('admin_error', 'Password is required for new users.');
            redirect('user_form.php');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, avatar) VALUES (?,?,?,?,?)');
        $stmt->execute([$name, $email, $hash, $role, $avatar_path ?: null]);
        flash('admin_success', 'User created successfully.');
    }
    redirect('users.php');
}

require __DIR__ . '/includes/admin_header.php';
?>

<form method="POST" enctype="multipart/form-data" class="admin-form-grid">
    <div class="admin-card" style="padding:24px;">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= clean($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= clean($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role">
                <option value="customer" <?= ($user['role'] ?? '') === 'customer' ? 'selected' : '' ?>>Customer</option>
                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label><?= $editing ? 'New Password (leave blank to keep current)' : 'Password' ?></label>
            <input type="password" name="password" <?= $editing ? '' : 'required' ?> autocomplete="new-password">
        </div>
    </div>

    <div class="admin-card" style="padding:24px;">
        <div class="form-group">
            <label>Profile Photo</label>
            <?php if (!empty($user['avatar'])): ?>
                <img src="../<?= clean($user['avatar']) ?>" alt="Current avatar"
                     style="width:96px;height:96px;object-fit:cover;border-radius:50%;margin-bottom:12px;border:2px solid var(--color-border);"
                     onerror="this.style.display='none'">
            <?php endif; ?>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp">
            <p style="font-size:12px;color:var(--color-text-muted);margin-top:6px;">
                JPG, PNG, or WEBP. Max 4MB. Leave empty to keep current photo.
            </p>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-2">
            <?= $editing ? 'Update User' : 'Add User' ?>
        </button>
        <a href="users.php" class="btn btn-outline btn-block mt-2">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/admin_footer.php'; ?>
