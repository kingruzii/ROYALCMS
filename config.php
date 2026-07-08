<?php
/**
 * Royal Village International Foundation Configuration
 */

// Auto-detect environment
$_is_live = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'royalvillageinternational.org') !== false);

// Errors: show locally, hide on live
ini_set('display_errors', $_is_live ? 0 : 1);
ini_set('display_startup_errors', $_is_live ? 0 : 1);
error_reporting($_is_live ? 0 : E_ALL);

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', $_is_live ? 'royaldbv_rvif'      : 'royalcms');
define('DB_USER', $_is_live ? 'royaldbv_rvif_user' : 'root');
define('DB_PASS', $_is_live ? 'dm@%%AF47F}w'       : '');

// Base path: empty on live (domain root), /ROYALCMS on localhost
define('BASE_PATH', $_is_live ? '' : '/ROYALCMS');

// Admin Credentials
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '1234');

// Stripe Credentials (Optional)
define('STRIPE_SECRET_KEY', '');
define('STRIPE_PUBLIC_KEY', '');

// Database connection
function getDBConnection() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (\PDOException $e) {
        $db = DB_NAME;
        die("<div style='font-family:sans-serif;padding:20px;background:#fef2f2;border:1px solid #fee2e2;border-radius:10px;margin:20px;'>
            <h3 style='color:#991b1b;margin-top:0;'>Database Connection Error</h3>
            <p>Could not connect to <strong>$db</strong>.</p>
            <p style='font-size:12px;color:#6b7280;margin-bottom:0;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
        </div>");
    }
}

// Session helpers
function startAdminSession() {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

function checkAdminAuth() {
    startAdminSession();
    if (!isset($_SESSION['rvif_admin']) || $_SESSION['rvif_admin'] !== true) {
        header('Location: ' . BASE_PATH . '/admin/login.php');
        exit;
    }
}
