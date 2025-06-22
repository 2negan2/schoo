<?php
session_start(); // Good practice, might be used later for user-specific actions
require_once __DIR__ . '/../../backend/config/connection.php'; // Corrected path

require_once __DIR__ . '/../../backend/helpers.php';

// Authorization Check: Ensure a user is logged in AND is an admin
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php',
        'error',
        'You must be logged in to access this page.'
    );
} elseif ($_SESSION['role'] !== 'admin') {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/index.php',
        'error',
        'You do not have permission to access this page. Admin access required.'
    );
}
$page_title = "User Management - International School Portal";
$header_title = "User Management";

// Fetch users from the database
$users = [];
$sql = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
// Note: $conn->close() will be called at the end of connection.php if it's the last script using it,
// or you can explicitly call it here if needed after fetching data.
// For this page, connection.php closes it.
include_once __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_user.php" class="btn"><i class="fas fa-user-plus"></i> Add New User</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($user['created_at']))); ?></td>
                            <td>
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-users">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>