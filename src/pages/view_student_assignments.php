<?php
// src/pages/view_student_assignments.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// Fetch assigned students with their section details
$query = "
    SELECT 
        s.first_name, s.last_name, s.grade,
        sec.name AS section_name,
        sec.stream,
        ca.assigned_at
    FROM class_assignments ca
    JOIN students s ON ca.student_id = s.id
    JOIN sections sec ON ca.section_id = sec.id
    ORDER BY s.grade, sec.name, s.last_name, s.first_name;
";

$result = $conn->query($query);

$page_title = "Student Section Assignments";
$header_title = "Student Section Assignments";