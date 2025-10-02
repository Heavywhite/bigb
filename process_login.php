<?php
session_start(); 
include_once "connect.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    if (empty($email)) {
        $_SESSION['error'] = "Email is required.";
        header("Location: log_index.php"); 
        $stmt->close();
        $conn->close();
        exit();
    }
    if (empty($password)) {
        $_SESSION['error'] = "Password is required.";
        header("Location: log_index.php");
        $stmt->close();
        $conn->close();
        exit();
    }

    // Prepare and execute query to fetch user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database query failed. Please try again.";
        header("Location: log_index.php");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if user is active
        if ($user['is_active'] == 0) {
            $_SESSION['error'] = 'Your account is not yet approved by the admin. Please wait.';
            header('Location: log_index.php');
            $stmt->close();
            $conn->close();
            exit();
        }

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            unset($_SESSION['error']);
            header("Location: admin_dashboard.php");
            $stmt->close();
            $conn->close();
            exit();
        } else {
            $_SESSION['error'] = "Invalid password or email.";
            header("Location: log_index.php");
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        $_SESSION['error'] = "User  not found. Please register.";
        header("Location: log_index.php");

        $stmt->close();
        $conn->close();
        exit();
    }

    

} else {
    header("Location: log_index.php");
    exit();
}