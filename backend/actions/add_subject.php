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
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $code = trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (empty($description)) {
        $description = null;
    }

    // 2. Validate required inputs
    if (empty($name) || empty($code)) {
        redirect_with_message('../../frontend/public/add_subject.php', 'error', 'Required fields (Subject Name, Subject Code) are missing.');
    }

    // 3. Prepare SQL statement
    $sql = "INSERT INTO subjects (name, code, description) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // 4. Bind parameters
        $stmt->bind_param("sss", $name, $code, $description);

        // 5. Execute the statement
        if ($stmt->execute()) {
            redirect_with_message('../../frontend/public/subjects.php', 'success', 'New subject added successfully.');
        } else {
            // Check for duplicate entry error (e.g., unique constraint on code)
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                redirect_with_message('../../frontend/public/add_subject.php', 'error', 'Subject code already exists. Please use a unique code.');
            } else {
                error_log("Subject Add Error: " . $stmt->error);
                redirect_with_message('../../frontend/public/add_subject.php', 'error', 'An error occurred while adding the subject.');
            }
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('../../frontend/public/add_subject.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/add_subject.php");
    exit();
}
?>