<?php
session_start();
require_once 'db.php'; // Your PDO DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    if ($email === '' || $password === '') {
        $_SESSION['error'] = 'Email and password are required.';
        header('Location: admin_auth.php');
        exit;
    }

    // Fetch user by email and active status
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = 'Invalid email or password.';
        header('Location: admin_auth.php');
        exit;
    }

    // Update last_login timestamp
    $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Set session variables
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_name'] = $user['name'];
    $_SESSION['admin_role'] = $user['role'];

    // Handle Remember Me securely
    if ($remember_me) {
        // Generate a random token
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);

        // Store token hash in DB with expiry (e.g., 30 days)
        $expiry = date('Y-m-d H:i:s', time() + 60*60*24*30);

        // Create or update remember_token and expiry columns in your table if not exist
        // For demo, assume columns: remember_token VARCHAR(64), remember_expiry DATETIME

        $stmt = $pdo->prepare("UPDATE admin_users SET remember_token = ?, remember_expiry = ? WHERE id = ?");
        $stmt->execute([$token_hash, $expiry, $user['id']]);

        // Set cookie with token (HttpOnly, Secure, SameSite)
        setcookie('admin_remember', $token, time() + 60*60*24*30, '/', '', isset($_SERVER['HTTPS']), true);
    }

    header('Location: admin_dashboard.php');
    exit;
} else {
    header('Location: admin_auth.php');
    exit;
}