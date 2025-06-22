<?php
session_start(); // Good practice
require_once __DIR__ . '/../../backend/config/connection.php'; // Path to DB connection

require_once __DIR__ . '/../../backend/helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php',
        'error',
        'You must be logged in to access this page.'
    );
}

// NOTE: In a real application, you would also filter this by the logged-in user's ID.
// For now, we fetch all to demonstrate the table structure.
$notifications = [];
$sql = "SELECT
            n.id,
            n.type,
            n.message,
            n.link,
            n.is_read,
            n.created_at,
            u.username AS user_username -- Username of the user receiving the notification
        FROM
            notifications n
        LEFT JOIN
            users u ON n.user_id = u.id
        ORDER BY
            n.created_at DESC, n.id DESC"; // Order by newest first

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching notification data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$page_title = "Notifications - International School Portal";
$header_title = "Notifications";

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <!-- Action bar could be added here later for "Mark All Read" -->
        <!-- <div class="action-bar">
            <button class="btn">Mark All as Read</button>
        </div> -->

        <?php if (!empty($_GET['error'])): ?>
            <div class="message error-message"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message) && empty($_GET['error'])): /* For initial load errors */ ?>
            <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['success'])): ?>
            <div class="message success-message"><?php echo htmlspecialchars(urldecode($_GET['success'])); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Recipient (User)</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notification['id']); ?></td>
                            <td><?php echo htmlspecialchars($notification['user_username'] ?? 'N/A User'); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $notification['type']))); ?></td>
                            <td>
                                <?php
                                    $message = htmlspecialchars($notification['message']);
                                    $link = htmlspecialchars($notification['link']);
                                    if (!empty($link)) {
                                        echo "<a href='{$link}'>{$message}</a>";
                                    } else {
                                        echo $message;
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $status_class = $notification['is_read'] ? 'status-read' : 'status-unread';
                                    $status_text = $notification['is_read'] ? 'Read' : 'Unread';
                                ?>
                                <span class="<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($notification['created_at']))); ?></td>
                            <td>
                                <!-- Actions like "Mark Read/Unread", "Delete" -->
                                <?php if ($notification['is_read']): ?>
                                    <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/mark_notification.php?id=<?php echo $notification['id']; ?>&status=unread" class="btn btn-sm"><i class="fas fa-envelope"></i> Mark Unread</a>
                                <?php else: ?>
                                    <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/mark_notification.php?id=<?php echo $notification['id']; ?>&status=read" class="btn btn-sm btn-edit"><i class="fas fa-envelope-open"></i> Mark Read</a>
                                <?php endif; ?>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_notification.php?id=<?php echo $notification['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this notification?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="7" class="no-data">No notifications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>