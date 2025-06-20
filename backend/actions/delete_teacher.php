<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

$teacher_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$teacher_id) {
    header("Location: ../../frontend/public/teachers.php?error=" . urlencode("Invalid teacher ID."));
    exit();
}

// Optional: Add role check here

$conn->begin_transaction();
try {
    // Consider related data: e.g., if teachers are assigned to classes or subjects, handle those relationships.
    // For now, direct delete.
    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $teacher_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $conn->commit();
                header("Location: ../../frontend/public/teachers.php?success=" . urlencode("Teacher deleted successfully."));
            } else {
                $conn->rollback();
                header("Location: ../../frontend/public/teachers.php?error=" . urlencode("Teacher not found or already deleted."));
            }
        } else {
            throw new Exception("Error deleting teacher: " . $stmt->error);
        }
        $stmt->close();
    } else {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
} catch (Exception $e) {
    $conn->rollback();
     if ($conn->errno == 1451) { // Foreign key constraint
         header("Location: ../../frontend/public/teachers.php?error=" . urlencode("Cannot delete teacher. They may be assigned to classes or subjects."));
    } else {
        header("Location: ../../frontend/public/teachers.php?error=" . urlencode("Deletion failed: " . $e->getMessage()));
    }
}

$conn->close();
exit();
?>