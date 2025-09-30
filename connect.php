<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "bigb_ecommerce";
$terms = filter_input(INPUT_POST, "terms", FILTER_VALIDATE_BOOL);

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
