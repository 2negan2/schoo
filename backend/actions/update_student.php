<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING); // Basic sanitize, validate format if needed
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));
    $section_id = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);
    $score = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    $last_grade = trim(filter_input(INPUT_POST, 'last_grade', FILTER_SANITIZE_STRING));
    $last_school = trim(filter_input(INPUT_POST, 'last_school', FILTER_SANITIZE_STRING));

    // Validate inputs
    if (!$student_id) {
        header("Location: ../../frontend/public/students.php?error=" . urlencode("Invalid student ID."));
        exit();
    }
    if (empty($first_name) || empty($last_name) || empty($date_of_birth) || empty($gender)) {
        header("Location: ../../frontend/public/edit_student.php?id=" . $student_id . "&error=" . urlencode("Required fields are missing."));
        exit();
    }
    if ($section_id === false && $section_id !== null) { // section_id can be null if not selected
         header("Location: ../../frontend/public/edit_student.php?id=" . $student_id . "&error=" . urlencode("Invalid section ID."));
        exit();
    }
     if ($section_id === 0) $section_id = null; // Allow unsetting section

    $sql = "UPDATE students SET 
                first_name = ?, 
                last_name = ?, 
                date_of_birth = ?, 
                gender = ?, 
                phone = ?, 
                address = ?,
                section_id = ?,
                score = ?,
                last_grade = ?,
                last_school = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // sss SSS i d ssi
        $stmt->bind_param("ssssssidsii", 
            $first_name, $last_name, $date_of_birth, $gender, $phone, $address, 
            $section_id, $score, $last_grade, $last_school, $student_id
        );

        if ($stmt->execute()) {
            header("Location: ../../frontend/public/students.php?success=" . urlencode("Student record updated successfully."));
        } else {
            header("Location: ../../frontend/public/edit_student.php?id=" . $student_id . "&error=" . urlencode("Error updating record: " . $stmt->error));
        }
        $stmt->close();
    } else {
        header("Location: ../../frontend/public/edit_student.php?id=" . $student_id . "&error=" . urlencode("Error preparing statement: " . $conn->error));
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/students.php"); // Redirect if not POST
}
exit();
?>