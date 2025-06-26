<?php
// src/pages/attendance.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Attendance Management - International School Portal";
$header_title = "Attendance Records";

// Fetch attendance records from the database
$attendanceRecords = [];
$sql = "SELECT
            a.id,
            s.first_name,
            s.last_name,
            sub.name AS subject_name,
            a.date,
            a.status,
            u.username AS marked_by_username,
            a.marked_at
        FROM attendance a
        LEFT JOIN students s ON a.student_id = s.id
        LEFT JOIN subjects sub ON a.subject_id = sub.id
        LEFT JOIN users u ON a.marked_by = u.id
        ORDER BY a.date DESC, a.id DESC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching attendance data: " . htmlspecialchars($conn->error);
} else {
    $attendanceRecords = $result->fetch_all(MYSQLI_ASSOC);
}