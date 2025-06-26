
<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/students.php';

include_once __DIR__ . '/../includes/header.php';
?>
     
    <div class="container">
        <div class="search-bar">
            <form action="<?php echo BASE_PATH; ?>/frontend/public/students.php" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search by Name, Username, Grade, or Date (YYYY-MM-DD)..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn"><i class="fas fa-search"></i> Search</button>
                <?php if (!empty($search)): ?>
                    <a href="<?php echo BASE_PATH; ?>/frontend/public/students.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/create_student.php" class="btn"><i class="fas fa-user-plus"></i> Add Student</a>
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
                    <th>Current Grade</th>
                    <th>Section</th>
                    <th>Last Grade</th>
                    <th>Last School</th>
                    <th>Last Score</th>
                    <th>D.O.B</th>
                    <th>Gender</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars(trim($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'])); ?></td>
                            <td><?php echo htmlspecialchars($student['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['current_student_grade'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['section_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_grade'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_school'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_score'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($student['date_of_birth']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($student['gender'])); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($student['registered_at']))); ?></td>
                            <td>
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this student and all their related records? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="12" class="no-data">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>