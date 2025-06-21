<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message('../../frontend/public/login.php', 'error', 'You must be logged in to perform this action.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $qualification = trim(filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // 2. Validate required inputs
    if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($email)) {
        redirect_with_message('../../frontend/public/add_teacher.php', 'error', 'Required fields (Full Name, D.O.B, Gender, Email) are missing.');
    }
    if ($email === false) {
        redirect_with_message('../../frontend/public/add_teacher.php', 'error', 'Invalid email format provided.');
    }

    // 3. Prepare SQL statement
    $sql = "INSERT INTO teachers (full_name, date_of_birth, gender, phone, email, qualification, address) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // 4. Bind parameters
        $stmt->bind_param("sssssss", $full_name, $date_of_birth, $gender, $phone, $email, $qualification, $address);

        // 5. Execute the statement
        if ($stmt->execute()) {
            // Success
            redirect_with_message('../../frontend/public/teachers.php', 'success', 'New teacher added successfully.');
        } else {
            // Failure
            error_log("Teacher Add Error: " . $stmt->error);
            redirect_with_message('../../frontend/public/add_teacher.php', 'error', 'An error occurred while adding the teacher.');
        }
        $stmt->close();
    } else {
        // SQL preparation failed
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('../../frontend/public/add_teacher.php', 'error', 'A database error occurred.');
    }
    $conn->close();

} else {
    // Not a POST request
    header("Location: ../../frontend/public/add_teacher.php");
    exit();
}
?>