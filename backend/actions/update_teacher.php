<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = filter_input(INPUT_POST, 'teacher_id', FILTER_VALIDATE_INT);
    $full_name = trim(filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));
    $qualification = trim(filter_input(INPUT_POST, 'qualification', FILTER_SANITIZE_STRING));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));

    if (!$teacher_id) {
        header("Location: ../../frontend/public/teachers.php?error=" . urlencode("Invalid teacher ID."));
        exit();
    }
    if (empty($full_name) || empty($date_of_birth) || empty($gender) || empty($email)) {
        header("Location: ../../frontend/public/edit_teacher.php?id=" . $teacher_id . "&error=" . urlencode("Required fields are missing."));
        exit();
    }
    if (!$email) {
        header("Location: ../../frontend/public/edit_teacher.php?id=" . $teacher_id . "&error=" . urlencode("Invalid email format."));
        exit();
    }

    $sql = "UPDATE teachers SET 
                full_name = ?, 
                date_of_birth = ?, 
                gender = ?, 
                phone = ?, 
                email = ?, 
                qualification = ?,
                address = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssssi", 
            $full_name, $date_of_birth, $gender, $phone, $email, $qualification, $address, $teacher_id
        );

        if ($stmt->execute()) {
            header("Location: ../../frontend/public/teachers.php?success=" . urlencode("Teacher record updated successfully."));
        } else {
            header("Location: ../../frontend/public/edit_teacher.php?id=" . $teacher_id . "&error=" . urlencode("Error updating record: " . $stmt->error));
        }
        $stmt->close();
    } else {
        header("Location: ../../frontend/public/edit_teacher.php?id=" . $teacher_id . "&error=" . urlencode("Error preparing statement: " . $conn->error));
    }
    $conn->close();
} else {
    header("Location: ../../frontend/public/teachers.php");
}
exit();
?>