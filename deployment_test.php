<?php
// Church Management System - Deployment Test Script
// Run this file after uploading to check if everything is working

echo "<h1>🚀 Church Management System - Deployment Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// Test PHP version
echo "<h2>PHP Version Check</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p class='success'>✅ PHP " . PHP_VERSION . " - Compatible</p>";
} else {
    echo "<p class='error'>❌ PHP " . PHP_VERSION . " - Requires PHP 7.4+</p>";
}

// Test required extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = ['mysqli', 'session', 'mbstring', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✅ $ext extension loaded</p>";
    } else {
        echo "<p class='error'>❌ $ext extension missing</p>";
    }
}

// Test file permissions
echo "<h2>File Permissions Check</h2>";
$files_to_check = [
    'config_production.php' => 'Production config file',
    'database.sql' => 'Database schema',
    'index.php' => 'Main entry point',
    'assets/css/style.css' => 'Stylesheet'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "<p class='success'>✅ $description exists (permissions: $perms)</p>";
    } else {
        echo "<p class='error'>❌ $description missing</p>";
    }
}

// Test database connection (if config is set up)
echo "<h2>Database Connection Test</h2>";
if (file_exists('config_production.php')) {
    echo "<p class='warning'>⚠️  Please rename config_production.php to config.php and update database credentials</p>";
    echo "<p>Then refresh this page to test database connection</p>";
} elseif (file_exists('config.php')) {
    include 'config.php';
    if (isset($conn) && $conn->connect_error) {
        echo "<p class='error'>❌ Database connection failed: " . $conn->connect_error . "</p>";
    } elseif (isset($conn)) {
        echo "<p class='success'>✅ Database connection successful</p>";
    } else {
        echo "<p class='error'>❌ Database configuration not found</p>";
    }
} else {
    echo "<p class='error'>❌ No configuration file found</p>";
}

// Test URL rewriting
echo "<h2>URL Rewriting Test</h2>";
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'deployment_test.php') !== false) {
    echo "<p class='success'>✅ URL rewriting appears to be working</p>";
} else {
    echo "<p class='warning'>⚠️  URL rewriting may not be working properly</p>";
}

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Rename <code>config_production.php</code> to <code>config.php</code></li>";
echo "<li>Update database credentials in <code>config.php</code></li>";
echo "<li>Import <code>database.sql</code> into your MySQL database</li>";
echo "<li>Create an admin user in the database</li>";
echo "<li>Test login at <code>staff_login.php</code></li>";
echo "<li>Delete this <code>deployment_test.php</code> file for security</li>";
echo "</ol>";

echo "<p><strong>Repository:</strong> <a href='https://github.com/mkokroy53-cmyk/crosslife-church-management' target='_blank'>GitHub</a></p>";
echo "<p><strong>Deployed on:</strong> " . date('F j, Y \a\t g:i A T') . "</p>";
?></content>
<parameter name="filePath">c:\wamp64\www\church pro\deployment_test.php