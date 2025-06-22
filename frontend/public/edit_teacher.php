<?php
session_start();
require_once __DIR__ . '/../../backend/config/connection.php';

$teacher_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$teacher = null;
$error_message = $_GET['error'] ?? '';
$success_message = $_GET['success'] ?? '';

$page_title = "Edit Teacher - International School Portal";
$header_title = "Edit Teacher";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

if (!$teacher_id) {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php?error=Invalid teacher ID.");
    exit();
}

// Fetch teacher details
$stmt = $conn->prepare("SELECT t.*, u.username FROM teachers t LEFT JOIN users u ON t.user_id = u.id WHERE t.id = ?");
if ($stmt) {
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $teacher = $result->fetch_assoc();
    } else {
        header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php?error=Teacher not found.");
        exit();
    }
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}

if ($teacher === null && empty($error_message)) {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php?error=Could not load teacher data.");
    exit();
}

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo htmlspecialchars(urldecode($error_message)); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo htmlspecialchars(urldecode($success_message)); ?></div>
        <?php endif; ?>

        <?php if ($teacher): ?>
        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/update_teacher.php" method="POST">
            <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher['id']); ?>">

            <div class="form-group">
                <label>Username (Linked Account):</label>
                <input type="text" value="<?php echo htmlspecialchars($teacher['username'] ?? 'N/A'); ?>" readonly disabled>
                <small>User account linking is managed separately.</small>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($teacher['date_of_birth']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php echo ($teacher['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($teacher['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="qualification">Qualification (Optional):</label>
                <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="religion">Religion (Optional):</label>
                <input type="text" id="religion" name="religion" value="<?php echo htmlspecialchars($teacher['religion'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn"><i class="fas fa-save"></i> Update Teacher</button>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
        <?php else: ?>
            <p>Teacher data could not be loaded.</p>
        <?php endif; ?>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>