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
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    // Get the user who is marking the attendance (assuming user_id in session)
    $marked_by = $_SESSION['user_id'] ?? null;

    // 2. Validate required inputs
    if ($student_id === false || $student_id === null || $subject_id === false || $subject_id === null || empty($date) || !in_array($status, ['present', 'absent'])) {
        redirect_with_message('../../frontend/public/add_attendance.php', 'error', 'All required fields (Student, Subject, Date, Status) must be valid.');
    }

    // Basic date validation (further validation like future dates might be needed)
    if (!strtotime($date)) {
        redirect_with_message('../../frontend/public/add_attendance.php', 'error', 'Invalid date format.');
    }

    // 3. Prepare SQL statement
    $sql = "INSERT INTO attendance (student_id, subject_id, date, status, marked_by) VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // 4. Bind parameters
        // 'iissi' -> student_id(int), subject_id(int), date(string), status(string), marked_by(int)
        $stmt->bind_param("iissi", $student_id, $subject_id, $date, $status, $marked_by);

        // 5. Execute the statement
        if ($stmt->execute()) {
            redirect_with_message('../../frontend/public/attendance.php', 'success', 'New attendance record added successfully.');
        } else {
            // Check for duplicate entry error (e.g., if you have a unique constraint on student_id, subject_id, date)
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                redirect_with_message('../../frontend/public/add_attendance.php', 'error', 'Attendance record for this student, subject, and date already exists.');
            } else {
                error_log("Attendance Add Error: " . $stmt->error);
                redirect_with_message('../../frontend/public/add_attendance.php', 'error', 'An error occurred while adding the attendance record.');
            }
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('../../frontend/public/add_attendance.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/add_attendance.php");
    exit();
}
?>