<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/assign_sections.php';

include_once __DIR__ . '/../includes/header.php';
?>

    <div class="container <?php echo $container_class; ?>">
        <h1><?php echo htmlspecialchars($header_title); ?></h1>
        <?php if (isset($error_message)): ?>
            <div class="message error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="message <?php echo $_SESSION['flash_message']['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                <?php unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <button type="submit" class="btn"><i class="fas fa-random"></i> Run Section Assignment</button>
        </form>
        
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <?php if (!empty($sectionAssignments)): ?>
            <h2>Suggested Section Assignments:</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Last Score</th>
                            <th>Suggested Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <?php
                            $studentId = $student['id'];
                            $sectionId = $sectionAssignments[$studentId];

                            $sectionName = 'N/A';
                            foreach ($sections as $section) {
                                if ($section['id'] == $sectionId) {
                                    $sectionName = $section['name'];
                                    break;
                                }
                            }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_score']); ?></td>
                                <td><?php echo htmlspecialchars($sectionName); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>