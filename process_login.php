<?php
session_start(); 
include_once "connect.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? ''); 
    $password = trim($_POST['password'] ?? '');

    if (empty($email)) {
        $_SESSION['error'] = "Email is required.";
        header("Location: log_index.php"); 
        exit();
    }
    if (empty($password)) {
        $_SESSION['error'] = "Password is required.";
        header("Location: log_index.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database query failed. Please try again.";
        header("Location: log_index.php");
        exit();
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            unset($_SESSION['error']);
            header("Location: products.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password or email.";
            header("Location: log_index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User  not found. Please register.";
        header("Location: log_index.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: log_index.php");
    exit();
}