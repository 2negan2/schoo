<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
    $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
    $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));
    
    // Handle optional fields that can be empty or null
    $section_id = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);
    if ($section_id === 0 || $section_id === false) {
        $section_id = null;
    }
    
    $score = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    
    $last_grade = trim(filter_input(INPUT_POST, 'last_grade', FILTER_SANITIZE_STRING));
    if (empty($last_grade)) $last_grade = null;

    $last_school = trim(filter_input(INPUT_POST, 'last_school', FILTER_SANITIZE_STRING));
    if (empty($last_school)) $last_school = null;

    // 2. Validate required inputs
    if (empty($first_name) || empty($last_name) || empty($date_of_birth) || empty($gender)) {
        // Redirect back to the form with an error message
        header("Location: ../../frontend/public/add_student.php?error=" . urlencode("Required fields (First Name, Last Name, D.O.B, Gender) are missing."));
        exit();
    }

    // 3. Prepare SQL statement
    // Note: user_id is not included here as it's a separate process to create and link a user account.
    $sql = "INSERT INTO students (first_name, last_name, date_of_birth, gender, phone, address, section_id, score, last_grade, last_school) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // 4. Bind parameters
        // s = string, i = integer, d = double
        $stmt->bind_param("ssssssidss", 
            $first_name, 
            $last_name, 
            $date_of_birth, 
            $gender, 
            $phone, 
            $address, 
            $section_id, 
            $score, 
            $last_grade, 
            $last_school

        // 5. Execute the statement
        if ($stmt->execute()) {
            // Success: redirect to the students list page with a success message
            header("Location: ../../frontend/public/students.php?success=" . urlencode("New student added successfully."));
        } else {
            // Failure: redirect back to the form with a database error
            header("Location: ../../frontend/public/add_student.php?error=" . urlencode("Error creating record: " . $stmt->error));
        }
        $stmt->close();
    } else {
        // SQL preparation failed
        header("Location: ../../frontend/public/add_student.php?error=" . urlencode("Error preparing statement: " . $conn->error));
    }
    $conn->close();

} else {
    // Not a POST request, redirect to the form page or student list
    header("Location: ../../frontend/public/add_student.php");
}
exit();
?>