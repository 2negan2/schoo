<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';
// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// This is a simple form page, so no separate logic file is needed.
$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) { unset($_SESSION['flash_message']); }
$page_title = "Add New Student - International School Portal";
$header_title = "Add New Student";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if ($flash_message): ?>
            <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($flash_message['message'])); ?>
            </div>
        <?php elseif (!empty($_GET['error'])): // Fallback for old error handling ?>
            <div class="message error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_student.php" method="POST">
            <h2>Personal Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" id="middle_name" name="middle_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nationality">Nationality:</label>
                    <input type="text" id="nationality" name="nationality" required>
                </div>

                <div class="form-group">
                    <label for="religion">Religion (Optional):</label>
                    <input type="text" id="religion" name="religion">
                </div>

                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact Phone:</label>
                    <input type="tel" id="emergency_contact" name="emergency_contact" required>
                </div>
            </div>

            <h2>Guardian Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="guardian1_name">Guardian 1 Name:</label>
                    <input type="text" id="guardian1_name" name="guardian1_name" required>
                </div>

                <div class="form-group">
                    <label for="guardian1_relation">Guardian 1 Relation:</label>
                    <input type="text" id="guardian1_relation" name="guardian1_relation" required>
                </div>

                <div class="form-group">
                    <label for="guardian1_phone">Guardian 1 Phone:</label>
                    <input type="tel" id="guardian1_phone" name="guardian1_phone" required>
                </div>

                <div class="form-group">
                    <label for="guardian2_name">Guardian 2 Name (Optional):</label>
                    <input type="text" id="guardian2_name" name="guardian2_name">
                </div>

                <div class="form-group">
                    <label for="guardian2_relation">Guardian 2 Relation (Optional):</label>
                    <input type="text" id="guardian2_relation" name="guardian2_relation">
                </div>

                <div class="form-group">
                    <label for="guardian2_phone">Guardian 2 Phone (Optional):</label>
                    <input type="tel" id="guardian2_phone" name="guardian2_phone">
                </div>
            </div>

            <h2>Academic History</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="grade">Current Grade:</label>
                    <input type="number" id="grade" name="grade" min="1" max="12" placeholder="e.g., 9" required>
                </div>

                <div class="form-group">
                    <label for="last_school">Last School Attended (Optional):</label>
                    <input type="text" id="last_school" name="last_school">
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

            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Add Student</button>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/students.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>