<?php
// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters from environment or defaults
$servername = getenv('DB_HOST') ?: "db";
$username   = getenv('DB_USER') ?: "root";
$password   = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "rootpassword";
$dbname     = getenv('DB_NAME') ?: "parkingdb";
$port       = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// Connection with retry mechanism for container boot sequence
$max_retries = 5;
$retry_delay = 2; // seconds
$conn = null;

for ($i = 0; $i < $max_retries; $i++) {
    $conn = @new mysqli($servername, $username, $password, $dbname, $port);
    if (!$conn->connect_error) {
        break;
    }
    if ($i < $max_retries - 1) {
        sleep($retry_delay);
    }
}

// Check final connection status
if ($conn && $conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
?>