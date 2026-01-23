<?php
// Database configuration
define('DB_HOST', 'db');
define('DB_USER', 'sess');
define('DB_PASS', 'sess');
define('DB_NAME', 'sessionlab');

// Create database connection
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    }
    return $conn;
}

// Start session
session_start();

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email, balance, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
