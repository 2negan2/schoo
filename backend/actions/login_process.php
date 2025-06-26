<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// 1. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not, redirect to login page. This prevents direct access to the script.
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Invalid request method.');
}

// 2. Sanitize and retrieve form data
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Username and password are required.');
}

// 3. Fetch user from the database using a prepared statement
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
if ($stmt === false) {
    // Handle SQL error
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'An internal error occurred. Please try again later.');
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 4. Verify user and password using password_verify()
if ($user && password_verify($password, $user['password'])) {
    // Password is correct. Login successful.

    // 5. Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);

    // 6. Store essential user data in the session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // 7. Redirect user based on their role
    if ($user['role'] === 'student') {
        header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/student_portal.php");
    } else {
        header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/index.php");
    }
    exit();
} else {
    // 8. Handle failed login attempt with a generic error message
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'Invalid username or password.');
}

$conn->close();