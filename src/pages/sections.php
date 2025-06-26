<?php
// src/pages/sections.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

$page_title = "Section Management - International School Portal";
$header_title = "Section Management";

// Fetch sections from the database
$sections = [];
$sql = "SELECT id, grade, stream, name, capacity FROM sections ORDER BY grade ASC, name ASC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching section data: " . htmlspecialchars($conn->error);
} else {
    $sections = $result->fetch_all(MYSQLI_ASSOC);
}