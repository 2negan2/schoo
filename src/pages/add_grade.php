<?php
// src/pages/add_grade.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check
check_auth_and_role('admin');

// Page metadata
$page_title = "Add New Grade - International School Portal";
$header_title = "Add New Grade";
$body_class = "animated-background";
$container_class = "form-container";

// Fetch students for dropdown
$students = [];
$student_result = $conn->query("SELECT id, first_name, last_name FROM students ORDER BY first_name, last_name");
if ($student_result) {
    $students = $student_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch subjects for dropdown
$subjects = [];
$subject_result = $conn->query("SELECT id, name FROM subjects ORDER BY name");
if ($subject_result) {
    $subjects = $subject_result->fetch_all(MYSQLI_ASSOC);
}

$conn->close();