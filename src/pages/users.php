<?php
// src/pages/users.php

// Bootstrap the application
require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// Page-specific metadata
$page_title = "User Management - International School Portal";
$header_title = "User Management";

// Data fetching logic
$users = [];
$sql = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}