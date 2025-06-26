<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// This is a simple form page, so no separate logic file is needed.
$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}
 
$page_title = "Add New Subject - International School Portal";
$header_title = "Add New Subject";
$body_class = "animated-background";
$container_class = "form-container";

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if ($flash_message): ?>
            <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($flash_message['message']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_subject.php" method="POST">
            <div class="form-group">
                <label for="name">Subject Name:</label>
                <input type="text" id="name" name="name" placeholder="e.g., Mathematics" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3" placeholder="Brief description of the subject"></textarea>
            </div>

            <div class="form-group">
                <label for="grade_level">Grade Level:</label>
                <input type="number" id="grade_level" name="grade_level" min="1" max="12" placeholder="e.g., 10" required>
            </div>

            <div class="form-group">
                <label for="stream">Stream (Optional):</label>
                <select id="stream" name="stream">
                    <option value="">Select Stream</option>
                    <option value="natural">Natural</option>
                    <option value="social">Social</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Subject</button>
                <a href="<?php echo BASE_PATH; ?>/frontend/public/subjects.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>