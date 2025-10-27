<?php
// Set the default timezone
date_default_timezone_set('Asia/Kolkata'); // Use your local timezone

// Database credentials (adjust as necessary)
$host = 'localhost';
$db   = 'task_management_db';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Die with an error message if connection fails
     die("Database connection failed: " . $e->getMessage());
}

// Function to check if the user is logged in
function check_auth($required_role = null) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
    if ($required_role && $_SESSION['user_role'] !== $required_role) {
        header('Location: ' . ($_SESSION['user_role'] === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
        exit;
    }
}
?>