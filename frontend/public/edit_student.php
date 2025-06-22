<?php
session_start();
require_once __DIR__ . '/../../backend/config/connection.php';

$student_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$student = null;
$error_message = $_GET['error'] ?? '';
$success_message = $_GET['success'] ?? '';

$page_title = "Edit Student - International School Portal";
$header_title = "Edit Student";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

if (!$student_id) {
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/students.php?error=Invalid student ID.");
    exit();
}

// Fetch student details
$stmt = $conn->prepare("SELECT s.*, u.username FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/students.php?error=Student not found.");
        exit();
    }
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}

if ($student === null && empty($error_message)) {
    // Fallback if student somehow wasn't loaded and no DB error was caught
    header("Location: /programing/schoo-main/schoo-main/schoo/frontend/public/students.php?error=Could not load student data.");
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

        <?php if ($student): ?>
        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/update_student.php" method="POST">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">

            <div class="form-group">
                <label>Username (Linked Account):</label>
                <input type="text" value="<?php echo htmlspecialchars($student['username'] ?? 'N/A'); ?>" readonly disabled>
                <small>User account is linked via user_id and cannot be changed here.</small>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php echo ($student['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($student['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="nationality">Nationality:</label>
                <input type="text" id="nationality" name="nationality" value="<?php echo htmlspecialchars($student['nationality']); ?>" required>
            </div>

            <div class="form-group">
                <label for="religion">Religion (Optional):</label>
                <input type="text" id="religion" name="religion" value="<?php echo htmlspecialchars($student['religion'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($student['city']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="emergency_contact">Emergency Contact Phone:</label>
                <input type="tel" id="emergency_contact" name="emergency_contact" value="<?php echo htmlspecialchars($student['emergency_contact']); ?>" required>
            </div>

            <div class="form-group">
                <label for="guardian1_name">Guardian 1 Name:</label>
                <input type="text" id="guardian1_name" name="guardian1_name" value="<?php echo htmlspecialchars($student['guardian1_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="guardian1_relation">Guardian 1 Relation:</label>
                <input type="text" id="guardian1_relation" name="guardian1_relation" value="<?php echo htmlspecialchars($student['guardian1_relation']); ?>" required>
            </div>

            <div class="form-group">
                <label for="guardian1_phone">Guardian 1 Phone:</label>
                <input type="tel" id="guardian1_phone" name="guardian1_phone" value="<?php echo htmlspecialchars($student['guardian1_phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="guardian2_name">Guardian 2 Name (Optional):</label>
                <input type="text" id="guardian2_name" name="guardian2_name" value="<?php echo htmlspecialchars($student['guardian2_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="guardian2_relation">Guardian 2 Relation (Optional):</label>
                <input type="text" id="guardian2_relation" name="guardian2_relation" value="<?php echo htmlspecialchars($student['guardian2_relation'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="guardian2_phone">Guardian 2 Phone (Optional):</label>
                <input type="tel" id="guardian2_phone" name="guardian2_phone" value="<?php echo htmlspecialchars($student['guardian2_phone'] ?? ''); ?>">
            </div>

            <h2>Academic History</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="grade">Current Grade:</label>
                    <input type="number" id="grade" name="grade" min="1" max="12" placeholder="e.g., 9" required>
                </div>

                <div class="form-group">
                    <label for="last_school">Last School Attended:</label>
                    <input type="text" id="last_school" name="last_school" required>
                </div>

                <div class="form-group">
                    <label for="last_score">Last Score:</label>
                    <input type="number" step="0.01" id="last_score" name="last_score" placeholder="e.g., 85.5" required>
                </div>

                <div class="form-group">
                    <label for="last_grade">Last Grade Completed (8-11):</label>
                    <input type="number" id="last_grade" name="last_grade" min="8" max="11" required>
                </div>
            </div>

            <button type="submit" class="btn"><i class="fas fa-save"></i> Update Student</button>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/students.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
        <?php else: ?>
            <p>Student data could not be loaded.</p>
        <?php endif; ?>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>o 