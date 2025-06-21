
<?php
session_start(); // Good practice

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - International School Portal</title>
    <style>
        :root {
            /* Light Mode - Classic International School Palette */
            --bg-color-light: #ffffff;
            --text-color-light: #333333;
            --primary-color-light: #004080; /* Deep, classic navy blue */
            --secondary-color-light: #f0f0f0; /* Light gray for subtle contrasts */
            --header-bg-light: #002b5c; /* Darker navy */
            --header-text-light: #ffffff;
            --footer-bg-light: #002b5c;
            --footer-text-light: #e0e0e0;
            --card-bg-light: #00509e; 
            --card-text-light: #ffffff;
            --card-hover-bg-light: #003366;
            --border-color-light: #dee2e6;
            --link-color-light: #004080;
            --button-bg-light: #00509e;
            --button-text-light: #ffffff;
            --button-hover-bg-light: #003366;
            --table-header-bg-light: #e9ecef;
            --table-row-hover-bg-light: #f8f9fa;
            --error-message-bg-light: #f8d7da;
            --error-message-color-light: #721c24;
            --error-message-border-light: #f5c6cb;
            --success-message-bg-light: #d4edda;
            --success-message-color-light: #155724;
            --success-message-border-light: #c3e6cb;

            /* Dark Mode - Classic International School Palette */
            --bg-color-dark: #1a1a1a;
            --text-color-dark: #e0e0e0;
            --primary-color-dark: #5c9ded;
            --secondary-color-dark: #2c2c2c;
            --header-bg-dark: #0d1b2a;
            --header-text-dark: #ffffff;
            --footer-bg-dark: #0d1b2a;
            --footer-text-dark: #cccccc;
            --card-bg-dark: #004080;
            --card-text-dark: #ffffff;
            --card-hover-bg-dark: #00509e;
            --border-color-dark: #444444;
            --link-color-dark: #5c9ded;
            --button-bg-dark: #004080;
            --button-text-dark: #ffffff;
            --button-hover-bg-dark: #00509e;
            --table-header-bg-dark: #343a40;
            --table-row-hover-bg-dark: #3E444A;
            --error-message-bg-dark: #522626;
            --error-message-color-dark: #f8d7da;
            --error-message-border-dark: #721c24;
            --success-message-bg-dark: #1f4d2b;
            --success-message-color-dark: #d4edda;
            --success-message-border-dark: #2a683b;
        }

        [data-theme="dark"] {
            --bg-color: var(--bg-color-dark);
            --text-color: var(--text-color-dark);
            --primary-color: var(--primary-color-dark);
            --secondary-color: var(--secondary-color-dark);
            --header-bg: var(--header-bg-dark);
            --header-text: var(--header-text-dark);
            --footer-bg: var(--footer-bg-dark);
            --footer-text: var(--footer-text-dark);
            --card-bg: var(--card-bg-dark);
            --card-text: var(--card-text-dark);
            --card-hover-bg: var(--card-hover-bg-dark);
            --border-color: var(--border-color-dark);
            --link-color: var(--link-color-dark);
            --button-bg: var(--button-bg-dark);
            --button-text: var(--button-text-dark);
            --button-hover-bg: var(--button-hover-bg-dark);
            --table-header-bg: var(--table-header-bg-dark);
            --table-row-hover-bg: var(--table-row-hover-bg-dark);
            --error-message-bg: var(--error-message-bg-dark);
            --error-message-color: var(--error-message-color-dark);
            --error-message-border: var(--error-message-border-dark);
            --success-message-bg: var(--success-message-bg-dark);
            --success-message-color: var(--success-message-color-dark);
            --success-message-border: var(--success-message-border-dark);
        }

        /* Keyframes for animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Initial state for elements that will animate */
        .header, .container, .action-bar, table, .footer {
            opacity: 0;
            transform: translateY(20px);
        }

        /* Apply animations with staggered delays */
        .header {
            animation: fadeInUp 0.8s ease-out forwards 0.2s;
        }
        .container { /* Animates the main content box */
            animation: fadeInUp 0.8s ease-out forwards 0.4s;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .search-bar form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .search-bar input {
            flex-grow: 1;
            padding: 10px;
        }
        .action-bar { /* Animates within the container */
            animation: fadeInUp 0.7s ease-out forwards 0.6s;
        }
        table { /* Animates within the container */
            animation: fadeInUp 0.7s ease-out forwards 0.8s;
        }
        .footer {
            animation: fadeInUp 0.8s ease-out forwards 1.0s;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0;
            background-color: var(--bg-color, var(--bg-color-light));
            color: var(--text-color, var(--text-color-light));
            line-height: 1.6; transition: background-color 0.3s ease, color 0.3s ease;
        }
        .header {
            background-color: var(--header-bg, var(--header-bg-light)); color: var(--header-text, var(--header-text-light));
            padding: 20px; text-align: center; border-bottom: 4px solid var(--primary-color, var(--primary-color-light));
            display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; /* Adjusted padding */
        }
        .header h1 { margin: 0; font-size: 2em; }
        .header-links {
            display: flex;
            align-items: center;
            gap: 15px; /* Space between links */
            flex-wrap: wrap; /* Allow links to wrap */
            justify-content: center; /* Center links if they wrap */
        }
        .header-links a {
            color: var(--header-text, var(--header-text-light)); text-decoration: none; font-size: 1em;
            padding: 5px 10px; border-radius: 4px;
            transition: background-color 0.3s ease, opacity 0.3s ease;
        }
        .header-links a:hover { opacity: 0.8; background-color: rgba(255, 255, 255, 0.1); }
        #theme-toggle {
            background-color: var(--primary-color, var(--primary-color-light)); color: var(--header-text, var(--header-text-light));
            border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 0.9em;
        }
        #theme-toggle:hover { opacity: 0.9; }
        .container {
            width: 90%; max-width: 1400px; margin: 30px auto; padding: 20px; /* Increased max-width for more columns */
            background-color: var(--secondary-color, var(--secondary-color-light));
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px;
        }
        [data-theme="dark"] .container { box-shadow: 0 4px 15px rgba(255,255,255,0.05); }
        .action-bar { margin-bottom: 20px; text-align: right; }
        .btn {
            background-color: var(--button-bg, var(--button-bg-light)); color: var(--button-text, var(--button-text-light));
            padding: 10px 15px; text-decoration: none; border-radius: 5px; border:none; cursor:pointer;
            font-size: 0.9em; transition: background-color 0.3s ease;
        }
        .btn:hover { background-color: var(--button-hover-bg, var(--button-hover-bg-light));}
        .btn-edit { background-color: #ffc107; color: #212529; } /* Yellow */
        .btn-delete { background-color: #dc3545; color: white; } /* Red */
        .btn-sm { padding: 5px 10px; font-size: 0.8em; margin-right: 5px;}

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td {
            border: 1px solid var(--border-color, var(--border-color-light));
            padding: 10px; text-align: left; font-size: 0.9em; /* Slightly smaller font for more data */
        }
        th { background-color: var(--table-header-bg, var(--table-header-bg-light)); font-weight: 600; }
        tbody tr:hover { background-color: var(--table-row-hover-bg, var(--table-row-hover-bg-light)); }
        .no-data { text-align: center; padding: 20px; font-style: italic; }
        .message {
            padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px;
            white-space: pre-wrap; /* Allows line breaks in the message */
        }
        .error-message {
            background-color: var(--error-message-bg, var(--error-message-bg-light));
            color: var(--error-message-color, var(--error-message-color-light));
            border-color: var(--error-message-border, var(--error-message-border-light));
        }
        .success-message {
            background-color: var(--success-message-bg, var(--success-message-bg-light));
            color: var(--success-message-color, var(--success-message-color-light));
            border-color: var(--success-message-border, var(--success-message-border-light));
        }
        .footer {
            text-align: center; padding: 20px; margin-top: 40px;
            background-color: var(--footer-bg, var(--footer-bg-light));
            color: var(--footer-text, var(--footer-text-light));
        }
        .dev-link a { color: var(--footer-text, var(--footer-text-light)); opacity: 0.8; }
    </style>
</head>
<body>
    <header class="header">
        <div>
            <h1>Student Management</h1>
        </div>
        <div class="header-links">
                <a href="index.php">Home</a>
                <a href="students.php">Students</a>
                <a href="teachers.php">Teachers</a>
                <a href="sections.php">Sections</a>
                <a href="users.php">Users</a>
                <a href="attendance.php">Attendance</a>
                <a href="grades.php">Grades</a>
                <a href="notifications.php">Notifications</a>
                <button id="theme-toggle">Toggle Theme</button>
        </div>
    </header>

    <div class="container">
        <div class="search-bar">
            <form action="students.php" method="GET">
                <input type="text" name="search" class="form-control" placeholder="Search by Name, Username, Grade, or Date (YYYY-MM-DD)..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="students.php" class="btn" style="background-color: #6c757d;">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="action-bar">
            <a href="create_student.php" class="btn">Add Student</a>
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
                                <a href="edit_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="../../backend/actions/delete_student.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this student and all their related records? This action cannot be undone.');">Delete</a>
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

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> International School. All rights reserved.</p>
        <p class="dev-link"><a href="../../setup_tables.php">Initialize Database (Dev Only)</a></p> <!-- Corrected path -->
    </footer>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            themeToggle.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }

        if (currentTheme) {
            setTheme(currentTheme);
        } else if (prefersDark) {
            setTheme('dark');
        } else {
            setTheme('light');
        }

        themeToggle.addEventListener('click', () => {
            let newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    </script>
</body>
</html>