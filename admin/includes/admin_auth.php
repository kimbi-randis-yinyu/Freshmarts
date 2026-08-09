<?php
/**
 * Include at the very top of every protected admin page:
 *   require_once __DIR__ . '/includes/admin_auth.php';
 * Redirects to admin_login.php if not an authenticated admin.
 */
require_once __DIR__ . '/../../config/functions.php';

if (!is_admin()) {
    redirect('admin_login.php');
}
