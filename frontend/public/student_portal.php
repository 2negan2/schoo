<?php
// This is the view file for the student portal.
// It includes the logic file to prepare data, then renders the HTML.
require_once __DIR__ . '/../../src/pages/student_portal.php';

include_once __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/pages/student_portal.css">

<div class="container portal-container">
    <!-- ID Card Column -->
    <div class="id-card-container">
        <div class="id-card">
            <div class="id-card-header">
                <h3>International School</h3>
                <p>Student ID Card</p>
            </div>
            <div class="id-card-body">
                <div class="id-card-photo">
                    <img src="https://via.placeholder.com/100" alt="Student Photo" class="student-photo">
                    <img src="<?php echo $qr_code_url; ?>" alt="QR Code" class="qr-code">
                </div>
                <div class="id-card-details">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars(trim($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'])); ?></p>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($student['username'] ?? 'N/A'); ?></p>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
                    <p><strong>Age:</strong> <?php echo $age; ?> | <strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($student['gender'])); ?></p>
                    <p><strong>Grade:</strong> <?php echo htmlspecialchars($student['grade']); ?> | <strong>Section:</strong> <?php echo htmlspecialchars($student['section_name'] ?? 'N/A'); ?></p>
                    <p><strong>Guardian:</strong> <?php echo htmlspecialchars($student['guardian1_name']); ?></p>
                    <p><strong>Emergency:</strong> <?php echo htmlspecialchars($student['guardian1_phone']); ?></p>
                </div>
            </div>
            <div class="id-card-footer">
                <span>Issued: <?php echo $issue_date->format('Y-m-d'); ?></span> | <span>Expires: <?php echo $expiry_date->format('Y-m-d'); ?></span>
            </div>
        </div>
    </div>

    <!-- Academic Data Column -->
    <div class="student-data-container">
        <div class="gpa-tracker">
            <h3>Current GPA</h3>
            <p class="gpa-value"><?php echo $gpa; ?></p>
        </div>

        <div class="data-table">
            <h3>My Grades</h3>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Total Score (/100)</th>
                        <th>Grade Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($grades)): ?>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($grade['total'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                        $score = $grade['total'];
                                        if ($score >= 90) echo '4.0';
                                        elseif ($score >= 80) echo '3.0';
                                        elseif ($score >= 70) echo '2.0';
                                        elseif ($score >= 60) echo '1.0';
                                        else echo '0.0';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="no-data">No grades have been recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="data-table" style="margin-top: 30px;">
            <h3>Recent Attendance</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendance)): ?>
                        <?php foreach ($attendance as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($record['date']))); ?></td>
                                <td><?php echo htmlspecialchars($record['subject_name']); ?></td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($record['status']);
                                        $status_class = ($status === 'present') ? 'status-present' : 'status-absent';
                                    ?>
                                    <span class="<?php echo $status_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="no-data">No attendance has been recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once __DIR__ . '/../includes/footer.php';
?>