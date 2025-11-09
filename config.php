<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'iscss_db');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
function db_connect() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check user role
function check_role($allowed_roles) {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }

    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: unauthorized.php");
        exit();
    }
}

// Sanitize input
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Get current user data
function get_current_user($conn) {
    if (!is_logged_in()) {
        return null;
    }

    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE id = '$user_id' AND is_active = 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

// Format date
function format_date($date) {
    return date('M d, Y h:i A', strtotime($date));
}

// Get unread message count
function get_unread_count($conn, $user_id) {
    $query = "SELECT COUNT(*) as count FROM messages WHERE recipient_id = '$user_id' AND is_read = 0";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    }

    return 0;
}
?>
