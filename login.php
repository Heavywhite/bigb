<?php
session_start(); // Start session for user authentication
include 'connect.php';

// Error reporting (for development; disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to sanitize input (for email/phone)
function sanitize($data) {
    return trim(strip_tags($data));
}

// Handle POST request from form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = sanitize($_POST['input'] ?? ''); // Email or Phone
    $pass = $_POST['password'] ?? '';

    // Validation
    if (empty($input) || empty($pass)) {
        $_SESSION['error'] = "Email/Phone and password are required.";
        header("Location: log_index.php");
        exit();
    } elseif (
        !filter_var($input, FILTER_VALIDATE_EMAIL) &&
        !preg_match('/^\+?[1-9]\d{1,14}$/', $input)
    ) {
        $_SESSION['error'] = "Invalid email or phone format.";
        header("Location: log_index.php");
        exit();
    } else {
        // Connect to database
        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            $_SESSION['error'] = "Connection failed: " . $conn->connect_error;
            header("Location: log_index.php");
            exit();
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
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);

                // Set session variables
                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name'];

                // Redirect to dashboard or home page
                header("Location: products.php");
                $stmt->close();
                $conn->close();
                exit();
            } else {
                $_SESSION['error'] = "Invalid password.";
                $stmt->close();
                $conn->close();
                header("Location: log_index.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "No account found with that email or phone. Please register if you don't have an account.";
            $stmt->close();
            $conn->close();
            header("Location: log_index.php");
            exit();
        }
    }
} else {
    // If not POST, redirect to index
    header("Location: log_index.php");
    exit();
}
?>