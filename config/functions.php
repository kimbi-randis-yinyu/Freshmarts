<?php
/**
 * Shared helper functions. Included by config/db.php callers via bootstrap
 * at the top of every page: require_once __DIR__ . '/config/functions.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/** Sanitize a plain string for safe output / storage */
function clean(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/** Fetch the single site_settings row (cached per-request) */
function get_settings(): array
{
    static $settings = null;
    if ($settings === null) {
        global $pdo;
        $stmt = $pdo->query('SELECT * FROM site_settings LIMIT 1');
        $settings = $stmt->fetch() ?: [];
    }
    return $settings;
}

/** Generate a unique human-friendly order tracking ID, e.g. FM-7F2K9A */
function generate_tracking_id(): string
{
    return 'FM-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
}

/** Is a customer logged in? */
function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

/** Is the logged-in user an admin? */
function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

/** Redirect helper */
function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/** Format a price consistently, e.g. $1.99 */
function fmt_price($amount): string
{
    return '$' . number_format((float) $amount, 2);
}

/** Simple flash-message helper (stored in session, shown once) */
function flash(string $key, string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
