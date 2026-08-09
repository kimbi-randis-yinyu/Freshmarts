<?php
/**
 * Admin layout header. Include admin_auth.php BEFORE this file.
 * Expects optional $admin_page_title.
 */
$admin_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($admin_page_title ?? 'Dashboard') ?> — FreshMart Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrap">
    <button class="hamburger-btn" aria-label="Toggle menu" id="hamburgerBtn" type="button">☰</button>
    <div class="admin-overlay" id="adminOverlay"></div>
    <aside class="admin-sidebar" id="sidebar">
        <div class="admin-logo">FreshMart <span>Admin</span></div>
        <nav>
            <a href="index.php" class="<?= $admin_page === 'index.php' ? 'active' : '' ?>">📊 Dashboard</a>
            <a href="products.php" class="<?= in_array($admin_page, ['products.php','product_form.php']) ? 'active' : '' ?>">🛒 Products</a>
            <a href="categories.php" class="<?= $admin_page === 'categories.php' ? 'active' : '' ?>">🗂 Categories</a>
            <a href="orders.php" class="<?= in_array($admin_page, ['orders.php','order_detail.php']) ? 'active' : '' ?>">📦 Orders</a>
            <a href="users.php" class="<?= in_array($admin_page, ['users.php','user_form.php']) ? 'active' : '' ?>">👥 Users</a>
            <a href="contact_messages.php" class="<?= in_array($admin_page, ['contact_messages.php','view_message.php']) ? 'active' : '' ?>">✉️ Messages</a>
            <a href="settings.php" class="<?= $admin_page === 'settings.php' ? 'active' : '' ?>">⚙️ Site Settings</a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="../index.php">← View Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-topbar">
            <h1><?= clean($admin_page_title ?? 'Dashboard') ?></h1>
            <div class="admin-user">👤 <?= clean($_SESSION['user_name'] ?? 'Admin') ?></div>
        </header>
        <div class="admin-content">
        <?php if ($msg = flash('admin_success')): ?><div class="alert alert-success"><?= clean($msg) ?></div><?php endif; ?>
        <?php if ($msg = flash('admin_error')): ?><div class="alert alert-error"><?= clean($msg) ?></div><?php endif; ?>
