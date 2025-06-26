<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in AND is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php',
        'error',
        'You do not have permission to perform this action.'
    );
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section_id = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);
    $day_of_week = filter_input(INPUT_POST, 'day_of_week', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $period_number = filter_input(INPUT_POST, 'period_number', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT); // Optional
    $start_time = filter_input(INPUT_POST, 'start_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $end_time = filter_input(INPUT_POST, 'end_time', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $shift = filter_input(INPUT_POST, 'shift', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$section_id || !$day_of_week || !$period_number || !$subject_id || empty($start_time) || empty($end_time) || empty($shift)) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/manage_schedule.php', 'error', 'All required schedule fields are missing or invalid.');
    }

    // Set teacher_id to NULL if not provided or invalid
    if ($teacher_id === false || $teacher_id === null) {
        $teacher_id = null;
    }

    // Use INSERT ... ON DUPLICATE KEY UPDATE to either insert a new record or update an existing one
    $sql = "INSERT INTO schedules (section_id, day_of_week, period_number, subject_id, teacher_id, start_time, end_time, shift)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                subject_id = VALUES(subject_id),
                teacher_id = VALUES(teacher_id),
                start_time = VALUES(start_time),
                end_time = VALUES(end_time),
                shift = VALUES(shift)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Corrected bind_param types: isiiisss
        $stmt->bind_param("isiiisss", $section_id, $day_of_week, $period_number, $subject_id, $teacher_id, $start_time, $end_time, $shift);

        if ($stmt->execute()) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/manage_schedule.php', 'success', 'Schedule updated successfully.');
        } else {
            error_log("Schedule Update Error: " . $stmt->error);
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/manage_schedule.php', 'error', 'An error occurred while updating the schedule: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/manage_schedule.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/manage_schedule.php");
    exit();
}
?>