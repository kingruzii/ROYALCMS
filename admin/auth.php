<?php
/**
 * Authentication Helper
 * Place this in /ROYALCMS/admin/auth.php
 */
session_start();

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_PATH . '/admin/login.php');
        exit();
    }
}

function adminLogout() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    session_destroy();
    header('Location: ' . BASE_PATH . '/admin/login.php');
    exit();
}
?>