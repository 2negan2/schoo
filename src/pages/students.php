<?php
// src/pages/students.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Student Management - International School Portal";
$header_title = "Student Management";

$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}
 
// Search and filter logic
$search = trim($_GET['search'] ?? '');
$where_clauses = [];
$params = [];
$param_types = '';
 
if (!empty($search)) {
    $search_term = "%{$search}%";
    $where_clauses[] = "(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) LIKE ? OR u.username LIKE ? OR sec.grade LIKE ? OR DATE(s.registered_at) LIKE ?)";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $search_term;
        $param_types .= 's';
    }
}
 
$sql = "SELECT
            s.id, s.first_name, s.middle_name, s.last_name,
            u.username,
            sec.name AS section_name,
            s.grade AS current_student_grade,
            sec.grade AS section_grade_level,
            s.last_school, s.last_score, s.last_grade,
            s.date_of_birth, s.gender, s.registered_at
        FROM students s
        LEFT JOIN users u ON s.user_id = u.id
        LEFT JOIN class_assignments ca ON s.id = ca.student_id
        LEFT JOIN sections sec ON ca.section_id = sec.id";
 
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
 
$sql .= " ORDER BY section_grade_level ASC, sec.name ASC, s.first_name ASC";
 
$students = [];
$error_message = '';
$stmt = $conn->prepare($sql);
 
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . htmlspecialchars($conn->error);
}