<?php
session_start();
require_once 'connect.php'; // Your PDO DB connection

// If user is logged in, clear remember token in DB
if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];

    // Remove remember token from DB
    $stmt = $pdo->prepare("UPDATE admin_users SET remember_token = NULL, remember_expiry = NULL WHERE id = ?");
    $stmt->execute([$adminId]);
}

// Clear all session data
$_SESSION = [];
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['admin_remember'])) {
    setcookie('admin_remember', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
}

// Redirect to login page
header('Location: admin_auth.php');
exit;