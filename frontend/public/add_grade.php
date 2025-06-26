<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/add_grade.php';

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

        <form action="<?php echo BASE_PATH; ?>/backend/actions/add_grade.php" method="POST">
            <div class="form-group">
                <label for="student_id">Student:</label>
                <select id="student_id" name="student_id" required>
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo htmlspecialchars($student['id']); ?>">
                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subject_id">Subject:</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?php echo htmlspecialchars($subject['id']); ?>">
                            <?php echo htmlspecialchars($subject['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="test">Test Score (0-100):</label>
                <input type="number" id="test" name="test" min="0" max="100" step="0.01">
            </div>

            <div class="form-group">
                <label for="assignment">Assignment Score (0-100):</label>
                <input type="number" id="assignment" name="assignment" min="0" max="100" step="0.01">
            </div>

            <div class="form-group">
                <label for="activity">Activity Score (0-100):</label>
                <input type="number" id="activity" name="activity" min="0" max="100" step="0.01">
            </div>

            <div class="form-group">
                <label for="exercise_book">Exercise Book Score (0-100):</label>
                <input type="number" id="exercise_book" name="exercise_book" min="0" max="100" step="0.01">
            </div>

            <div class="form-group">
                <label for="midterm">Midterm Score (0-100):</label>
                <input type="number" id="midterm" name="midterm" min="0" max="100" step="0.01">
            </div>

            <div class="form-group">
                <label for="final_exam">Final Exam Score (0-100):</label>
                <input type="number" id="final_exam" name="final_exam" min="0" max="100" step="0.01">
            </div>

            <button type="submit" class="btn"><i class="fas fa-plus-circle"></i> Add Grade</button>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/grades.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>