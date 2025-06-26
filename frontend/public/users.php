<?php
// This is the view file for the user management page.
// It includes the logic file to prepare data, then renders the HTML.
require_once __DIR__ . '/../../src/pages/users.php';

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_user.php" class="btn"><i class="fas fa-user-plus"></i> Add New User</a>
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
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash-alt"></i> Delete</a>
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