<?php
// src/pages/notifications.php

require_once __DIR__ . '/../bootstrap.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(BASE_PATH . '/frontend/public/auth/login.php', 'error', 'You must be logged in to access this page.');
}

// NOTE: In a real application, you would also filter this by the logged-in user's ID.
$notifications = [];
$sql = "SELECT
            n.id,
            n.type,
            n.message,
            n.link,
            n.is_read,
            n.created_at,
            u.username AS user_username
        FROM notifications n
        LEFT JOIN users u ON n.user_id = u.id
        ORDER BY n.created_at DESC, n.id DESC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    $error_message = "Error fetching notification data: " . htmlspecialchars($conn->error);
} else {
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
}

$page_title = "Notifications - International School Portal";
$header_title = "Notifications";