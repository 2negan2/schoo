<?php
session_start();
require_once __DIR__ . '/../../backend/config/connection.php';

$student_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$student = null;
$sections = [];
$error_message = $_GET['error'] ?? '';
$success_message = $_GET['success'] ?? '';

if (!$student_id) {
    header("Location: students.php?error=Invalid student ID.");
    exit();
}

// Fetch student details
$stmt = $conn->prepare("SELECT s.*, u.username FROM students s LEFT JOIN users u ON s.user_id = u.id WHERE s.id = ?");
if ($stmt) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    } else {
        header("Location: students.php?error=Student not found.");
        exit();
    }
    $stmt->close();
} else {
    $error_message = "Error preparing statement: " . $conn->error;
}

// Fetch sections for dropdown
$section_result = $conn->query("SELECT id, name, grade FROM sections ORDER BY grade, name");
if ($section_result) {
    while ($row = $section_result->fetch_assoc()) {
        $sections[] = $row;
    }
}

if ($student === null && empty($error_message)) {
    // Fallback if student somehow wasn't loaded and no DB error was caught
    header("Location: students.php?error=Could not load student data.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - International School Portal</title>
    <style>
        :root {
            /* Light Mode */
            --bg-color-light: #ffffff; --text-color-light: #333333; --primary-color-light: #004080;
            --secondary-color-light: #f0f0f0; --header-bg-light: #002b5c; --header-text-light: #ffffff;
            --footer-bg-light: #002b5c; --footer-text-light: #e0e0e0; --card-bg-light: #00509e;
            --card-text-light: #ffffff; --card-hover-bg-light: #003366; --border-color-light: #dee2e6;
            --link-color-light: #004080; --button-bg-light: #00509e; --button-text-light: #ffffff;
            --button-hover-bg-light: #003366; --input-bg-light: #fff; --input-border-light: #ced4da;
            --error-message-bg-light: #f8d7da; --error-message-color-light: #721c24; --error-message-border-light: #f5c6cb;
            --success-message-bg-light: #d4edda; --success-message-color-light: #155724; --success-message-border-light: #c3e6cb;

            /* Dark Mode */
            --bg-color-dark: #1a1a1a; --text-color-dark: #e0e0e0; --primary-color-dark: #5c9ded;
            --secondary-color-dark: #2c2c2c; --header-bg-dark: #0d1b2a; --header-text-dark: #ffffff;
            --footer-bg-dark: #0d1b2a; --footer-text-dark: #cccccc; --card-bg-dark: #004080;
            --card-text-dark: #ffffff; --card-hover-bg-dark: #00509e; --border-color-dark: #444444;
            --link-color-dark: #5c9ded; --button-bg-dark: #004080; --button-text-dark: #ffffff;
            --button-hover-bg-dark: #00509e; --input-bg-dark: #2c2c2c; --input-border-dark: #555;
            --error-message-bg-dark: #522626; --error-message-color-dark: #f8d7da; --error-message-border-dark: #721c24;
            --success-message-bg-dark: #1f4d2b; --success-message-color-dark: #d4edda; --success-message-border-dark: #2a683b;
        }
        [data-theme="dark"] {
            --bg-color: var(--bg-color-dark); --text-color: var(--text-color-dark);
            --primary-color: var(--primary-color-dark); --secondary-color: var(--secondary-color-dark);
            --header-bg: var(--header-bg-dark); --header-text: var(--header-text-dark);
            --footer-bg: var(--footer-bg-dark); --footer-text: var(--footer-text-dark);
            --card-bg: var(--card-bg-dark); --card-text: var(--card-text-dark);
            --card-hover-bg: var(--card-hover-bg-dark); --border-color: var(--border-color-dark);
            --link-color: var(--link-color-dark); --button-bg: var(--button-bg-dark);
            --button-text: var(--button-text-dark); --button-hover-bg: var(--button-hover-bg-dark);
            --input-bg: var(--input-bg-dark); --input-border: var(--input-border-dark);
            --error-message-bg: var(--error-message-bg-dark); --error-message-color: var(--error-message-color-dark);
            --error-message-border: var(--error-message-border-dark);
            --success-message-bg: var(--success-message-bg-dark); --success-message-color: var(--success-message-color-dark);
            --success-message-border: var(--success-message-border-dark);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0;
            background-color: var(--bg-color, var(--bg-color-light));
            color: var(--text-color, var(--text-color-light));
            line-height: 1.6; transition: background-color 0.3s ease, color 0.3s ease;
        }
        .header {
            background-color: var(--header-bg, var(--header-bg-light)); color: var(--header-text, var(--header-text-light));
            padding: 15px 20px; /* Adjusted padding */
            text-align: left; /* Align title left */
            border-bottom: 4px solid var(--primary-color, var(--primary-color-light));
            display: flex; justify-content: space-between; align-items: center;
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
        .container {
            width: 90%; max-width: 700px; margin: 30px auto; padding: 20px;
            background-color: var(--secondary-color, var(--secondary-color-light));
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="tel"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%; padding: 10px; border-radius: 4px;
            border: 1px solid var(--input-border, var(--input-border-light));
            background-color: var(--input-bg, var(--input-bg-light));
            color: var(--text-color, var(--text-color-light));
            box-sizing: border-box;
        }
        .btn {
            background-color: var(--button-bg, var(--button-bg-light)); color: var(--button-text, var(--button-text-light));
            padding: 10px 20px; text-decoration: none; border-radius: 5px; border:none; cursor:pointer;
            font-size: 1em; transition: background-color 0.3s ease; display: inline-block;
        }
        .btn:hover { background-color: var(--button-hover-bg, var(--button-hover-bg-light));}
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .footer {
            text-align: center; padding: 20px; margin-top: 40px;
            background-color: var(--footer-bg, var(--footer-bg-light));
            color: var(--footer-text, var(--footer-text-light));
        }
        .message { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
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
        /* Animations */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .header, .container, .footer { opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
        .container { animation-delay: 0.2s; }
        .footer { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-links">
            <h1>Edit Student</h1>
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
        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo htmlspecialchars(urldecode($error_message)); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo htmlspecialchars(urldecode($success_message)); ?></div>
        <?php endif; ?>

        <?php if ($student): ?>
        <form action="../../backend/actions/update_student.php" method="POST">
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">

            <div class="form-group">
                <label>Username (Linked Account):</label>
                <input type="text" value="<?php echo htmlspecialchars($student['username'] ?? 'N/A'); ?>" readonly disabled>
                <small>User account linking is managed separately.</small>
            </div>

            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="male" <?php echo ($student['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($student['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo ($student['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="section_id">Section:</label>
                <select id="section_id" name="section_id">
                    <option value="">Select Section</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?php echo $section['id']; ?>" <?php echo ($student['section_id'] == $section['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($section['name'] . " (Grade " . $section['grade'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="score">Admission Score (if applicable):</label>
                <input type="number" step="0.01" id="score" name="score" value="<?php echo htmlspecialchars($student['score'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="last_grade">Last Grade Completed:</label>
                <input type="text" id="last_grade" name="last_grade" value="<?php echo htmlspecialchars($student['last_grade'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="last_school">Last School Attended:</label>
                <input type="text" id="last_school" name="last_school" value="<?php echo htmlspecialchars($student['last_school'] ?? ''); ?>">
            </div>

            <button type="submit" class="btn">Update Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </form>
        <?php else: ?>
            <p>Student data could not be loaded.</p>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> International School. All rights reserved.</p>
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
        if (currentTheme) { setTheme(currentTheme); }
        else if (prefersDark) { setTheme('dark'); }
        else { setTheme('light'); }
        themeToggle.addEventListener('click', () => {
            let newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) { setTheme(e.matches ? 'dark' : 'light'); }
        });
    </script>
</body>
</html>