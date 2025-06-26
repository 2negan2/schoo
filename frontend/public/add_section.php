<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';
// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// This is a simple form page, so no separate logic file is needed.
$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']); // Clear the message after retrieving it
}
 
$page_title = "Add New Section - International School Portal";
$header_title = "Add New Section";
$body_class = "animated-background";
$container_class = "form-container";

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if ($flash_message): ?>
            <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php
                    echo htmlspecialchars($flash_message['message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_section.php" method="POST">
            <div class="form-group">
                <label for="name">Section Name:</label>
                <input type="text" id="name" name="name" placeholder="e.g., Section A" required>
            </div>

            <div class="form-group">
                <label for="grade">Grade Level:</label>
                <input type="number" id="grade" name="grade" placeholder="e.g., 10" required>
            </div>

            <div class="form-group">
                <label for="stream">Stream:</label>
                <select id="stream" name="stream">
                    <option value="">Select Stream (if applicable)</option>
                    <option value="general">General</option>
                    <option value="natural">Natural</option>
                    <option value="social">Social</option>
                </select>
            </div>

            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" placeholder="e.g., 30" required>
            </div>

            <button type="submit" class="btn"><i class="fas fa-plus"></i> Add Section</button>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/sections.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>