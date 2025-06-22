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
    $guardian2_name = trim(filter_input(INPUT_POST, 'guardian2_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS)); // Optional
    $guardian2_relation = trim(filter_input(INPUT_POST, 'guardian2_relation', FILTER_SANITIZE_FULL_SPECIAL_CHARS)); // Optional
    $guardian2_phone = trim(filter_input(INPUT_POST, 'guardian2_phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS)); // Optional
    $grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_INT);
    $last_school = trim(filter_input(INPUT_POST, 'last_school', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $last_score = filter_input(INPUT_POST, 'last_score', FILTER_VALIDATE_FLOAT);
    $last_grade = filter_input(INPUT_POST, 'last_grade', FILTER_VALIDATE_INT);

    // Handle optional fields (set to null if empty)
    if (empty($religion)) $religion = null;
    if (empty($guardian2_name)) $guardian2_name = null;
    if (empty($guardian2_relation)) $guardian2_relation = null;
    if (empty($guardian2_phone)) $guardian2_phone = null;
    if (empty($last_school)) $last_school = null;


    // 2. Validate required inputs based on all_table.php schema
    if (empty($first_name) || empty($middle_name) || empty($last_name) || empty($date_of_birth) || empty($gender) || // Added middle_name to validation
        empty($nationality) || empty($city) || empty($phone) || empty($emergency_contact) ||
        empty($guardian1_name) || empty($guardian1_relation) || empty($guardian1_phone) ||
        empty($last_school) || // last_school is now mandatory
        $grade === false || $grade === null || // grade is INT, so check for false or null
        $last_score === false || $last_score === null || // last_score is FLOAT, so check for false or null
        $last_grade === false || $last_grade === null // last_grade is INT, so check for false or null
    ) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/create_student.php', 'error', 'Required fields are missing or invalid.');
    }
    // Validate gender against ENUM values
    if (!in_array($gender, ['male', 'female'])) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/create_student.php', 'error', 'Invalid gender selected.');
    }

    // Start a transaction
    $conn->begin_transaction();

    try {
        // --- Create a new user account for the student ---

        // Define the prefix for the username
        $prefix = 'prep';

        // Generate a unique sequential numeric username starting with the prefix
        $next_username_num = 1; // Default starting number
        
        // Prepare a statement to find the highest numeric part of usernames with the given prefix
        $sql_max_username = "SELECT MAX(CAST(SUBSTRING(username, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) AS max_numeric_username 
                             FROM users 
                             WHERE username LIKE ? AND username REGEXP ?";
        $stmt_max_username = $conn->prepare($sql_max_username);
        if (!$stmt_max_username) {
            throw new Exception("Max username query prepare failed: " . $conn->error);
        }
        $like_prefix = $prefix . '%';
        $regexp_prefix = '^' . $prefix . '[0-9]+$';
        $stmt_max_username->bind_param("ss", $like_prefix, $regexp_prefix);
        $stmt_max_username->execute();
        $result_max_username = $stmt_max_username->get_result();
        $row = $result_max_username->fetch_assoc();
        if ($row && $row['max_numeric_username'] !== null) {
            $next_username_num = $row['max_numeric_username'] + 1;
        }
        $stmt_max_username->close();

        // Pad the number with leading zeros and add the prefix
        $username = $prefix . sprintf('%08d', $next_username_num);

        // Optional: Double-check uniqueness (highly unlikely to clash if max+1 is used correctly)
        // This check is more critical if the username generation logic is complex or prone to collisions.
        // For a simple sequential increment, it's mostly for extreme edge cases or concurrent writes.
        // The UNIQUE constraint on the 'username' column in the database will also prevent duplicates.

        // Generate a secure default password
        $default_password = $username; // Set default password to be the same as the username
        $password_hash = password_hash($default_password, PASSWORD_DEFAULT);
        $role = 'student';

        // Insert the new user
        $sql_user = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt_user = $conn->prepare($sql_user);
        if (!$stmt_user) {
            throw new Exception("User SQL Prepare Error: " . $conn->error);
        }
        $stmt_user->bind_param("sss", $username, $password_hash, $role);
        if (!$stmt_user->execute()) {
            throw new Exception("User Creation Error: " . $stmt_user->error);
        }
        $user_id = $conn->insert_id; // Get the ID of the newly created user
        $stmt_user->close();

        // 3. Prepare SQL statement for the 'students' table
        $sql_student = "INSERT INTO students (user_id, first_name, middle_name, last_name, date_of_birth, gender, nationality, religion, city, phone, emergency_contact, guardian1_name, guardian1_relation, guardian1_phone, guardian2_name, guardian2_relation, guardian2_phone, grade, last_school, last_score, last_grade)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_student = $conn->prepare($sql_student);
        if (!$stmt_student) {
            throw new Exception("Student SQL Prepare Error: " . $conn->error);
        }

        // 4. Bind parameters for the 'students' table
        $stmt_student->bind_param("isssssssssssssssisdii", // Corrected: last_school is 's', last_score is 'd'
            $user_id, $first_name, $middle_name, $last_name, $date_of_birth, $gender, $nationality,
            $religion, $city, $phone, $emergency_contact, $guardian1_name, $guardian1_relation,
            $guardian1_phone, $guardian2_name, $guardian2_relation, $guardian2_phone,
            $grade, $last_school, $last_score, $last_grade
        );

        // 5. Execute the student statement
        if (!$stmt_student->execute()) {
            throw new Exception("Student Creation Error: " . $stmt_student->error);
        }

        // Get the ID of the newly inserted student
        $new_student_id = $conn->insert_id;
        $stmt_student->close();

        // If all queries were successful, commit the transaction
        $conn->commit();

        $success_message = "New student created successfully.\n"
                         . "A user account has also been created.\n"
                         . "Username: " . htmlspecialchars($username) . "\n"
                         . "Default Password: " . htmlspecialchars($default_password);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/students.php', 'success', $success_message);

    } catch (Exception $e) {
        // An error occurred, roll back the transaction
        $conn->rollback();
        error_log($e->getMessage());
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/create_student.php', 'error', 'An error occurred. Could not create student: ' . $e->getMessage());
    }

    $conn->close();

} else {
    // Not a POST request, redirect to the form page
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/create_student.php");
}
exit();
?>