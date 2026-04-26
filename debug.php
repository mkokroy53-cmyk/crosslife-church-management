<?php
require_once 'config.php';

echo "<h1>Database Connection Test</h1>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Users table exists</p>";

    // Check if admin user exists
    $stmt = $conn->prepare("SELECT id, username, full_name, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $admin_username = 'admin');
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        echo "<p style='color: green;'>✓ Admin user exists:</p>";
        echo "<ul>";
        echo "<li>ID: " . $user['id'] . "</li>";
        echo "<li>Username: " . $user['username'] . "</li>";
        echo "<li>Full Name: " . $user['full_name'] . "</li>";
        echo "<li>Role: " . $user['role'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
        echo "<p>You may need to run the database setup SQL file.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Users table does not exist</p>";
    echo "<p>You need to run the database.sql or complete_database_fix.sql file first.</p>";
}

// Test password verification
$test_password = 'admin123';
$stored_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($test_password, $stored_hash)) {
    echo "<p style='color: green;'>✓ Password hash verification works</p>";
} else {
    echo "<p style='color: red;'>✗ Password hash verification failed</p>";
}

echo "<hr>";
echo "<h2>Database Setup Instructions:</h2>";
echo "<ol>";
echo "<li>Open phpMyAdmin or MySQL command line</li>";
echo "<li>Create database: <code>CREATE DATABASE church_management;</code></li>";
echo "<li>Import the database.sql or complete_database_fix.sql file</li>";
echo "<li>Try logging in again with username: admin, password: admin123</li>";
echo "</ol>";
?>