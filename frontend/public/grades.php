<?php
session_start(); // Good practice
require_once __DIR__ . '/../../backend/config/connection.php'; // Path to DB connection

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
$page_title = "Grade Management - International School Portal";
$header_title = "Grade Management";

// Fetch grades from the database
$grades = [];
$sql = "SELECT
            g.id,
            s.first_name,
            s.last_name,
            sub.name AS subject_name,
            g.test,
            g.assignment,
            g.activity,
            g.exercise_book,
            g.midterm,
            g.total,
            g.final_exam,
            u.username AS updated_by_username,
            g.updated_at
        FROM
            grades g
        LEFT JOIN
            students s ON g.student_id = s.id
        LEFT JOIN
            subjects sub ON g.subject_id = sub.id
        LEFT JOIN
            users u ON g.updated_by = u.id
        ORDER BY
            g.updated_at DESC, g.id DESC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching grade data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
}
include_once __DIR__ . '/../includes/header.php'; ?>
    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_grade.php" class="btn"><i class="fas fa-plus-circle"></i> Add New Grade</a>
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
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this grade record?');"><i class="fas fa-trash-alt"></i> Delete</a>
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