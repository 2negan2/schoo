<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/assign_student_section.php';

include_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <?php echo $message; ?>

    <form action="<?php echo BASE_PATH; ?>/frontend/public/assign_student_section.php" method="post">
        <div class="form-group">
            <label for="student_id">Select Student:</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- Select an Unassigned Student --</option>
                <?php
                if ($students_result->num_rows > 0) {
                    while($student = $students_result->fetch_assoc()) {
                        echo "<option value='" . $student['id'] . "'>" . htmlspecialchars($student['last_name'] . ", " . $student['first_name'] . " (Grade " . $student['grade'] . ")") . "</option>";
                    }
                } else {
                    echo "<option value=''>No unassigned students found</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="section_id">Select Section:</label>
            <select name="section_id" id="section_id" required>
                <option value="">-- Select a Section --</option>
                <?php
                if ($sections_result->num_rows > 0) {
                    while($section = $sections_result->fetch_assoc()) {
                        $stream = $section['stream'] ? " (" . ucfirst($section['stream']) . ")" : "";
                        echo "<option value='" . $section['id'] . "'>" . htmlspecialchars("Grade " . $section['grade'] . " - Section " . $section['name'] . $stream) . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn">Assign Student</button>
    </form>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>