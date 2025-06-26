<?php
// src/pages/subjects.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(BASE_PATH . '/frontend/public/auth/login.php', 'error', 'You must be logged in to access this page.');
}

$subjects = [];
$sql = "SELECT id, name, grade_level, stream FROM subjects ORDER BY grade_level ASC, name ASC";
$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching subject data: " . htmlspecialchars($conn->error);
} else {
    $subjects = $result->fetch_all(MYSQLI_ASSOC);
}

$page_title = "Subject Management - International School Portal";
$header_title = "Subject Management";