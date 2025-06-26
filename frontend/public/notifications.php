<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/notifications.php';

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