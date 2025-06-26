<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/manage_schedule.php';

include_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/pages/manage_schedule.css">

<div class="container">
    <h1><?php echo htmlspecialchars($header_title); ?></h1>

    <?php if ($flash_message): ?>
        <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($flash_message['message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="message info-message">
        Displaying schedule for the current week type: <strong>Week <?php echo $week_type_name; ?></strong>. The schedule rotates weekly.
    </div>

    <?php foreach ($sections as $section): ?>
        <?php
            $section_grade = $section['grade'];
            $assigned_shift_name = null;
            $current_shift_periods = null;

            if (in_array($section_grade, $morning_grades)) {
                $assigned_shift_name = 'Morning';
                $current_shift_periods = $shift_timings['Morning']['periods'];
            } elseif (in_array($section_grade, $afternoon_grades)) {
                $assigned_shift_name = 'Afternoon';
                $current_shift_periods = $shift_timings['Afternoon']['periods'];
            }

            if ($assigned_shift_name === null) continue; // Skip sections not part of a defined shift group
        ?>
        <h2>Section: <?php echo htmlspecialchars($section['name'] . ' (Grade ' . $section['grade'] . ' ' . ($section['stream'] ? $section['stream'] : '') . ') - ' . $assigned_shift_name . ' Shift'); ?></h2>
        <table class="schedule-grid">
            <thead>
                <tr>
                    <th>Time / Day</th>
                    <?php foreach ($days_of_week as $day): ?>
                        <th><?php echo htmlspecialchars($day); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($current_shift_periods as $period): ?>
                    <tr>
                        <td class="period-time">
                            <?php echo htmlspecialchars($period['number']); ?><br>
                            <small><?php echo htmlspecialchars($period['start'] . ' - ' . $period['end']); ?></small>
                        </td>
                        <?php foreach ($days_of_week as $day): ?>
                            <?php if ($period['number'] === 'Break'): ?>
                                <td colspan="<?php echo count($days_of_week); ?>" class="break-slot">Break</td>
                                <?php break; // Break out of inner loop for break row ?>
                            <?php else: ?>
                                <td>
                                    <form action="<?php echo BASE_PATH; ?>/backend/actions/update_schedule.php" method="POST">
                                        <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($section['id']); ?>">
                                        <input type="hidden" name="day_of_week" value="<?php echo htmlspecialchars($day); ?>">
                                        <input type="hidden" name="period_number" value="<?php echo htmlspecialchars($period['number']); ?>">
                                        <input type="hidden" name="start_time" value="<?php echo htmlspecialchars($period['start']); ?>">
                                        <input type="hidden" name="end_time" value="<?php echo htmlspecialchars($period['end']); ?>">
                                        <input type="hidden" name="shift" value="<?php echo htmlspecialchars($assigned_shift_name); ?>">
                                        
                                        <div class="schedule-slot" 
                                             data-section-id="<?php echo htmlspecialchars($section['id']); ?>"
                                             data-section-grade="<?php echo htmlspecialchars($section['grade']); ?>"
                                             data-section-stream="<?php echo htmlspecialchars($section['stream'] ?? ''); ?>"
                                             data-day="<?php echo htmlspecialchars($day); ?>"
                                             data-period="<?php echo htmlspecialchars($period['number']); ?>">
                                            <select name="subject_id" class="subject-select" required>
                                                <option value="">-- Subject --</option>
                                            </select>
                                            <select name="teacher_id" class="teacher-select">
                                                <option value="">-- Teacher (Optional) --</option>
                                            </select>
                                            <button type="submit">Save</button>
                                        </div>
                                    </form>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pass PHP data to JavaScript
    const allSubjects = <?php echo json_encode($subjects); ?>;
    const teacherAssignments = <?php echo json_encode($teacher_assignments); ?>;
    const scheduleData = <?php echo json_encode($schedule_data); ?>;

    const scheduleSlots = document.querySelectorAll('.schedule-slot');

    scheduleSlots.forEach(slot => {
        const sectionId = slot.dataset.sectionId;
        const sectionGrade = slot.dataset.sectionGrade;
        const sectionStream = slot.dataset.sectionStream;
        const day = slot.dataset.day;
        const period = slot.dataset.period;

        const subjectSelect = slot.querySelector('.subject-select');
        const teacherSelect = slot.querySelector('.teacher-select');

        // 1. Populate subjects based on section's grade and stream
        // Normalize sectionStream from dataset (which is always a string)
        const normalizedSectionStream = sectionStream === 'null' ? '' : sectionStream;

        allSubjects.forEach(subject => {
            // Normalize subject.stream (from JSON, can be null or string)
            const normalizedSubjectStream = subject.stream === null ? '' : subject.stream;
            if (subject.grade_level == sectionGrade && (normalizedSubjectStream === normalizedSectionStream || normalizedSubjectStream === '' || normalizedSectionStream === '')) {
                const option = new Option(`${subject.name} (G${subject.grade_level})`, subject.id);
                subjectSelect.add(option);
            }
        });

        // 2. Add event listener to subject dropdown
        subjectSelect.addEventListener('change', function() {
            const selectedSubjectId = this.value;
            
            // Clear existing teacher options
            teacherSelect.innerHTML = '<option value="">-- Teacher (Optional) --</option>';

            if (selectedSubjectId && teacherAssignments[sectionId] && teacherAssignments[sectionId][selectedSubjectId]) {
                const assignedTeachers = teacherAssignments[sectionId][selectedSubjectId];
                assignedTeachers.forEach(teacher => {
                    const option = new Option(teacher.name, teacher.id);
                    teacherSelect.add(option);
                });
            }
        });

        // 3. Set initial values based on existing schedule data
        if (scheduleData[sectionId] && scheduleData[sectionId][day] && scheduleData[sectionId][day][period]) {
            const existingSlotData = scheduleData[sectionId][day][period];
            
            // Set subject
            if (existingSlotData.subject_id) {
                subjectSelect.value = existingSlotData.subject_id;
                
                // Manually trigger change event to populate teachers
                subjectSelect.dispatchEvent(new Event('change'));
                
                // Set teacher after teachers are populated
                if (existingSlotData.teacher_id) {
                    // Use a small timeout to allow the teacher dropdown to be populated by the change event
                    setTimeout(() => {
                        teacherSelect.value = existingSlotData.teacher_id;
                    }, 0);
                }
            }
        }
    });
});
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>