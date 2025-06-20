<?php
session_start();
require_once __DIR__ . '/../config/connection.php';

$notification_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$notification_id) {
    header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Invalid notification ID."));
    exit();
}

// In a real app, ensure this notification belongs to the logged-in user or user has permission

$stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
// Add "AND user_id = ?" if filtering by user
if ($stmt) {
    $stmt->bind_param("i", $notification_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: ../../frontend/public/notifications.php?success=" . urlencode("Notification deleted successfully."));
        } else {
            header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Notification not found or already deleted."));
        }
    } else {
        header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Error deleting notification: " . $stmt->error));
    }
    $stmt->close();
} else {
    header("Location: ../../frontend/public/notifications.php?error=" . urlencode("Error preparing statement: " . $conn->error));
}
$conn->close();
exit();
?>