<?php
session_start();
require_once 'db_connect.php';

// Create MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    $_SESSION['error'] = 'Database connection failed. Please try again.';
    header('Location: admin_auth.php');
    exit();
}

// Disable strict errors to handle manually
mysqli_report(MYSQLI_REPORT_OFF);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form data
    $name = trim(strip_tags($_POST['name'] ?? ''));
    $email = trim(strip_tags($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = trim(strip_tags($_POST['role'] ?? 'staff'));
    $is_active = 0;

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: admin_auth.php');
        exit();
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: admin_auth.php');
        exit();
    }

    // Password match and length check (min 6 chars)
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: admin_auth.php');
        exit();
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long.';
        header('Location: admin_auth.php');
        exit();
    }

    // Role validation 
    $allowed_roles = ['staff', 'manager', 'admin', 'super_admin'];
    if (!in_array($role, $allowed_roles)) {
        $_SESSION['error'] = 'Invalid role selected.';
        header('Location: admin_auth.php');
        exit();
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM admin_users WHERE email = ?");
    if (!$check_stmt) {
        $_SESSION['error'] = 'Database prepare error.';
        header('Location: admin_auth.php');
        exit();
    }
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'Email already registered.';
        $check_stmt->close();
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }
    $check_stmt->close();

    // Hash password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new admin user (inactive, with role)
    $insert_stmt = $conn->prepare("INSERT INTO admin_users (name, email, password, role, is_active) VALUES (?, ?, ?, ?, ?)");
    if (!$insert_stmt) {
        $_SESSION['error'] = 'Database prepare error.';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }
    $insert_stmt->bind_param("ssssi", $name, $email, $password, $role, $is_active);

    if ($insert_stmt->execute()) {
        $_SESSION['success'] = 'Registration successful. Please wait for admin approval before logging in.';
    } else {
        // Handle errors
        $error_msg = $insert_stmt->error;
        if (strpos($error_msg, 'Duplicate entry') !== false) {
            $_SESSION['error'] = 'Email already exists. Please choose a different one.';
        } else {
            $_SESSION['error'] = 'Registration failed. Please try again.';
        }
    }

    $insert_stmt->close();
    $conn->close();
    header('Location: admin_auth.php');
    exit();
} else {
    // Not POST, redirect
    header('Location: admin_auth.php');
    exit();
}
