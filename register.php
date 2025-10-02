<?php
include 'connect.php';
session_start();

// Disable strict exceptions to prevent fatal errors on duplicates
mysqli_report(MYSQLI_REPORT_OFF);  // Turn off exceptions; use error checking instead

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    $_SESSION['error'] = "Connection failed: " . $conn->connect_error;
    header("Location: log_index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data safely
    $name = trim(strip_tags($_POST['name'] ?? ''));
    $email = trim(strip_tags($_POST['email'] ?? ''));
    $phone = trim(strip_tags($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'All fields are required.';
        header("Location: log_index.php");
        exit();
    }

    // Validate email and phone formats
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header("Location: log_index.php");
        exit();
    }
    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
        $_SESSION['error'] = 'Invalid phone format (e.g., +256xxxxxxxxx).';
        header("Location: log_index.php");
        exit();
    }

    // Check if passwords match and length
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: log_index.php");
        exit();
    }
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long.';
        header("Location: log_index.php");
        exit();
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (name, password, email, phone) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        $_SESSION['error'] = 'Database prepare error: ' . $conn->error;
        header("Location: log_index.php");
        exit();
    }
    $stmt->bind_param("ssss", $name, $password, $email, $phone);

    // Execute the statement and handle errors without exceptions
    if ($stmt->execute()) {
        // Auto-login after successful registration
        $user_id = $conn->insert_id;
        session_regenerate_id(true);
        $_SESSION['id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;

        // Redirect to products page on success
        header("Location: products.php");
        exit();
    } else {
        // Handle insertion errors (e.g., duplicate email/phone)
        $error_msg = $stmt->error;  // Use $stmt->error for more precise error
        if (strpos($error_msg, 'Duplicate entry') !== false) {
            $_SESSION['error'] = 'Email or phone already exists. Please choose a different one and try again.';
        } else {
            $_SESSION['error'] = 'Registration failed due to a database error. Please try again.';
            
        }
        header("Location: log_index.php");
        $stmt->close();
        $conn->close();
        exit();
    }

} else {
    // If not POST, redirect to index
    header("Location: log_index.php");
    exit();
}
