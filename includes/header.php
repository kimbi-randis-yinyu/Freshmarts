<?php
/**
 * Shared header. Expects (optionally) the including page to set:
 *   $page_title         - string, prepended to site name
 *   $page_description   - string, overrides site meta_description
 *   $page_active         - string, one of home/shop/deals/about/contact, for nav highlighting
 * Requires config/functions.php to already be loaded by the parent page.
 */
$settings   = get_settings();
$site_name  = $settings['site_name'] ?? 'FreshMart';
$page_title = isset($page_title) ? "{$page_title} | {$site_name}" : "{$site_name} — Fresh Groceries Delivered to You";
$page_desc  = $page_description ?? ($settings['meta_description'] ?? '');
$og_image   = $settings['og_image'] ?? 'assets/images/og-image.jpg';
$favicon    = $settings['favicon_path'] ?? 'assets/images/favicon.png';
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= clean($page_title) ?></title>
<meta name="description" content="<?= clean($page_desc) ?>">

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?= clean($favicon) ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= clean($page_title) ?>">
<meta property="og:description" content="<?= clean($page_desc) ?>">
<meta property="og:image" content="<?= clean($og_image) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= clean($current_url) ?>">

<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="topbar">
    <div class="container">
        <span>Enjoy FREE delivery on orders, eat healthy.</span>
        <span>Hotline: <strong><?= clean($settings['hotline'] ?? '') ?></strong></span>
    </div>
</div>

<header class="main-header">
    <div class="nav-wrap container">
        <a href="index.php" class="logo"><?= clean($site_name) ?> <span>.</span></a>

        <nav class="nav-links">
            <a href="index.php#home" class="<?= ($page_active ?? '') === 'home' ? 'active' : '' ?>">Home</a>
            <a href="index.php#shop" class="<?= ($page_active ?? '') === 'shop' ? 'active' : '' ?>">Shop</a>
            <a href="index.php#deals">Deals</a>
            <a href="index.php#about">About Us</a>
            <a href="contact.php" class="<?= ($page_active ?? '') === 'contact' ? 'active' : '' ?>">Contact Us</a>
            <a href="track_order.php" class="<?= ($page_active ?? '') === 'track' ? 'active' : '' ?>">Track Order</a>
        </nav>

        <div class="nav-actions">
            <?php if (is_logged_in()): ?>
                <a href="logout.php" class="nav-icon-btn" title="Logout">👤</a>
            <?php else: ?>
                <a href="login.php" class="nav-icon-btn" title="Sign in">👤</a>
            <?php endif; ?>
            <a href="order.php" class="nav-icon-btn" title="Cart">
                🛒<span class="cart-count">0</span>
            </a>
            <button class="hamburger" aria-label="Menu">☰</button>
        </div>
    </div>
</header>
