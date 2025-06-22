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
$page_title = "Attendance Management - International School Portal";
$header_title = "Attendance Records";

// Fetch attendance records from the database
$attendanceRecords = [];
$sql = "SELECT
            a.id,
            s.first_name,
            s.last_name,
            sub.name AS subject_name,
            a.date,
            a.status,
            u.username AS marked_by_username,
            a.marked_at
        FROM
            attendance a
        LEFT JOIN
            students s ON a.student_id = s.id
        LEFT JOIN
            subjects sub ON a.subject_id = sub.id
        LEFT JOIN
            users u ON a.marked_by = u.id
        ORDER BY
            a.date DESC, a.id DESC"; // Order by date descending, then ID

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching attendance data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $attendanceRecords[] = $row;
    }
}
include_once __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_attendance.php" class="btn"><i class="fas fa-plus"></i> Add New Record</a>
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
                    <th>Date</th>
                    <th>Status</th>
                    <th>Marked By</th>
                    <th>Marked At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendanceRecords)): ?>
                    <?php foreach ($attendanceRecords as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['id']); ?></td>
                            <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name'] ?? 'N/A Student'); ?></td>
                            <td><?php echo htmlspecialchars($record['subject_name'] ?? 'N/A Subject'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($record['date']))); ?></td>
                            <td>
                                <?php
                                    $status = htmlspecialchars($record['status']);
                                    $status_class = ($status === 'present') ? 'status-present' : 'status-absent';
                                ?>
                                <span class="<?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($record['marked_by_username'] ?? 'N/A User'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($record['marked_at']))); ?></td>
                            <td>
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_attendance.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_attendance.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this attendance record?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="8" class="no-data">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>