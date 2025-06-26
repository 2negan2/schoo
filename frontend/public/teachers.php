// Use the new bootstrap file for common includes
<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/teachers.php';

include_once __DIR__ . '/../includes/header.php';
?>
    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_teacher.php" class="btn"><i class="fas fa-user-plus"></i> Add New Teacher</a>
        </div>

        <?php if ($flash_message): ?>
            <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($flash_message['message'])); // Use nl2br to respect line breaks ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>D.O.B</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Qualification</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teachers)): ?>
                    <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($teacher['date_of_birth']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($teacher['gender'])); ?></td>
                            <td><?php echo htmlspecialchars($teacher['phone']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($teacher['qualification'] ?? 'N/A'); ?></td>
                            <td>
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="9" class="no-data">No teachers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>