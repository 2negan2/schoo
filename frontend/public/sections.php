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
$page_title = "Section Management - International School Portal";
$header_title = "Section Management";

// Fetch sections from the database
$sections = [];
$sql = "SELECT
            id,
            grade,
            stream,
            name,
            capacity
        FROM
            sections
        ORDER BY
            grade ASC, name ASC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching section data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }
}
// $conn will be closed by connection.php or can be closed manually if needed.
include_once __DIR__ . '/../includes/header.php'; ?>

    <div class="container">
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/add_section.php" class="btn"><i class="fas fa-plus"></i> Add New Section</a>
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
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_section.php?id=<?php echo $section['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this section?');"><i class="fas fa-trash-alt"></i> Delete</a>
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