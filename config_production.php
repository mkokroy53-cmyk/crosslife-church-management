<?php
// Production Configuration for Online Deployment
// Disable error display in production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Database Configuration - UPDATE THESE VALUES FOR YOUR HOSTING
define('DB_HOST', 'localhost'); // Usually 'localhost' for shared hosting
define('DB_USER', 'your_db_username'); // Your hosting database username
define('DB_PASS', 'your_db_password'); // Your hosting database password
define('DB_NAME', 'your_db_name'); // Your hosting database name

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database connection error. Please contact administrator.");
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Auto-detect HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify CSRF token for POST requests
function verifyCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            http_response_code(403);
            die('Invalid CSRF token.');
        }
    }
}

// Helper functions for deployment
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}
?></content>
<parameter name="filePath">c:\wamp64\www\church pro\config_production.php