<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

$student_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$student_id) {
    header("Location: ../../frontend/public/students.php?error=" . urlencode("Invalid student ID."));
    exit();
}

// Optional: Add role check here to ensure only authorized users can delete
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     header("Location: ../../frontend/public/students.php?error=" . urlencode("You are not authorized to perform this action."));
//     exit();
// }

$conn->begin_transaction();

try {
    // Before deleting a student, consider related data.
    // For example, if students have user accounts, you might want to delete or disassociate them.
    // Or delete related grades, attendance, etc.
    // For now, we'll just delete the student record.
    // Example: Delete related class assignments first
    // $stmt_class_assign = $conn->prepare("DELETE FROM class_assign WHERE student_id = ?");
    // if ($stmt_class_assign) {
    //     $stmt_class_assign->bind_param("i", $student_id);
    //     $stmt_class_assign->execute();
    //     $stmt_class_assign->close();
    // } else {
    //     throw new Exception("Error preparing to delete class assignments: " . $conn->error);
    // }

    // Add similar deletions for grades, attendance etc. if cascade is not set or specific logic is needed

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                header("Location: ../../frontend/public/students.php?success=" . urlencode("Student deleted successfully."));
            } else {
                $conn->rollback();
                header("Location: ../../frontend/public/students.php?error=" . urlencode("Student not found or already deleted."));
            }
        } else {
            throw new Exception("Error deleting student: " . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

} catch (Exception $e) {
    $conn->rollback();
    // Check for foreign key constraint violation (e.g., MySQL error code 1451)
    if ($conn->errno == 1451) {
         header("Location: ../../frontend/public/students.php?error=" . urlencode("Cannot delete student. They have related records (e.g., grades, attendance). Please remove those first."));
    } else {
        header("Location: ../../frontend/public/students.php?error=" . urlencode("Deletion failed: " . $e->getMessage()));
    }
}

$conn->close();
exit();
?>