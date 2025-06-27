<?php
// src/pages/student_portal.php

// Bootstrap the application
require_once __DIR__ . '/../bootstrap.php';

// 1. Authorization Check: Ensure the user is a logged-in student.
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(BASE_PATH . '/frontend/public/auth/login.php', 'error', 'You must be logged in to access this page.');
}

if ($_SESSION['role'] !== 'student') {
    redirect_with_message(BASE_PATH . '/frontend/public/index.php', 'error', 'This portal is for students only.');
}

$user_id = $_SESSION['user_id'];
$student = null;
$grades = [];
$attendance = [];
$gpa = 'N/A';

// 2. Fetch Student Data (Personal Info, Section, etc.)
$stmt_student = $conn->prepare("
    SELECT 
        s.*, 
        u.username,
        sec.name AS section_name,
        sec.stream AS section_stream
    FROM students s
    LEFT JOIN class_assignments ca ON s.id = ca.student_id
    LEFT JOIN sections sec ON ca.section_id = sec.id
    LEFT JOIN users u ON s.user_id = u.id
    WHERE s.user_id = ?
");
$stmt_student->bind_param("i", $user_id);
$stmt_student->execute();
$result_student = $stmt_student->get_result();
if ($result_student->num_rows === 1) {
    $student = $result_student->fetch_assoc();
} else {
    // This can happen if a user with role 'student' doesn't have a corresponding entry in the students table.
    $page_title = "Error - International School Portal";
    $header_title = "Error";
    include_once BASE_PATH . '/frontend/includes/header.php';
    echo "<div class='container'><div class='message error-message'>Error: Student profile not found for your account. Please contact administration.</div></div>";
    include_once BASE_PATH . '/frontend/includes/footer.php';
    exit();
}
$stmt_student->close();

$student_id = $student['id'];

// 3. Fetch Grades Data
$stmt_grades = $conn->prepare("
    SELECT g.total, sub.name AS subject_name
    FROM grades g
    JOIN subjects sub ON g.subject_id = sub.id
    WHERE g.student_id = ?
    ORDER BY sub.name
");
$stmt_grades->bind_param("i", $student_id);
$stmt_grades->execute();
$result_grades = $stmt_grades->get_result();
while ($row = $result_grades->fetch_assoc()) {
    $grades[] = $row;
}
$stmt_grades->close();

// 4. GPA Calculation using helper
$gpa = calculate_gpa($grades);

// 5. Fetch Attendance Data
$stmt_attendance = $conn->prepare("
    SELECT a.date, a.status, sub.name AS subject_name
    FROM attendance a
    JOIN subjects sub ON a.subject_id = sub.id
    WHERE a.student_id = ?
    ORDER BY a.date DESC
    LIMIT 10
");
$stmt_attendance->bind_param("i", $student_id);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();
while ($row = $result_attendance->fetch_assoc()) {
    $attendance[] = $row;
}
$stmt_attendance->close();

// 6. Age Calculation (using new helper function)
$age = calculate_age($student['date_of_birth']);

// 7. ID Card Issue and Expiry Date
$issue_date = new DateTime();
$expiry_date = (new DateTime())->modify('+1 year');

// 8. QR Code Generation
$qr_data = urlencode("Student ID: {$student['id']}\nName: {$student['first_name']} {$student['last_name']}\nGrade: {$student['grade']}");
$qr_code_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl={$qr_data}&choe=UTF-8";

$page_title = "Student Portal - " . htmlspecialchars($student['first_name']);
$header_title = "Welcome, " . htmlspecialchars($student['first_name']) . "!";