<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $nationality = trim(filter_input(INPUT_POST, 'nationality', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $religion = trim(filter_input(INPUT_POST, 'religion', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $city = trim(filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $qualification = trim(filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Handle optional fields
    if (empty($religion)) $religion = null;
    if (empty($qualification)) $qualification = null;

    if (!$teacher_id) {
        redirect_with_message('../../frontend/public/teachers.php', 'error', 'Invalid teacher ID.');
    }
    if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($email) || empty($nationality) || empty($city) || empty($phone)) {
        redirect_with_message('../../frontend/public/edit_teacher.php?id=' . $teacher_id, 'error', 'Required fields are missing or invalid.');
    }
    if ($email === false) {
        redirect_with_message('../../frontend/public/edit_teacher.php?id=' . $teacher_id, 'error', 'Invalid email format provided.');
    }
    if (!in_array($gender, ['male', 'female'])) {
        redirect_with_message('../../frontend/public/edit_teacher.php?id=' . $teacher_id, 'error', 'Invalid gender selected.');
    }

    $sql = "UPDATE teachers SET 
                full_name = ?, 
                date_of_birth = ?, 
                gender = ?, 
                nationality = ?,
                religion = ?,
                city = ?,
                phone = ?, 
                email = ?, 
                qualification = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssssi", $full_name, $date_of_birth, $gender, $nationality, $religion, $city, $phone, $email, $qualification, $teacher_id);

        if ($stmt->execute()) {
            redirect_with_message('../../frontend/public/teachers.php', 'success', 'Teacher record updated successfully.');
        } else {
            redirect_with_message('../../frontend/public/edit_teacher.php?id=' . $teacher_id, 'error', 'Error updating record: ' . $stmt->error);
        }
        $stmt->close();
    } else {
        redirect_with_message('../../frontend/public/edit_teacher.php?id=' . $teacher_id, 'error', 'Error preparing statement: ' . $conn->error);
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/teachers.php");
}
exit();