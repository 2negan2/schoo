<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Not a POST request, redirect or show error
    header("Location: ../../frontend/public/assign_class.php?error=invalid_method");
    exit();
}

// Get and sanitize input data
$student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
$score = filter_input(INPUT_POST, 'score', FILTER_VALIDATE_FLOAT); // Use FLOAT for score
$last_grade = filter_input(INPUT_POST, 'last_grade', FILTER_SANITIZE_STRING);
$last_school = filter_input(INPUT_POST, 'last_school', FILTER_SANITIZE_STRING);

// Basic validation
if (!$student_id || !$class_id) {
    header("Location: ../../frontend/public/assign_class.php?error=missing_ids");
    exit();
}

// Optional: Get the ID of the user performing the action (if logged in)
// You would typically get this from the session after a successful login
$assigned_by_user_id = $_SESSION['user_id'] ?? null; // Assuming user_id is stored in session

// Start a transaction for atomicity
$conn->begin_transaction();

try {
    // 1. Fetch grades for the selected student and class
    $sql_grades = "SELECT s.grade AS student_grade, c.grade AS class_grade
                   FROM students s, classes c
                   WHERE s.id = ? AND c.id = ?";
    $stmt_grades = $conn->prepare($sql_grades);
    if ($stmt_grades === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt_grades->bind_param("ii", $student_id, $class_id);
    $stmt_grades->execute();
    $result_grades = $stmt_grades->get_result();

    if ($result_grades->num_rows === 0) {
        throw new Exception("Student or Class not found.");
    }

    $grades_data = $result_grades->fetch_assoc();
    $student_grade = $grades_data['student_grade'];
    $class_grade = $grades_data['class_grade'];

    // Check if grades match
    if ($student_grade !== $class_grade) {
        throw new Exception("Cannot assign student to a class of a different grade. Student Grade: " . htmlspecialchars($student_grade) . ", Class Grade: " . htmlspecialchars($class_grade));
    }

    // 2. Update the student's record with score, last_grade, and last_school
    // Use prepared statement to prevent SQL injection
    $sql_update_student = "UPDATE students SET score = ?, last_grade = ?, last_school = ? WHERE id = ?";
    $stmt_update_student = $conn->prepare($sql_update_student);
    if ($stmt_update_student === false) {
        throw new Exception("Prepare update student failed: " . $conn->error);
    }
    // Bind parameters: s = string, d = double (for float), i = integer
    $stmt_update_student->bind_param("dssi", $score, $last_grade, $last_school, $student_id);

    if (!$stmt_update_student->execute()) {
        throw new Exception("Error updating student record: " . $stmt_update_student->error);
    }

    // 3. Insert the assignment record into the class_assign table
    $sql_insert_assign = "INSERT INTO class_assign (student_id, class_id, assigned_by) VALUES (?, ?, ?)";
    $stmt_insert_assign = $conn->prepare($sql_insert_assign);
    if ($stmt_insert_assign === false) {
         throw new Exception("Prepare insert assignment failed: " . $conn->error);
    }
    // Handle nullable assigned_by
    if ($assigned_by_user_id !== null) {
        $stmt_insert_assign->bind_param("iii", $student_id, $class_id, $assigned_by_user_id);
    } else {
        // If assigned_by is null, bind null
        $stmt_insert_assign->bind_param("iiN", $student_id, $class_id, $assigned_by_user_id); // 'N' for NULL
    }

    if (!$stmt_insert_assign->execute()) {
        // Check for duplicate entry error (MySQL error code 1062)
        if ($conn->errno == 1062) {
             throw new Exception("Student is already assigned to this class.");
        } else {
            throw new Exception("Error inserting class assignment: " . $stmt_insert_assign->error);
        }
    }

    // If all queries were successful, commit the transaction
    $conn->commit();

    // Redirect back to the assignment page with a success message
    header("Location: ../../frontend/public/assign_class.php?success=assignment_successful");
    exit();

} catch (Exception $e) {
    // An error occurred, rollback the transaction
    $conn->rollback();
    // Redirect back with an error message
    header("Location: ../../frontend/public/assign_class.php?error=" . urlencode($e->getMessage()));
    exit();
}

// $conn will be closed by connection.php or can be closed manually if needed.
?>