<?php
session_start();
require_once 'db.php'; // Your DB connection file

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$role = $_POST['role']; // 'staff', 'manager', 'admin', 'super_admin'
$is_active = isset($_POST['is_active']) ? 1 : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'staff';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Validate required fields
    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: admin_auth.php');
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: admin_auth.php');
        exit;
    }

    // Validate password confirmation
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: admin_auth.php');
        exit;
    }

    // Validate role value (must be one of ENUM options)
    $valid_roles = ['super_admin', 'admin', 'manager', 'staff'];
    if (!in_array($role, $valid_roles, true)) {
        $_SESSION['error'] = 'Invalid role selected.';
        header('Location: admin_auth.php');
        exit;
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'Email already registered.';
        header('Location: admin_auth.php');
        exit;
    }

    // Hash password securely
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin user
    $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$name, $email, $password_hash, $role, $is_active]);

    if ($success) {
        $_SESSION['success'] = 'Registration successful. Please log in.';
        header('Location: admin_auth.php');
        exit;
    } else {
        $_SESSION['error'] = 'Registration failed. Please try again.';
        header('Location: admin_auth.php');
        exit;
    }
} else {
    // Redirect if accessed directly
    header('Location: admin_auth.php');
    exit;
}