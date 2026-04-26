<?php
// Database Configuration - UPDATE WITH YOUR INFINITYFREE DETAILS
define('DB_HOST', 'sql208.infinityfree.com');  // Change to your actual host
define('DB_USER', 'if0_41289286');
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');  // Get from MySQL Databases in cPanel
define('DB_NAME', 'if0_41289286_church_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed. Please contact administrator.");
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
