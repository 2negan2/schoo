<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

$notification_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$mark_as = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING); // 'read' or 'unread'

if (!$notification_id || !in_array($mark_as, ['read', 'unread'])) {
    header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Invalid request."));
    exit();
}

// In a real app, ensure this notification belongs to the logged-in user or user has permission
// $user_id = $_SESSION['user_id'] ?? null;
// if (!$user_id) { /* ... handle unauthorized ... */ }

$new_status = ($mark_as === 'read') ? 1 : 0;

$stmt = $conn->prepare("UPDATE notifications SET is_read = ? WHERE id = ?");
// Add "AND user_id = ?" if filtering by user
if ($stmt) {
    $stmt->bind_param("ii", $new_status, $notification_id);
    // Add $user_id to bind_param if user filtering is active: $stmt->bind_param("iii", $new_status, $notification_id, $user_id);

    if ($stmt->execute()) {
        header("Location: ../../frontend/public/notifications.php?success=" . urlencode("Notification status updated."));
    } else {
        header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Error updating status: " . $stmt->error));
    }
    $stmt->close();
} else {
    header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Error preparing statement: " . $conn->error));
}
$conn->close();
exit();
?>