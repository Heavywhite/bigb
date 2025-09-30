<?php
include 'connect.php'; // Include the database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the form data safely
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (name, password, email, phone) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "<script>alert('Database error: " . addslashes($conn->error) . "'); window.history.back();</script>";
        exit();
    }
    $stmt->bind_param("ssss", $name, $hashed_password, $email, $phone);

    // Execute the statement
    try {
    $stmt->execute();
    echo "<script>
            alert('Registration successful!');
            window.location.href = 'products.php';
          </script>";
    exit();
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "<script>alert('Email already exists. Please choose a different email.'); window.history.back();</script>";
    } else {
        echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
    exit();
}

    $stmt->close();
    $conn->close();

} else {
    echo "<script>alert('Form not submitted properly.'); window.history.back();</script>";
    exit();
}
