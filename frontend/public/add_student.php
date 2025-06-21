<?php
session_start();
require_once __DIR__ . '/../../backend/config/connection.php';

$session_message = $_SESSION['message'] ?? null;
if ($session_message) { unset($_SESSION['message']); }

$error_message = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student - International School Portal</title>
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

            /* Dark Mode */
            --success-message-bg-light: #d4edda; --success-message-color-light: #155724; --success-message-border-light: #c3e6cb;

            /* Dark Mode */
            /* Dark Mode */
            --bg-color-dark: #1a1a1a; --text-color-dark: #e0e0e0; --primary-color-dark: #5c9ded;
            --secondary-color-dark: #2c2c2c; --header-bg-dark: #0d1b2a; --header-text-dark: #ffffff;
            --footer-bg-dark: #0d1b2a; --footer-text-dark: #cccccc; --card-bg-dark: #004080;
            --card-text-dark: #ffffff; --card-hover-bg-dark: #00509e; --border-color-dark: #444444;
            --link-color-dark: #5c9ded; --button-bg-dark: #004080; --button-text-dark: #ffffff;
            --button-hover-bg-dark: #00509e; --input-bg-dark: #2c2c2c; --input-border-dark: #555;
            --error-message-bg-dark: #522626; --error-message-color-dark: #f8d7da; --error-message-border-dark: #721c24;
        }
        --success-message-bg-dark: #1f4d2b; --success-message-color-dark: #d4edda; --success-message-border-dark: #2a683b;

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
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            /* New: Animated Background */
            background: linear-gradient(135deg, #e0f2f7, #cce7f0, #b3dce6, #99d1dc);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
        [data-theme="dark"] body {
            background: linear-gradient(135deg, #2c3e50, #34495e, #2c3e50, #1a242f);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }
        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
            width: 90%; max-width: 700px; margin: 30px auto;
            padding: 30px; /* Increased padding */
            background-color: var(--secondary-color, var(--secondary-color-light));
            border-radius: 12px; /* More rounded corners */
            box-shadow: 0 8px 25px rgba(0,0,0,0.15); /* Stronger shadow */
            animation: fadeInUp 0.8s ease-out forwards; /* Apply animation to container */
        }
        [data-theme="dark"] .container {
            box-shadow: 0 8px 25px rgba(255,255,255,0.08);
        }
        .form-group {
            margin-bottom: 20px; /* Increased spacing */
            position: relative; /* For potential label animations */
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600; /* Slightly bolder labels */
            color: var(--primary-color, var(--primary-color-light)); /* Primary color for labels */
            transition: color 0.3s ease;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group input[type="tel"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea { /* Added textarea */
            width: 100%; padding: 10px; border-radius: 4px;
            border: 1px solid var(--input-border, var(--input-border-light));
            background-color: var(--input-bg, var(--input-bg-light));
            color: var(--text-color, var(--text-color-light));
            box-sizing: border-box;
            padding: 12px; /* Increased padding */
            border-radius: 8px; /* More rounded inputs */
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color, var(--primary-color-light));
            box-shadow: 0 0 0 3px rgba(0, 64, 128, 0.2); /* Subtle focus glow */
        }
        [data-theme="dark"] .form-group input:focus,
        [data-theme="dark"] .form-group select:focus,
        [data-theme="dark"] .form-group textarea:focus {
            box-shadow: 0 0 0 3px rgba(92, 157, 237, 0.3);
        }
        /* Grid layout for multi-column sections */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-grid .form-group {
            margin-bottom: 0; /* Reset margin for grid items */
        }
        .btn {
            background-color: var(--button-bg, var(--button-bg-light)); color: var(--button-text, var(--button-text-light));
            padding: 12px 25px; /* Larger buttons */
            text-decoration: none; border-radius: 8px; /* More rounded buttons */
            border:none; cursor:pointer; font-size: 1em;
            font-weight: bold; letter-spacing: 0.5px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
        }
        .btn:hover { background-color: var(--button-hover-bg, var(--button-hover-bg-light));}
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .footer {
            text-align: center; padding: 20px; margin-top: 40px;
            background-color: var(--footer-bg, var(--footer-bg-light));
            color: var(--footer-text, var(--footer-text-light));
        }
        .message {
            padding: 15px; margin-bottom: 20px;
            border: 1px solid transparent; border-radius: 8px; /* More rounded messages */
            font-weight: 500;
            animation: fadeIn 0.5s ease-out forwards;
        }
        .error-message {
            background-color: var(--error-message-bg, var(--error-message-bg-light));
            color: var(--error-message-color, var(--error-message-color-light));
            border-color: var(--error-message-border, var(--error-message-border-light));
        }
        .success-message {
            background-color: var(--success-message-bg-light); color: var(--success-message-color-light); border-color: var(--success-message-border-light);
        }
        [data-theme="dark"] .success-message {
            background-color: var(--success-message-bg-dark); color: var(--success-message-color-dark); border-color: var(--success-message-border-dark);
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        /* Animations */
        /* Removed redundant fadeInUp from header/footer as container handles it */
    </style>
</head>
<body>
    <header class="header">
        <div class="header-links">
            <h1>Add New Student</h1>
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
        <?php if ($session_message): ?>
            <div class="message <?php echo $session_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($session_message['text'])); ?>
            </div>
        <?php elseif (!empty($_GET['error'])): // Fallback for old error handling ?>
            <div class="message error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <form action="../../backend/actions/add_student.php" method="POST">
            <h2>Personal Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>

                <div class="form-group">
                    <label for="middle_name">Middle Name:</label>
                    <input type="text" id="middle_name" name="middle_name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nationality">Nationality:</label>
                    <input type="text" id="nationality" name="nationality" required>
                </div>

                <div class="form-group">
                    <label for="religion">Religion (Optional):</label>
                    <input type="text" id="religion" name="religion">
                </div>

                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="emergency_contact">Emergency Contact Phone:</label>
                    <input type="tel" id="emergency_contact" name="emergency_contact" required>
                </div>
            </div>

            <h2>Guardian Information</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="guardian1_name">Guardian 1 Name:</label>
                    <input type="text" id="guardian1_name" name="guardian1_name" required>
                </div>

                <div class="form-group">
                    <label for="guardian1_relation">Guardian 1 Relation:</label>
                    <input type="text" id="guardian1_relation" name="guardian1_relation" required>
                </div>

                <div class="form-group">
                    <label for="guardian1_phone">Guardian 1 Phone:</label>
                    <input type="tel" id="guardian1_phone" name="guardian1_phone" required>
                </div>

                <div class="form-group">
                    <label for="guardian2_name">Guardian 2 Name (Optional):</label>
                    <input type="text" id="guardian2_name" name="guardian2_name">
                </div>

                <div class="form-group">
                    <label for="guardian2_relation">Guardian 2 Relation (Optional):</label>
                    <input type="text" id="guardian2_relation" name="guardian2_relation">
                </div>

                <div class="form-group">
                    <label for="guardian2_phone">Guardian 2 Phone (Optional):</label>
                    <input type="tel" id="guardian2_phone" name="guardian2_phone">
                </div>
            </div>

            <h2>Academic History</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="grade">Current Grade:</label>
                    <input type="number" id="grade" name="grade" min="1" max="12" placeholder="e.g., 9" required>
                </div>

                <div class="form-group">
                    <label for="last_school">Last School Attended (Optional):</label>
                    <input type="text" id="last_school" name="last_school">
                </div>

                <div class="form-group">
                    <label for="last_score">Last Score:</label>
                    <input type="number" step="0.01" id="last_score" name="last_score" placeholder="e.g., 85.5" required>
                </div>

                <div class="form-group">
                    <label for="last_grade">Last Grade Completed (8-11):</label>
                    <input type="number" id="last_grade" name="last_grade" min="8" max="11" required>
                </div>
            </div>

            <button type="submit" class="btn">Add Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </form>
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