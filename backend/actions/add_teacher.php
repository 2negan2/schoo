<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) { // This check should ideally be in a common auth file
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'You must be logged in to perform this action.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $religion = trim(filter_input(INPUT_POST, 'religion', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $qualification = trim(filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Handle optional fields
    if (empty($religion)) $religion = null;
    if (empty($qualification)) $qualification = null;

    // 2. Validate required inputs
    if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($email) || empty($phone)) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php', 'error', 'Required fields are missing or invalid.');
    }
    if ($email === false) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php', 'error', 'Invalid email format provided.');
    }
    if (!in_array($gender, ['male', 'female'])) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php', 'error', 'Invalid gender selected.');
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // --- Create a new user account for the teacher ---
        $prefix = 'teach'; // Different prefix for teachers
        $next_username_num = 1;
        $sql_max_username = "SELECT MAX(CAST(SUBSTRING(username, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) AS max_numeric_username
                             FROM users
                             WHERE username LIKE ? AND username REGEXP ?";
        $stmt_max_username = $conn->prepare($sql_max_username);
        if (!$stmt_max_username) {
            throw new Exception("Max username query prepare failed: " . $conn->error);
        }
        $like_prefix = $prefix . '%';
        $regexp_prefix = '^' . $prefix . '[0-9]+$';
        $stmt_max_username->bind_param("ss", $like_prefix, $regexp_prefix);
        $stmt_max_username->execute();
        $result_max_username = $stmt_max_username->get_result();
        $row = $result_max_username->fetch_assoc();
        if ($row && $row['max_numeric_username'] !== null) {
            $next_username_num = $row['max_numeric_username'] + 1;
        }
        $stmt_max_username->close();
        $username = $prefix . sprintf('%08d', $next_username_num);

        // Set the default password to be the same as the username
        $default_password = $username;
        $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
        $role = 'teacher';

        $sql_user = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        if (!$stmt_user) {
            throw new Exception("User SQL Prepare Error: " . $conn->error);
        }
        $stmt_user->bind_param("sss", $username, $password_hash, $role);
        if (!$stmt_user->execute()) {
            throw new Exception("User Creation Error: " . $stmt_user->error);
        }
        $user_id = $conn->insert_id;
        $stmt_user->close();

        // 3. Prepare SQL statement for the 'teachers' table
        $sql_teacher = "INSERT INTO teachers (user_id, full_name, date_of_birth, gender, religion, phone, email, qualification) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt_teacher = $conn->prepare($sql_teacher);
        if (!$stmt_teacher) {
            throw new Exception("Teacher SQL Prepare Error: " . $conn->error);
        }

        // 4. Bind parameters
        $stmt_teacher->bind_param("isssssss", $user_id, $full_name, $date_of_birth, $gender, $religion, $phone, $email, $qualification);

        // 5. Execute the statement
        if (!$stmt_teacher->execute()) {
            throw new Exception("Teacher Creation Error: " . $stmt_teacher->error);
        }
        $stmt_teacher->close();

        // Commit the transaction
        $conn->commit();

        // Success
        $success_message = "New teacher added successfully.\n"
                         . "A user account has also been created.\n"
                         . "Username: " . htmlspecialchars($username) . "\n"
                         . "Default Password: " . htmlspecialchars($default_password);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php', 'success', $success_message);

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Teacher Add Error: " . $e->getMessage());
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php', 'error', 'An error occurred while adding the teacher: ' . $e->getMessage());
    }
    $conn->close();

} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php");
    exit();
}