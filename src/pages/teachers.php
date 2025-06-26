<?php
// src/pages/teachers.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}

$teachers = [];
$sql = "SELECT
            t.id, t.full_name, u.username, t.date_of_birth,
            t.gender, t.phone, t.email, t.qualification
        FROM teachers t
        LEFT JOIN users u ON t.user_id = u.id
        ORDER BY t.id ASC";

$page_title = "Teacher Management - International School Portal";
$header_title = "Teacher Management";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching teacher data: " . htmlspecialchars($conn->error);
} else {
    $teachers = $result->fetch_all(MYSQLI_ASSOC);
}