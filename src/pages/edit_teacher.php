<?php
// src/pages/edit_teacher.php

require_once __DIR__ . '/../bootstrap.php';

$teacher_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$teacher = null;
$error_message = $_GET['error'] ?? '';
$success_message = $_GET['success'] ?? '';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Edit Teacher - International School Portal";
$header_title = "Edit Teacher";
$body_class = "animated-background";
$container_class = "form-container";

if (!$teacher_id) {
    redirect_with_message(BASE_PATH . "/frontend/public/teachers.php", 'error', "Invalid teacher ID.");
}

// Fetch teacher details
$stmt = $conn->prepare("SELECT t.*, u.username FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?");
if ($stmt) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();
    } else {
        redirect_with_message(BASE_PATH . "/frontend/public/teachers.php", 'error', "Teacher not found.");
    }
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}