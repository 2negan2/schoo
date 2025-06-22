<?php
session_start();
require_once __DIR__ . '/../../backend/config/connection.php';

require_once __DIR__ . '/../../backend/helpers.php';

// Authorization Check: Ensure a user is logged in
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php',
        'error',
        'You must be logged in to access this page.'
    );
}

// Fetch students for dropdown
$students = [];
$student_result = $conn->query("SELECT id, first_name, last_name FROM students ORDER BY first_name, last_name");
if ($student_result) {
    while ($row = $student_result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch subjects for dropdown
$subjects = [];
$subject_result = $conn->query("SELECT id, name FROM subjects ORDER BY name");
if ($subject_result) {
    while ($row = $subject_result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

$conn->close(); // Close connection after fetching data

$page_title = "Add New Grade - International School Portal";
$header_title = "Add New Grade";
$body_class = "animated-background";
$container_class = "form-container";

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

        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/add_grade.php" method="POST">
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
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/grades.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </form>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>