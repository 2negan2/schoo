<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in and is an admin
// Note: You should implement role-based access control. For now, we just check if logged in.
if (!isset($_SESSION['user_id'])) {
    redirect_with_message('../../frontend/public/login.php', 'error', 'You must be logged in to perform this action.');
}
// Example of a role check:
// if ($_SESSION['role'] !== 'admin') {
//     redirect_with_message('../../frontend/public/index.php', 'error', 'You do not have permission to add users.');
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $password = $_POST['password']; // Don't sanitize password before hashing
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // 2. Validate required inputs
    if (empty($username) || empty($password) || empty($role)) {
        redirect_with_message('../../frontend/public/add_user.php', 'error', 'All fields are required.');
    }

    // 3. Check if username already exists
    $sql_check = "SELECT id FROM users WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        redirect_with_message('../../frontend/public/add_user.php', 'error', 'Username already exists. Please choose another.');
    }
    $stmt_check->close();

    // 4. Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 5. Prepare SQL statement
    $sql = "INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $username, $password_hash, $role);

        if ($stmt->execute()) {
            redirect_with_message('../../frontend/public/users.php', 'success', 'New user added successfully.');
        } else {
            error_log("User Add Error: " . $stmt->error);
            redirect_with_message('../../frontend/public/add_user.php', 'error', 'An error occurred while adding the user.');
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('../../frontend/public/add_user.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/add_user.php");
    exit();
}
?>