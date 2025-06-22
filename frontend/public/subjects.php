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

// Fetch subjects from the database
$subjects = [];
$sql = "SELECT
            id,
            name,
            grade_level,
            stream
        FROM
            subjects
        ORDER BY
            grade_level ASC, name ASC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching subject data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

$page_title = "Subject Management - International School Portal";
$header_title = "Subject Management";

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_subject.php" class="btn"><i class="fas fa-plus"></i> Add New Subject</a>
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
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_subject.php?id=<?php echo $subject['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this subject?');"><i class="fas fa-trash-alt"></i> Delete</a>
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