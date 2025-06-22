<?php
session_start();
require_once __DIR__ . '/../../backend/helpers.php';

// Authorization Check: Ensure a user is logged in
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

$page_title = "Add New Teacher - International School Portal";
$header_title = "Add New Teacher";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

// Check for session-based messages from redirects
$session_message = $_SESSION['message'] ?? null;
if ($session_message) {
    unset($_SESSION['message']); // Clear the message after retrieving it
}

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="message <?php echo $_SESSION['flash_message']['type'] === 'error' ? 'error-message' : 'success-message'; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['flash_message']['message']); 
                    unset($_SESSION['flash_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/add_teacher.php" method="POST" class="form-grid-container">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
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
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="qualification">Qualification (Optional):</label>
                <input type="text" id="qualification" name="qualification" placeholder="e.g., M.Sc. in Physics">
            </div>

            <div class="form-group">
                <label for="religion">Religion (Optional):</label>
                <input type="text" id="religion" name="religion">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Add Teacher</button>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>