<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/assign_teacher_section.php';

include_once __DIR__ . '/../includes/header.php';
?>

<div class="container <?php echo $container_class; ?>">
    <h1><?php echo htmlspecialchars($header_title); ?></h1>

    <?php if ($flash_message): ?>
        <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($flash_message['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_PATH; ?>/backend/actions/assign_teacher_section_process.php" method="POST">
        <div class="form-group">
            <label for="teacher_id">Select Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <option value="">-- Select Teacher --</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo htmlspecialchars($teacher['id']); ?>">
                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="subject_id">Select Subject:</label>
            <select id="subject_id" name="subject_id" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo htmlspecialchars($subject['id']); ?>"
                            data-grade="<?php echo htmlspecialchars($subject['grade_level']); ?>" 
                            data-stream="<?php echo htmlspecialchars($subject['stream']); ?>"> 
                        <?php echo htmlspecialchars($subject['name'] . ' (Grade ' . $subject['grade_level'] . ' ' . ($subject['stream'] ? '(' . $subject['stream'] . ')' : '') . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="section_id">Select Section:</label>
            <select id="section_id" name="section_id" required>
                <option value="">-- Select Section --</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?php echo htmlspecialchars($section['id']); ?>"
                            data-grade="<?php echo htmlspecialchars($section['grade']); ?>" 
                            data-stream="<?php echo htmlspecialchars($section['stream']); ?>"> 
                        <?php echo htmlspecialchars($section['name'] . ' (Grade ' . $section['grade'] . ' ' . ($section['stream'] ? '(' . $section['stream'] . ')' : '') . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn"><i class="fas fa-link"></i> Assign Teacher</button>
        <a href="<?php echo BASE_PATH; ?>/frontend/public/index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
    </form>
</div>

<script>
    // JavaScript to filter sections based on selected subject's grade and stream
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const sectionSelect = document.getElementById('section_id');
        const initialSectionOptions = Array.from(sectionSelect.options);

        subjectSelect.addEventListener('change', function() {
            const selectedSubjectOption = this.options[this.selectedIndex];
            const subjectGrade = selectedSubjectOption.dataset.grade;
            const subjectStream = selectedSubjectOption.dataset.stream;

            // Clear current section options
            sectionSelect.innerHTML = '<option value="">-- Select Section --</option>'; // Reset to default

            // Add filtered options
            initialSectionOptions.forEach(option => {
                if (option.value === "") return; // Skip the default "Select Section" option
                const sectionGrade = option.dataset.grade;
                const sectionStream = option.dataset.stream;

                if (subjectGrade === sectionGrade && (subjectStream === sectionStream || subjectStream === '' || sectionStream === '')) {
                    sectionSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>