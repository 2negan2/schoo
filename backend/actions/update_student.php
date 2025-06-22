<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php'; // For redirect_with_message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $middle_name = trim(filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nationality = trim(filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $religion = trim(filter_input(INPUT_POST, 'religion', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $emergency_contact = trim(filter_input(INPUT_POST, 'emergency_contact', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian1_name = trim(filter_input(INPUT_POST, 'guardian1_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian1_relation = trim(filter_input(INPUT_POST, 'guardian1_relation', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian1_phone = trim(filter_input(INPUT_POST, 'guardian1_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian2_name = trim(filter_input(INPUT_POST, 'guardian2_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian2_relation = trim(filter_input(INPUT_POST, 'guardian2_relation', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $guardian2_phone = trim(filter_input(INPUT_POST, 'guardian2_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_INT); // Made mandatory
    $last_school = trim(filter_input(INPUT_POST, 'last_school', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $last_score = filter_input(INPUT_POST, 'last_score', FILTER_VALIDATE_FLOAT); // Made mandatory
    $last_grade = filter_input(INPUT_POST, 'last_grade', FILTER_VALIDATE_INT); // Made mandatory

    // Handle optional fields
    if (empty($religion)) $religion = null;
    if (empty($guardian2_name)) $guardian2_name = null;
    if (empty($guardian2_relation)) $guardian2_relation = null;
    if (empty($guardian2_phone)) $guardian2_phone = null;
    if (empty($last_school)) $last_school = null;


    // 2. Validate inputs
    if (!$student_id) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/students.php', 'error', 'Invalid student ID.');
    }
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($date_of_birth) || empty($gender) || 
        empty($nationality) || empty($city) || empty($phone) || empty($emergency_contact) || 
        empty($guardian1_name) || empty($guardian1_relation) || empty($guardian1_phone) || 
        empty($last_school) || // last_school is now mandatory
        $grade === false || $grade === null || 
        $last_score === false || $last_score === null || 
        $last_grade === false || $last_grade === null) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/edit_student.php?id=' . $student_id, 'error', 'Required fields are missing.');
    }
    if (!in_array($gender, ['male', 'female'])) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/edit_student.php?id=' . $student_id, 'error', 'Invalid gender selected.');
    }

    // 3. Prepare SQL statement
    $sql = "UPDATE students SET
                first_name = ?, middle_name = ?, last_name = ?, date_of_birth = ?, gender = ?,
                nationality = ?, religion = ?, city = ?, phone = ?, emergency_contact = ?,
                guardian1_name = ?, guardian1_relation = ?, guardian1_phone = ?,
                guardian2_name = ?, guardian2_relation = ?, guardian2_phone = ?,
                grade = ?, last_school = ?, last_score = ?, last_grade = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // 4. Bind parameters
        $stmt->bind_param("ssssssssssssssssisdii", // Corrected: last_school is 's', last_score is 'd'
            $first_name, $middle_name, $last_name, $date_of_birth, $gender,
            $nationality, $religion, $city, $phone, $emergency_contact,
            $guardian1_name, $guardian1_relation, $guardian1_phone,
            $guardian2_name, $guardian2_relation, $guardian2_phone,
            $grade, $last_school, $last_score, $last_grade,
            $student_id
        );

        // 5. Execute
        if ($stmt->execute()) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/students.php', 'success', 'Student record updated successfully.');
        } else {
            error_log("Student Update Error: " . $stmt->error);
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/edit_student.php?id=' . $student_id, 'error', 'Error updating record: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/edit_student.php?id=' . $student_id, 'error', 'Error preparing statement: ' . $conn->error);
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/students.php"); // Redirect if not POST
}
exit();
?>