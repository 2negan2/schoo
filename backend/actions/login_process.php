<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $password = $_POST['password']; // Don't sanitize password before verification

    if (empty($username) || empty($password)) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Username and password are required.');
    }

    // Prepare SQL to prevent SQL injection
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Handle SQL prepare error
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'A database error occurred.');
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start the session
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to a dashboard or home page
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/index.php', 'success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
        } else {
            // Invalid password
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Invalid username or password.');
        }
    } else {
        // No user found
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Invalid username or password.');
    }
    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    header('Location: /programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php');
    exit();
}
?>