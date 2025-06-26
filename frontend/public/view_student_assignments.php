<?php
// This is the view file.
require_once __DIR__ . '/../../src/pages/view_student_assignments.php';

include_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Grade</th>
                <th>Section</th>
                <th>Stream</th>
                <th>Date Assigned</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['last_name'] . ", " . $row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['section_name']) . "</td>";
                    echo "<td>" . htmlspecialchars(ucfirst($row['stream'] ?? 'N/A')) . "</td>";
                    echo "<td>" . date('Y-m-d', strtotime($row['assigned_at'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No students have been assigned to sections yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>