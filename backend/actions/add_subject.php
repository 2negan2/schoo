<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'You must be logged in to perform this action.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $grade_level = filter_input(INPUT_POST, 'grade_level', FILTER_VALIDATE_INT);
    $stream = trim(filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (empty($description)) {
        $description = null;
    }
    if (empty($stream)) {
        $stream = null;
    }

    // 2. Validate required inputs
    if (empty($name) || $grade_level === false || $grade_level === null) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_subject.php', 'error', 'Required fields (Subject Name, Grade Level) are missing or invalid.');
    }

    // Start a transaction for atomicity
    $conn->begin_transaction();

    try {
        // --- Auto-generate Subject Code ---
        // Find the highest numeric code currently in the database
        $next_code_num = 1; // Default starting number
        $sql_max_code = "SELECT MAX(CAST(code AS UNSIGNED)) AS max_numeric_code FROM subjects WHERE code REGEXP '^[0-9]+$'";
        $stmt_max_code = $conn->prepare($sql_max_code);
        if (!$stmt_max_code) {
            throw new Exception("Max code query prepare failed: " . $conn->error);
        }
        $stmt_max_code->execute();
        $result_max_code = $stmt_max_code->get_result();
        $row = $result_max_code->fetch_assoc();
        if ($row && $row['max_numeric_code'] !== null) {
            $next_code_num = $row['max_numeric_code'] + 1;
        }
        $stmt_max_code->close();

        // Format the new code with leading zeros (e.g., 001, 010, 100)
        // Assuming codes will not exceed 999 for a 3-digit format. Adjust sprintf if needed.
        $code = sprintf('%03d', $next_code_num);

        // 3. Prepare SQL statement for inserting the subject
        $sql = "INSERT INTO subjects (name, code, description, grade_level, stream) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Subject SQL Prepare Error: " . $conn->error);
        }

        // 4. Bind parameters
        $stmt->bind_param("sssis", $name, $code, $description, $grade_level, $stream);

        // 5. Execute the statement
        if (!$stmt->execute()) {
            // Check for duplicate entry error (e.g., unique constraint on code)
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                throw new Exception('A subject with the auto-generated code already exists. Please try again.');
            } else {
                throw new Exception("Subject Creation Error: " . $stmt->error);
            }
        }
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect on success AFTER committing
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/subjects.php', 'success', 'New subject added successfully.');

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Subject Add Error: " . $e->getMessage());
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_subject.php', 'error', 'An error occurred: ' . $e->getMessage());
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/add_subject.php");
    exit();
}
?>