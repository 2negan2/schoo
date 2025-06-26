<?php
// src/pages/edit_student.php

// Bootstrap the application
require_once __DIR__ . '/../bootstrap.php';

$student_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$student = null;
$error_message = $_GET['error'] ?? '';
$success_message = $_GET['success'] ?? '';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Edit Student - International School Portal";
$header_title = "Edit Student";
$body_class = "animated-background";
$container_class = "form-container";

if (!$student_id) {
    redirect_with_message(BASE_PATH . "/frontend/public/students.php", 'error', 'Invalid student ID.');
}

// Fetch student details
$stmt = $conn->prepare("SELECT s.*, u.username FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        redirect_with_message(BASE_PATH . "/frontend/public/students.php", 'error', 'Student not found.');
    }
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}

if ($student === null && empty($error_message)) {
    // Fallback if student somehow wasn't loaded and no DB error was caught 
    redirect_with_message(BASE_PATH . "/frontend/public/students.php", 'error', 'Could not load student data.');
}