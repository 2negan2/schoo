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
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);
    $subject_id = filter_input(INPUT_POST, 'subject_id', FILTER_VALIDATE_INT);
    $section_id = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);

    if (!$teacher_id || !$subject_id || !$section_id) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'error', 'All fields are required.');
    }

    // Validate subject and section compatibility
    $stmt_check_compatibility = $conn->prepare("
        SELECT s.grade_level, s.stream AS subject_stream, sec.grade, sec.stream AS section_stream
        FROM subjects s, sections sec
        WHERE s.id = ? AND sec.id = ?
    ");
    $stmt_check_compatibility->bind_param("ii", $subject_id, $section_id);
    $stmt_check_compatibility->execute();
    $result_compatibility = $stmt_check_compatibility->get_result();
    $compatibility_data = $result_compatibility->fetch_assoc();
    $stmt_check_compatibility->close();

    if (!$compatibility_data || $compatibility_data['grade_level'] !== $compatibility_data['grade'] ||
        ($compatibility_data['subject_stream'] !== null && $compatibility_data['subject_stream'] !== $compatibility_data['section_stream'])) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'error', 'Subject grade/stream does not match section grade/stream.');
    }

    // Insert into teacher_section_assignments
    $sql = "INSERT INTO teacher_section_assignments (teacher_id, section_id, subject_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iii", $teacher_id, $section_id, $subject_id);

        if ($stmt->execute()) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'success', 'Teacher assigned to section successfully.');
        } else {
            if ($conn->errno == 1062) { // Duplicate entry error
                redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'error', 'This teacher is already assigned to this subject in this section.');
            } else {
                error_log("Teacher Assignment Error: " . $stmt->error);
                redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'error', 'An error occurred during assignment: ' . $stmt->error);
            }
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/assign_teacher_section.php");
    exit();
}
?>