
<?php
session_start(); // Good practice
require_once __DIR__ . '/../../backend/helpers.php';

// Authorization Check: Ensure a user is logged in AND is an admin
if (!isset($_SESSION['user_id'])) {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php',
        'error',
        'You must be logged in to access this page.'
    );
} elseif ($_SESSION['role'] !== 'admin') {
    redirect_with_message(
        '/programing/schoo-main/schoo-main/schoo/frontend/public/index.php',
        'error',
        'You do not have permission to access this page. Admin access required.'
    );
}

$page_title = "Student Management - International School Portal";
$header_title = "Student Management";

// Check for session-based messages from redirects
$session_message = $_SESSION['message'] ?? null;
if ($session_message) {
    unset($_SESSION['message']); // Clear the message after retrieving it
}
require_once __DIR__ . '/../../backend/config/connection.php'; // Path to DB connection
 
// Search and filter logic
$search = trim($_GET['search'] ?? '');
$where_clauses = [];
$params = [];
$param_types = '';
 
if (!empty($search)) {
    $search_term = "%{$search}%";
    // Search by full name, username, grade, or registration date
    $where_clauses[] = "(CONCAT_WS(' ', s.first_name, s.middle_name, s.last_name) LIKE ? OR u.username LIKE ? OR sec.grade LIKE ? OR DATE(s.registered_at) LIKE ?)";
    // Add params for each LIKE
    for ($i = 0; $i < 4; $i++) {
        $params[] = $search_term;
        $param_types .= 's';
    }
}
 
// Base SQL query
$sql = "SELECT
            s.id,
            s.first_name,
            s.middle_name,
            s.last_name,
            u.username,
            sec.name AS section_name,
            s.grade AS current_student_grade,
            sec.grade AS section_grade_level,
            s.last_school,
            s.last_score,
            s.last_grade,
            s.date_of_birth,
            s.gender,
            s.registered_at
        FROM
            students s
        LEFT JOIN
            users u ON s.user_id = u.id
        LEFT JOIN
            class_assignments ca ON s.id = ca.student_id
        LEFT JOIN
            sections sec ON ca.section_id = sec.id";
 
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
 
// New ordering: by grade, then section, then student name
$sql .= " ORDER BY section_grade_level ASC, sec.name ASC, s.first_name ASC";
 
$students = [];
$error_message = '';
$stmt = $conn->prepare($sql);
 
if ($stmt === false) {
    $error_message = "Error preparing statement: " . htmlspecialchars($conn->error);
} else {
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
    } else {
        $error_message = "Error executing query: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
// $conn will be closed by connection.php or can be closed manually if needed.
include_once __DIR__ . '/../includes/header.php'; ?>
     
    <div class="container">
        <div class="search-bar">
            <form action="/programing/schoo-main/schoo-main/schoo/frontend/public/students.php" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search by Name, Username, Grade, or Date (YYYY-MM-DD)..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn"><i class="fas fa-search"></i> Search</button>
                <?php if (!empty($search)): ?>
                    <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/students.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="action-bar">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/create_student.php" class="btn"><i class="fas fa-user-plus"></i> Add Student</a>
        </div>

        <?php if ($session_message): ?>
            <div class="message <?php echo $session_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($session_message['text'])); // Use nl2br to respect line breaks ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Current Grade</th>
                    <th>Section</th>
                    <th>Last Grade</th>
                    <th>Last School</th>
                    <th>Last Score</th>
                    <th>D.O.B</th>
                    <th>Gender</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars(trim($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name'])); ?></td>
                            <td><?php echo htmlspecialchars($student['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['current_student_grade'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['section_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_grade'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_school'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($student['last_score'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($student['date_of_birth']))); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($student['gender'])); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($student['registered_at']))); ?></td>
                            <td>
                                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/edit_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/delete_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this student and all their related records? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="12" class="no-data">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>