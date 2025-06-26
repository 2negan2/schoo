<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/attendance.php';

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="action-bar">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/add_attendance.php" class="btn"><i class="fas fa-plus"></i> Add New Record</a>
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
                                <a href="<?php echo BASE_PATH; ?>/frontend/public/edit_attendance.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="<?php echo BASE_PATH; ?>/backend/actions/delete_attendance.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this attendance record?');"><i class="fas fa-trash-alt"></i> Delete</a>
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