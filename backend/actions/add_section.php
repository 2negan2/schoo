<?php
session_start();
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/../helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'error', 'You must be logged in to perform this action.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and retrieve form data
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $grade = filter_input(INPUT_POST, 'grade', FILTER_VALIDATE_INT);
    $stream = trim(filter_input(INPUT_POST, 'stream', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);

    if (empty($stream)) {
        $stream = null;
    }

    // 2. Validate required inputs
    if (empty($name) || $grade === false || $capacity === false) {
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_section.php', 'error', 'Required fields (Name, Grade, Capacity) are missing or invalid.');
    }

    // 3. Prepare SQL statement
    $sql = "INSERT INTO sections (name, grade, stream, capacity) VALUES (?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // 4. Bind parameters
        $stmt->bind_param("sisi", $name, $grade, $stream, $capacity);

        // 5. Execute the statement
        if ($stmt->execute()) {
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/sections.php', 'success', 'New section added successfully.');
        } else {
            error_log("Section Add Error: " . $stmt->error);
            redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_section.php', 'error', 'An error occurred while adding the section.');
        }
        $stmt->close();
    } else {
        error_log("SQL Prepare Error: " . $conn->error);
        redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/add_section.php', 'error', 'A database error occurred.');
    }
    $conn->close();
} else {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/add_section.php");
    exit();
}
?>