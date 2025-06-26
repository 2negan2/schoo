<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/edit_teacher.php';

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
        <form action="<?php echo BASE_PATH; ?>/backend/actions/update_teacher.php" method="POST">
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
            <a href="<?php echo BASE_PATH; ?>/frontend/public/teachers.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
        <?php else: ?>
            <p>Teacher data could not be loaded.</p>
        <?php endif; ?>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>