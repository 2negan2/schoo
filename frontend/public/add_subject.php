<?php
session_start();
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
        'You do not have permission to access this page.'
    );
}

$session_message = $_SESSION['message'] ?? null;
if ($session_message) {
    unset($_SESSION['message']);
}

$page_title = "Add New Subject - International School Portal";
$header_title = "Add New Subject";
$body_class = "animated-background";
$container_class = "form-container";

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if ($session_message): ?>
            <div class="message <?php echo $session_message['type'] === 'error' ? 'error-message' : 'success-message'; ?>">
                <?php echo htmlspecialchars($session_message['text']); ?>
            </div>
        <?php endif; ?>

        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/add_subject.php" method="POST">
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
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/subjects.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>