<?php
// src/pages/assign_student_section.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id']) && isset($_POST['section_id'])) {
    $student_id = $_POST['student_id'];
    $section_id = $_POST['section_id'];

    // Check if student is already assigned
    $check_stmt = $conn->prepare("SELECT id FROM class_assignments WHERE student_id = ?");
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $message = "<p class='message error'>Error: This student is already assigned to a section.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO class_assignments (student_id, section_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $section_id);

        if ($stmt->execute()) {
            $message = "<p class='message success'>Student assigned to section successfully.</p>";
        } else {
            $message = "<p class='message error'>Error: " . htmlspecialchars($stmt->error) . "</p>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}

// Fetch unassigned students and sections for the form
$students_result = $conn->query("SELECT id, first_name, last_name, grade FROM students WHERE id NOT IN (SELECT student_id FROM class_assignments) ORDER BY grade, last_name, first_name");
$sections_result = $conn->query("SELECT id, name, grade, stream FROM sections ORDER BY grade, name");

$page_title = "Assign Student to Section";
$header_title = "Assign Student to Section";