<?php
// This is the view file for the grade management page.
// It includes the logic file to prepare data, then renders the HTML.
require_once __DIR__ . '/../../src/pages/grades.php';

include_once __DIR__ . '/../includes/header.php';
?>
    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_grade.php" class="btn"><i class="fas fa-plus-circle"></i> Add New Grade</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Test</th>
                    <th>Assignment</th>
                    <th>Activity</th>
                    <th>Ex. Book</th>
                    <th>Midterm</th>
                    <th>Total</th>
                    <th>Final Exam</th>
                    <th>Updated By</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grades)): ?>
                    <?php foreach ($grades as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['id']); ?></td>
                            <td><?php echo htmlspecialchars(($grade['first_name'] ?? '') . ' ' . ($grade['last_name'] ?? 'N/A Student')); ?></td>
                            <td><?php echo htmlspecialchars($grade['subject_name'] ?? 'N/A Subject'); ?></td>
                            <td><?php echo htmlspecialchars($grade['test'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['assignment'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['activity'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['exercise_book'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['midterm'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['total'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['final_exam'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['updated_by_username'] ?? 'N/A User'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($grade['updated_at']))); ?></td>
                            <td>
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this grade record?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="13" class="no-data">No grade records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>