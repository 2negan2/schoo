<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';

// Authorization Check: Ensure a user is logged in
check_auth_and_role('admin');

// This is a simple form page, so no separate logic file is needed.
$page_title = "Add New Teacher - International School Portal";
$header_title = "Add New Teacher";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']); // Clear the message after retrieving it
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

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_teacher.php" method="POST" class="form-grid-container">
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
                <a href="<?php echo BASE_PATH; ?>/frontend/public/teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>