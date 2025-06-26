<?php
// src/pages/assign_teacher_section.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check
check_auth_and_role('admin');

// Page metadata
$page_title = "Assign Teacher to Section - International School Portal";
$header_title = "Assign Teacher to Section";
$body_class = "animated-background";
$container_class = "form-container";

$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}

// Fetch all teachers
$teachers = [];
$stmt_teachers = $conn->prepare("SELECT id, full_name FROM teachers ORDER BY full_name");
$stmt_teachers->execute();
$teachers = $stmt_teachers->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_teachers->close();

// Fetch all subjects
$subjects = [];
$stmt_subjects = $conn->prepare("SELECT id, name, grade_level, IFNULL(stream, '') AS stream FROM subjects ORDER BY name");
$stmt_subjects->execute();
$subjects = $stmt_subjects->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_subjects->close();

// Fetch all sections
$sections = [];
$stmt_sections = $conn->prepare("SELECT id, name, grade, IFNULL(stream, '') AS stream FROM sections ORDER BY grade, name");
$stmt_sections->execute();
$sections = $stmt_sections->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_sections->close();

$conn->close();