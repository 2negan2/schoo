<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';

// 1. If user is not logged in, redirect to the login page immediately.
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_PATH . '/frontend/public/auth/login.php');
    exit();
}

// 2. If the logged-in user is a student, redirect them to their personal portal.
if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    header('Location: ' . BASE_PATH . '/frontend/public/student_portal.php');
    exit();
}
// 3. If we reach here, the user is a logged-in Admin or Teacher.
//    Show them a useful dashboard instead of the old welcome page.
$page_title = "Admin Dashboard - Home";
$header_title = "Welcome, " . htmlspecialchars($_SESSION['username'] ?? 'Admin') . "!";

include_once __DIR__ . '/../includes/header.php';
?>
    <div class="container">
        <h2>Admin & Staff Dashboard</h2>
        <p>This is your central hub for managing school operations. Select an option below to get started.</p>
        <div class="navigation-cards">
            <a href="<?php echo BASE_PATH; ?>/frontend/public/students.php" class="card"><h3><i class="fas fa-user-graduate"></i> Manage Students</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/teachers.php" class="card"><h3><i class="fas fa-chalkboard-teacher"></i> Manage Teachers</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/sections.php" class="card"><h3><i class="fas fa-sitemap"></i> Manage Sections</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/subjects.php" class="card"><h3><i class="fas fa-book"></i> Manage Subjects</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/attendance.php" class="card"><h3><i class="fas fa-user-check"></i> Track Attendance</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/grades.php" class="card"><h3><i class="fas fa-book-open"></i> Manage Grades</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/users.php" class="card"><h3><i class="fas fa-users-cog"></i> Manage Users</h3></a>
            <a href="<?php echo BASE_PATH; ?>/frontend/public/notifications.php" class="card"><h3><i class="fas fa-bell"></i> View Notifications</h3></a>
        </div>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>