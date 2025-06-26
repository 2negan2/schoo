<?php
// src/pages/manage_schedule.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Manage Class Schedule - International School Portal";
$header_title = "Manage Class Schedule";
$body_class = "animated-background";
$container_class = "form-container";

$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}

// --- Schedule Rotation Logic ---
$current_week_number = (int)date('W');
$is_odd_week = ($current_week_number % 2 !== 0);
$week_type_name = $is_odd_week ? "A (Odd Weeks)" : "B (Even Weeks)";

$grade_groups = [
    'Group1' => [9, 11],
    'Group2' => [10, 12]
];

$days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$shift_timings = [
    'Morning' => [
        'periods' => [
            ['number' => 1, 'start' => '02:00', 'end' => '02:40'],
            ['number' => 2, 'start' => '02:40', 'end' => '03:20'],
            ['number' => 3, 'start' => '03:20', 'end' => '04:00'],
            ['number' => 'Break', 'start' => '04:00', 'end' => '04:15'],
            ['number' => 4, 'start' => '04:15', 'end' => '04:55'],
            ['number' => 5, 'start' => '04:55', 'end' => '05:35'],
            ['number' => 6, 'start' => '05:35', 'end' => '06:15']
        ]
    ],
    'Afternoon' => [
        'periods' => [
            ['number' => 1, 'start' => '07:00', 'end' => '07:40'],
            ['number' => 2, 'start' => '07:40', 'end' => '08:20'],
            ['number' => 3, 'start' => '08:20', 'end' => '09:00'],
            ['number' => 'Break', 'start' => '09:00', 'end' => '09:15'],
            ['number' => 4, 'start' => '09:15', 'end' => '09:55'],
            ['number' => 5, 'start' => '09:55', 'end' => '10:35'],
            ['number' => 6, 'start' => '10:35', 'end' => '11:15']
        ]
    ]
];

$morning_grades = $is_odd_week ? $grade_groups['Group1'] : $grade_groups['Group2'];
$afternoon_grades = $is_odd_week ? $grade_groups['Group2'] : $grade_groups['Group1'];

// Fetch all necessary data
$sections = $conn->query("SELECT id, name, grade, stream FROM sections ORDER BY grade, name")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT id, name, grade_level, IFNULL(stream, '') AS stream FROM subjects ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$teachers = $conn->query("SELECT id, full_name FROM teachers ORDER BY full_name")->fetch_all(MYSQLI_ASSOC);

$teacher_assignments_result = $conn->query("
    SELECT tsa.section_id, tsa.subject_id, tsa.teacher_id, t.full_name
    FROM teacher_section_assignments tsa
    JOIN teachers t ON tsa.teacher_id = t.id 
    WHERE tsa.teacher_id IS NOT NULL
");
$teacher_assignments = [];
while ($row = $teacher_assignments_result->fetch_assoc()) {
    $teacher_assignments[$row['section_id']][$row['subject_id']][] = ['id' => $row['teacher_id'], 'name' => $row['full_name']];
}

$schedule_data_result = $conn->query("
    SELECT 
        sch.section_id, sch.day_of_week, sch.period_number,
        sch.subject_id, sch.teacher_id,
        sub.name AS subject_name, t.full_name AS teacher_name
    FROM schedules sch
    LEFT JOIN subjects sub ON sch.subject_id = sub.id
    LEFT JOIN teachers t ON sch.teacher_id = t.id
");
$schedule_data = [];
while ($row = $schedule_data_result->fetch_assoc()) {
    $schedule_data[$row['section_id']][$row['day_of_week']][$row['period_number']] = [
        'subject_id' => $row['subject_id'],
        'teacher_id' => $row['teacher_id'],
        'subject_name' => $row['subject_name'],
        'teacher_name' => $row['teacher_name']
    ];
}

$conn->close();