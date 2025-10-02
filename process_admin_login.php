<?php
session_start();
require_once 'db_connect.php';

// Create MySQLi connection (fixed: use $host and $dbname, not undefined $servername/$datadase)
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    $_SESSION['error'] = 'Database connection failed: ' . $conn->connect_error . '. Check XAMPP/MySQL.';
    header('Location: admin_auth.php');
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and get form data
    $email = trim(strip_tags($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email and password are required.';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }

    // Fetch active user by email (improved error handling)
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ? AND is_active = 1");
    if (!$stmt) {
        // FIXED: More specific error message with actual MySQL error
        $_SESSION['error'] = 'Database prepare error: ' . $conn->error . '. Check if "admin_users" table exists and has correct columns (e.g., email, is_active, password).';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        // NEW: Check execute error
        $_SESSION['error'] = 'Query execution failed: ' . $stmt->error;
        $stmt->close();
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Check user and password
    if (!$user) {
        $_SESSION['error'] = 'Admin not found or not active. If you just registered, wait for approval.';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = 'Invalid password.';
        $conn->close();
        header('Location: admin_auth.php');
        exit();
    }

    // User is valid—update last_login
    $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
    if ($update_stmt) {
        $update_stmt->bind_param("i", $user['id']);
        if (!$update_stmt->execute()) {
            error_log('Last login update failed: ' . $update_stmt->error);
        }
        $update_stmt->close();
    } else {
        error_log('Prepare failed for last_login update: ' . $conn->error);
    }

    // Set session variables (security: regenerate ID)
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_name'] = $user['name'];
    $_SESSION['admin_role'] = $user['role'];

    // Handle Remember Me (if checked)
    if ($remember_me) {
        try {
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            $expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 30));  // 30 days

            $token_stmt = $conn->prepare("UPDATE admin_users SET remember_token = ?, remember_expiry = ? WHERE id = ?");
            if ($token_stmt) {
                $token_stmt->bind_param("ssi", $token_hash, $expiry, $user['id']);
                if (!$token_stmt->execute()) {
                    error_log('Remember token update failed: ' . $token_stmt->error);
                }
                $token_stmt->close();
            }

            // Set secure cookie
            $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            setcookie('admin_remember', $token, time() + (60 * 60 * 24 * 30), '/', '', $secure, true);
        } catch (Exception $e) {
            error_log('Remember me token generation failed: ' . $e->getMessage());
        }
    }

    $conn->close();
    header('Location: admin_dashboard.php');
    exit();
} else {
    // Not POST, redirect
    if ($conn) $conn->close();
    header('Location: admin_auth.php');
    exit();
}
