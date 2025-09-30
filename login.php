<?php
session_start(); // Start session for user authentication

// Database configuration
$host = 'localhost';
$dbname = 'bigb_ecommerce';
$username = 'root'; 
$password = '';     


if (!isset($_SESSION['logged_in'])) {
      header("Location: login.php");
      exit();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
$error = '';
// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Handle POST request from form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = sanitize($_POST['input'] ?? ''); // Email or Phone
    $pass = $_POST['password'] ?? '';
    
 // Validation
    if (empty($input) || empty($pass)) {
        $error = "Email/Phone and password are required.";
    } elseif (!filter_var($input, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[1-9]\d{1,14}$/', $input)) {
        $error = "Invalid email or phone format.";
    } else {
        // Connect to database
        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // Prepare query to find user by email OR phone
        $stmt = $conn->prepare("SELECT id, email, phone, password, name FROM users WHERE email = ? OR phone = ?");
        $stmt->bind_param("ss", $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
// Verify password
            if (password_verify($pass, $user['password'])) {
                // Login successful
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['logged_in'] = true;
                // Redirect to dashboard or home page
                header("Location: products.php"); //file for post-login page
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No account found with that email or phone.";
        }
        $stmt->close();
        $conn->close();
    }
}
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $cart_count = $row['total'] ?? 0;
    $stmt->close();
}
$conn->close();