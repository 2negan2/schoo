<?php
session_start(); // Good practice
require_once __DIR__ . '/../../backend/config/connection.php'; // Path to DB connection

// Fetch grades from the database
$grades = [];
$sql = "SELECT
            g.id,
            s.first_name,
            s.last_name,
            sub.name AS subject_name,
            g.test,
            g.assignment,
            g.activity,
            g.exercise_book,
            g.midterm,
            g.total,
            g.final_exam,
            u.username AS updated_by_username,
            g.updated_at
        FROM
            grades g
        LEFT JOIN
            students s ON g.student_id = s.id
        LEFT JOIN
            subjects sub ON g.subject_id = sub.id
        LEFT JOIN
            users u ON g.updated_by = u.id
        ORDER BY
            g.updated_at DESC, g.id DESC";

$result = $conn->query($sql);
$error_message = '';

if ($result === false) {
    // Query failed
    $error_message = "Error fetching grade data: " . htmlspecialchars($conn->error);
} elseif ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grades[] = $row;
    }
}
// $conn will be closed by connection.php or can be closed manually if needed.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management - International School Portal</title>
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
            display: flex; justify-content: space-between; align-items: center;
        }
        .header h1 { margin: 0; font-size: 2em; }
        .header a { color: var(--header-text, var(--header-text-light)); text-decoration: none; margin-left:15px; }
        #theme-toggle {
            background-color: var(--primary-color, var(--primary-color-light)); color: var(--header-text, var(--header-text-light));
            border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-size: 0.9em;
        }
        #theme-toggle:hover { opacity: 0.9; }
        .container {
            width: 90%; max-width: 1600px; margin: 30px auto; padding: 20px; /* Increased max-width for more columns */
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

        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: auto; /* or fixed if preferred */ }
        th, td {
            border: 1px solid var(--border-color, var(--border-color-light));
            padding: 8px; text-align: left; font-size: 0.85em; /* Adjusted padding and font-size for more columns */
            word-wrap: break-word; /* Helps prevent layout breaking with long content */
        }
        th { background-color: var(--table-header-bg, var(--table-header-bg-light)); font-weight: 600; }
        tbody tr:hover { background-color: var(--table-row-hover-bg, var(--table-row-hover-bg-light)); }
        .no-data { text-align: center; padding: 20px; font-style: italic; }
        .error-message {
            padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px;
            background-color: var(--error-message-bg, var(--error-message-bg-light));
            color: var(--error-message-color, var(--error-message-color-light));
            border-color: var(--error-message-border, var(--error-message-border-light));
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
            <a href="../index.php">Home</a> <!-- Link to frontend index -->
        </div>
        <h1>Grade Management</h1>
        <button id="theme-toggle">Toggle Theme</button>
    </header>

    <div class="container">
        <div class="action-bar">
            <a href="add_grade.php" class="btn">Add New Grade</a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Test</th>
                    <th>Assignment</th>
                    <th>Activity</th>
                    <th>Ex. Book</th>
                    <th>Midterm</th>
                    <th>Total</th>
                    <th>Final Exam</th>
                    <th>Updated By</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($grades)): ?>
                    <?php foreach ($grades as $grade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($grade['id']); ?></td>
                            <td><?php echo htmlspecialchars(($grade['first_name'] ?? '') . ' ' . ($grade['last_name'] ?? 'N/A Student')); ?></td>
                            <td><?php echo htmlspecialchars($grade['subject_name'] ?? 'N/A Subject'); ?></td>
                            <td><?php echo htmlspecialchars($grade['test'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['assignment'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['activity'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['exercise_book'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['midterm'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['total'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['final_exam'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($grade['updated_by_username'] ?? 'N/A User'); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($grade['updated_at']))); ?></td>
                            <td>
                                <a href="edit_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="delete_grade.php?id=<?php echo $grade['id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this grade record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (empty($error_message)): ?>
                    <tr>
                        <td colspan="13" class="no-data">No grade records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> International School. All rights reserved.</p>
        <p class="dev-link"><a href="../../setup_tables.php">Initialize Database (Dev Only)</a></p>
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