<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';

// Authorization Check: Ensure a user is logged in AND is an admin
check_auth_and_role('admin');

// This is a simple form page, so no separate logic file is needed.
$page_title = "Add New User - International School Portal";
$header_title = "Add New User";
$body_class = "animated-background"; // For animated background
$container_class = "form-container"; // For form-specific styling

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="message <?php echo $_SESSION['flash_message']['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php 
                    echo htmlspecialchars($_SESSION['flash_message']['message']); 
                    unset($_SESSION['flash_message']);
                ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_user.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                    <option value="director">Director</option>
                </select>
            </div>

            <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Add User</button>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/users.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>