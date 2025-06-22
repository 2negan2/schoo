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
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    
    // Use FILTER_VALIDATE_FLOAT with FILTER_NULL_ON_FAILURE for optional numeric fields to get null if invalid/empty
    $test = filter_input(INPUT_POST, 'test', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $assignment = filter_input(INPUT_POST, 'assignment', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $activity = filter_input(INPUT_POST, 'activity', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $exercise_book = filter_input(INPUT_POST, 'exercise_book', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $midterm = filter_input(INPUT_POST, 'midterm', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $final_exam = filter_input(INPUT_POST, 'final_exam', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);

    // Get the user who is marking the grade (assuming user_id in session)
    $updated_by = $_SESSION['user_id'] ?? null;

    // 2. Validate required inputs
    if ($student_id === false || $student_id === null || $subject_id === false || $subject_id === null) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php', 'error', 'Student and Subject are required fields.');
    }

    // Validate scores are within range (0-100) if provided
    $score_fields = ['test', 'assignment', 'activity', 'exercise_book', 'midterm', 'final_exam'];
    foreach ($score_fields as $field) {
        if ($$field !== null && ($$field < 0 || $$field > 100)) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php', 'error', ucfirst($field) . ' score must be between 0 and 100.');
        }
    }

    // Calculate total score (sum of all valid scores)
    $total = array_sum(array_filter([$test, $assignment, $activity, $exercise_book, $midterm, $final_exam], function($value) {
        return $value !== null;
    }));

    // 3. Prepare SQL statement
    $sql = "INSERT INTO grades (student_id, subject_id, test, assignment, activity, exercise_book, midterm, total, final_exam, updated_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // 4. Bind parameters
        // 'iiiddddddi' -> student_id(int), subject_id(int), test(double), assignment(double), activity(double), exercise_book(double), midterm(double), total(double), final_exam(double), updated_by(int)
        $stmt->bind_param("iiiddddddi", 
            $student_id, 
            $subject_id, 
            $test, 
            $assignment, 
            $activity, 
            $exercise_book, 
            $midterm, 
            $total, 
            $final_exam, 
            $updated_by
        );

        // 5. Execute the statement
        if ($stmt->execute()) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/grades.php', 'success', 'New grade added successfully.');
        } else {
            error_log("Grade Add Error: " . $stmt->error);
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php', 'error', 'An error occurred while adding the grade.');
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php");
    exit();
}
?>