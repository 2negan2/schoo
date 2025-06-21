<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php'; // Assuming redirect_with_message is here

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $middle_name = trim(filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)); // Added middle_name
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
    $section_id = filter_input(INPUT_POST, 'section_id', FILTER_VALIDATE_INT);

    // Handle optional fields (set to null if empty)
    if (empty($religion)) $religion = null;
    if (empty($guardian2_name)) $guardian2_name = null;
    if (empty($guardian2_relation)) $guardian2_relation = null;
    if (empty($guardian2_phone)) $guardian2_phone = null;

    // 2. Validate required inputs based on all_table.php schema
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($date_of_birth) || empty($gender) || // Added middle_name to validation
        empty($nationality) || empty($city) || empty($phone) || empty($emergency_contact) ||
        empty($guardian1_name) || empty($guardian1_relation) || empty($guardian1_phone) ||
        $section_id === false || $section_id === null) { // section_id is NOT NULL in schema
        redirect_with_message('../../frontend/public/create_student.php', 'error', 'Required fields are missing or invalid.');
    }
    // Validate gender against ENUM values
    if (!in_array($gender, ['male', 'female'])) {
        redirect_with_message('../../frontend/public/create_student.php', 'error', 'Invalid gender selected.');
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Placeholder for user_id (since it's NOT NULL in schema but not from form)
        // In a real application, this would be linked to a user account (e.g., the logged-in admin's ID, or a new user created for the student).
        // For demonstration, we'll use a hardcoded ID. Ensure this user ID exists in your 'users' table.
        $user_id = 1; // IMPORTANT: Adjust this to a valid user_id from your 'users' table or implement proper user linking.

        // 3. Prepare SQL statement for the 'students' table
        $sql_student = "INSERT INTO students (user_id, first_name, middle_name, last_name, date_of_birth, gender, nationality, religion, city, phone, emergency_contact, guardian1_name, guardian1_relation, guardian1_phone, guardian2_name, guardian2_relation, guardian2_phone)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_student = $conn->prepare($sql_student);
        if (!$stmt_student) {
            throw new Exception("Student SQL Prepare Error: " . $conn->error);
        }

        // 4. Bind parameters for the 'students' table
        $stmt_student->bind_param("issssssssssssssss",
            $user_id, $first_name, $middle_name, $last_name, $date_of_birth, $gender, $nationality,
            $religion, $city, $phone, $emergency_contact, $guardian1_name, $guardian1_relation,
            $guardian1_phone, $guardian2_name, $guardian2_relation, $guardian2_phone
        );

        // 5. Execute the student statement
        if (!$stmt_student->execute()) {
            throw new Exception("Student Creation Error: " . $stmt_student->error);
        }

        // Get the ID of the newly inserted student
        $new_student_id = $conn->insert_id;
        $stmt_student->close();

        // 6. Prepare and execute the statement for 'class_assignments' table
        $sql_assignment = "INSERT INTO class_assignments (student_id, section_id) VALUES (?, ?)";
        $stmt_assignment = $conn->prepare($sql_assignment);
        if (!$stmt_assignment) {
            throw new Exception("Assignment SQL Prepare Error: " . $conn->error);
        }

        $stmt_assignment->bind_param("ii", $new_student_id, $section_id);

        if (!$stmt_assignment->execute()) {
            throw new Exception("Class Assignment Error: " . $stmt_assignment->error);
        }
        $stmt_assignment->close();

        // If all queries were successful, commit the transaction
        $conn->commit();

        redirect_with_message('../../frontend/public/students.php', 'success', 'New student created and assigned to section successfully.');

    } catch (Exception $e) {
        // An error occurred, roll back the transaction
        $conn->rollback();
        error_log($e->getMessage());
        redirect_with_message('../../frontend/public/create_student.php', 'error', 'An error occurred. Could not create student.');
    }

    $conn->close();

} else {
    // Not a POST request, redirect to the form page
    header("Location: ../../frontend/public/create_student.php");
}
exit();
?>