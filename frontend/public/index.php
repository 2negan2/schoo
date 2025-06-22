<?php
$page_title = "School Management System - Home";
$header_title = "International School Portal";

include_once __DIR__ . '/../includes/header.php';
?>
    <div class="container">
        <div class="navigation-cards">
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php" class="card"><h3>Student Portal</h3></a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php" class="card"><h3>Teacher Portal</h3></a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php" class="card"><h3>Administration</h3></a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php" class="card"><h3>Directorate</h3></a>
        </div>

        <section class="content-section">
            <h2>Welcome to Our Global Learning Community</h2>
            <p>Our platform provides a comprehensive suite of tools to support students, faculty, and staff. Access academic resources, manage schedules, and stay connected with our vibrant international community.</p>
        </section>
        <section class="content-section">
            <h2>Core Features</h2>
            <ul>
                <li>User Authentication & Role Management</li>
                <li>Student Information System</li>
                <li>Class and Subject Management</li>
                <li>Attendance Tracking</li>
                <li>Gradebook and Reporting</li>
                <li>Communication Tools</li>
                <li>Multilingual Support (Planned)</li>
            </ul>
        </section>
    </div>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>