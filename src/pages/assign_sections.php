<?php
// src/pages/assign_sections.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Assign Sections - International School Portal";
$header_title = "Assign Students to Sections";
$body_class = "animated-background";
$container_class = "form-container";

// Fetch all students and group by grade
$students = [];
$students_by_grade = [];
$sql_students = "SELECT id, first_name, last_name, last_score, grade FROM students ORDER BY grade, last_score DESC";
$result_students = $conn->query($sql_students);
if ($result_students && $result_students->num_rows > 0) {
    while ($row = $result_students->fetch_assoc()) {
        $students[] = $row;
        $students_by_grade[$row['grade']][] = $row;
    }
} else {
    $error_message = "No students found.";
}

// Fetch all sections and group by grade
$sections = [];
$sections_by_grade = [];
$sql_sections = "SELECT id, name, capacity, grade FROM sections ORDER BY grade ASC, name ASC";
$result_sections = $conn->query($sql_sections);
if ($result_sections && $result_sections->num_rows > 0) {
    while ($row = $result_sections->fetch_assoc()) {
        $sections[] = $row;
        $sections_by_grade[$row['grade']][] = $row;
    }
} else {
    $error_message = ($error_message ?? '') . "<br>No sections found.";
}

// Function to perform section assignment for a single grade level
function assignStudentsToSections($students_of_a_grade, $sections_of_a_grade, $conn) {
    $sectionAssignments = [];
    if (empty($students_of_a_grade) || empty($sections_of_a_grade)) {
        return [];
    }

    $section_ids = array_column($sections_of_a_grade, 'id');
    $sectionAverages = array_fill_keys($section_ids, 0);
    $sectionCounts = array_fill_keys($section_ids, 0);

    $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
    $types = str_repeat('i', count($section_ids));

    $sql = "SELECT section_id, AVG(s.last_score) AS avg_score, COUNT(s.id) as student_count
            FROM class_assignments ca
            JOIN students s ON ca.student_id = s.id
            WHERE ca.section_id IN ($placeholders)
            GROUP BY section_id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$section_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sectionAverages[$row['section_id']] = (float)$row['avg_score'];
            $sectionCounts[$row['section_id']] = (int)$row['student_count'];
        }
    }
    $stmt->close();

    foreach ($students_of_a_grade as $student) {
        $studentId = $student['id'];
        $lowestAvgSectionId = null;
        $lowestAvgScore = PHP_FLOAT_MAX;
        foreach ($sections_of_a_grade as $section) {
            $sectionId = $section['id'];
            if ($sectionCounts[$sectionId] < $section['capacity']) {
                if ($sectionAverages[$sectionId] < $lowestAvgScore) {
                    $lowestAvgSectionId = $sectionId;
                    $lowestAvgScore = $sectionAverages[$sectionId];
                }
            }
        }

        $sectionAssignments[$studentId] = $lowestAvgSectionId;
        if ($lowestAvgSectionId !== null) {
            $sectionAverages[$lowestAvgSectionId] = (($sectionAverages[$lowestAvgSectionId] * $sectionCounts[$lowestAvgSectionId]) + $student['last_score']) / ($sectionCounts[$lowestAvgSectionId] + 1);
            $sectionCounts[$lowestAvgSectionId]++;
        }
    }
    return $sectionAssignments;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sectionAssignments = [];
    $grades_to_assign = array_keys($students_by_grade);

    foreach ($grades_to_assign as $grade) {
        $grade_students = $students_by_grade[$grade] ?? [];
        $grade_sections = $sections_by_grade[$grade] ?? [];
        if (!empty($grade_students) && !empty($grade_sections)) {
            $grade_assignments = assignStudentsToSections($grade_students, $grade_sections, $conn);
            $sectionAssignments = array_merge($sectionAssignments, $grade_assignments);
        }
    }

    $sql_update = "INSERT INTO class_assignments (student_id, section_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE section_id = VALUES(section_id)";
    $stmt_update = $conn->prepare($sql_update);
    foreach ($sectionAssignments as $studentId => $sectionId) {
        if ($sectionId !== null) {
            $stmt_update->bind_param("ii", $studentId, $sectionId);
            $stmt_update->execute();
        }
    }
    $stmt_update->close();
    redirect_with_message(BASE_PATH . '/frontend/public/assign_sections.php', 'success', 'Successfully ran section assignment process.');
}