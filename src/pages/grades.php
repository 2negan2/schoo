<?php
// src/pages/grades.php

// Bootstrap the application
require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// Page-specific metadata
$page_title = "Grade Management - International School Portal";
$header_title = "Grade Management";

// Data fetching logic
$grades = [];
$sql = "SELECT
            g.id,
            s.first_name,
            s.last_name,
            sub.name AS subject_name,
            g.test,
            g.assignment,
            g.activity,
            g.exercise_book,
            g.midterm,
            g.total,
            g.final_exam,
            u.username AS updated_by_username,
            g.updated_at
        FROM
            grades g
        LEFT JOIN
            students s ON g.student_id = s.id
        LEFT JOIN
            subjects sub ON g.subject_id = sub.id
        LEFT JOIN
            users u ON g.updated_by = u.id
        ORDER BY
            g.updated_at DESC, g.id DESC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching grade data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
}