<?php
// Database configuration for cPanel
define('DB_SERVER', 'localhost');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
define('DB_NAME', '');

// Error handling - ENABLE FOR DEBUGGING
error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors during development
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');

// Create database connection
try {
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
    
    // Set charset
    mysqli_set_charset($conn, "utf8mb4");
} catch (Exception $e) {
    // Log and display error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    echo "<div style='color: red; background: #f8d7da; padding: 15px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
    echo "<strong>Database Connection Error:</strong> " . $e->getMessage();
    echo "<br>Please check your database credentials and ensure the database exists.";
    echo "</div>";
}

// Start session
session_start();
?> 
