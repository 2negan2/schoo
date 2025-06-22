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
// Check for session-based messages from redirects
$session_message = $_SESSION['message'] ?? null;
if ($session_message) {
    unset($_SESSION['message']); // Clear the message after retrieving it
}

// Fetch teachers from the database
$teachers = [];
$sql = "SELECT
            t.id,
            t.full_name,
            u.username,
            t.date_of_birth,
            t.gender,
            t.phone,
            t.email,
            t.qualification
        FROM
            teachers t
        LEFT JOIN
            users u ON t.user_id = u.id
        ORDER BY
            t.id ASC";


$page_title = "Teacher Management - International School Portal";
$header_title = "Teacher Management";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching teacher data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}
// $conn will be closed by connection.php or can be closed manually if needed.

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_teacher.php" class="btn"><i class="fas fa-user-plus"></i> Add New Teacher</a>
        </div>

        <?php if ($session_message): ?>
            <div class="message <?php echo $session_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($session_message['text'])); // Use nl2br to respect line breaks ?>
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
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_teacher.php?id=<?php echo htmlspecialchars($teacher['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this teacher? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
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