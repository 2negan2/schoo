<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'International School Portal'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/programing/schoo-main/schoo-main/schoo/assets/css/style.css">
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <header class="header">
        <div class="header-links">
            <h1><?php echo $header_title ?? 'International School Portal'; ?></h1>
        </div>
        <div class="header-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/index.php"><i class="fas fa-home"></i> Home</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/students.php"><i class="fas fa-user-graduate"></i> Students</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/sections.php"><i class="fas fa-sitemap"></i> Sections</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/subjects.php"><i class="fas fa-book"></i> Subjects</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/users.php"><i class="fas fa-users-cog"></i> Users</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/attendance.php"><i class="fas fa-user-check"></i> Attendance</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/grades.php"><i class="fas fa-book-open"></i> Grades</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/notifications.php"><i class="fas fa-bell"></i> Notifications</a>
                <a href="/programing/schoo-main/schoo-main/schoo/backend/actions/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/index.php"><i class="fas fa-home"></i> Home</a>
                <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <?php endif; ?>
            <button id="theme-toggle" class="theme-toggle-btn"><i id="theme-icon" class="fas fa-moon"></i></button>
        </div>
    </header>