// Use the new bootstrap file for common includes
<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/sections.php';

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_section.php" class="btn"><i class="fas fa-plus"></i> Add New Section</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Grade</th>
                    <th>Stream</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sections)): ?>
                    <?php foreach ($sections as $section): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($section['id']); ?></td>
                            <td><?php echo htmlspecialchars($section['name']); ?></td>
                            <td><?php echo htmlspecialchars($section['grade']); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($section['stream'] ?? 'N/A')); ?></td>
                            <td><?php echo htmlspecialchars($section['capacity']); ?></td>
                            <td>
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this section?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="6" class="no-data">No sections found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>