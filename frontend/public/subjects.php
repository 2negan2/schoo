// Use the new bootstrap file for common includes
<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/subjects.php';

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_subject.php" class="btn"><i class="fas fa-plus"></i> Add New Subject</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>Stream</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['id']); ?></td>
                            <td><?php echo htmlspecialchars($subject['name']); ?></td>
                            <td><?php echo htmlspecialchars($subject['grade_level']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($subject['stream'] ?? 'N/A')); ?></td>
                            <td>
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this subject?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="5" class="no-data">No subjects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>