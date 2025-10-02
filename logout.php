<?php
session_start();
require_once 'db_connect.php';  

$conn = null;
if (isset($_SESSION['admin_id'])) {
    $conn = new mysqli($servername, $username, $password, $database);
    if (!$conn->connect_error) {
        mysqli_report(MYSQLI_REPORT_OFF);  // Suppress strict errors, handle manually

        // Clear remember token from DB for this admin
        $adminId = intval($_SESSION['admin_id']);
        $stmt = $conn->prepare("UPDATE admin_users SET remember_token = NULL, remember_expiry = NULL WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $adminId);
            if (!$stmt->execute()) {
                // Log failure but don't block logout (non-critical)
                error_log('Logout token cleanup failed: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            error_log('Logout prepare failed: ' . $conn->error);
        }
        $conn->close();
    } else {
        // Connection failed—log but proceed (logout still works)
        error_log('Logout DB connection failed: ' . $conn->connect_error);
    }
}

// Clear all session data
$_SESSION = [];
session_destroy();

// Clear remember me cookie if set (expire in past, secure flags)
if (isset($_COOKIE['admin_remember'])) {
    $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');  // FIXED: Proper HTTPS check
    setcookie('admin_remember', '', time() - 3600, '/', '', $secure, true);  // HttpOnly=true
}

// Redirect to admin login page (FIXED: Not index.php, to keep admin flow)
header('Location: admin_auth.php');
exit();
?>