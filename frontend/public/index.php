<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Home</title>
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
            --card-bg-light: #00509e; /* A slightly lighter, more vibrant blue */
            --card-text-light: #ffffff;
            --card-hover-bg-light: #003366; /* Darker shade of card blue */
            --border-color-light: #dee2e6;
            --link-color-light: #004080;

            /* Dark Mode - Classic International School Palette */
            --bg-color-dark: #1a1a1a; /* Very dark gray */
            --text-color-dark: #e0e0e0; /* Light gray */
            --primary-color-dark: #5c9ded; /* Pleasant, readable blue on dark */
            --secondary-color-dark: #2c2c2c; /* Medium dark gray for subtle contrasts */
            --header-bg-dark: #0d1b2a; /* Very dark, almost black blue */
            --header-text-dark: #ffffff;
            --footer-bg-dark: #0d1b2a;
            --footer-text-dark: #cccccc;
            --card-bg-dark: #004080; /* Using the light mode primary for cards to stand out */
            --card-text-dark: #ffffff;
            --card-hover-bg-dark: #00509e;
            --border-color-dark: #444444;
            --link-color-dark: #5c9ded;
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
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-color, var(--bg-color-light));
            color: var(--text-color, var(--text-color-light));
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .header {
            background-color: var(--header-bg, var(--header-bg-light));
            color: var(--header-text, var(--header-text-light));
            padding: 15px 20px; /* Adjusted padding */
            text-align: left; /* Align title left */
            border-bottom: 4px solid var(--primary-color, var(--primary-color-light));
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Space out items */
            align-items: center; /* Vertically align items */
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header-links {
            display: flex;
            align-items: center;
            gap: 15px; /* Space between links */
            flex-wrap: wrap; /* Allow links to wrap on smaller screens */
            justify-content: center; /* Center links if they wrap */
        }
        .header-links a {
            color: var(--header-text, var(--header-text-light));
            text-decoration: none;
            font-size: 1em;
            transition: opacity 0.3s ease;
            padding: 5px 10px; /* Add padding to links */
            border-radius: 4px; /* Rounded corners for links */
            transition: background-color 0.3s ease;
        }
        .header-links a:hover {
            opacity: 0.8;
            background-color: rgba(255, 255, 255, 0.1); /* Subtle hover effect */
        }
        #theme-toggle {
            background-color: var(--primary-color, var(--primary-color-light));
            color: var(--header-text, var(--header-text-light));
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }
        #theme-toggle:hover {
            opacity: 0.9;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: var(--secondary-color, var(--secondary-color-light));
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        [data-theme="dark"] .container {
            box-shadow: 0 4px 15px rgba(255,255,255,0.05);
        }
        .navigation-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin: 30px 0;
        }
        .card {
            background-color: var(--card-bg, var(--card-bg-light));
            color: var(--card-text, var(--card-text-light));
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 200px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card:hover {
            background-color: var(--card-hover-bg, var(--card-hover-bg-light));
            transform: translateY(-5px);
        }
        .card h3 {
            margin-top: 0;
            font-size: 1.2em;
        }
        .content-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--bg-color, var(--bg-color-light)); /* Inner sections match body bg */
            border-radius: 6px;
        }
        .content-section h2 {
            color: var(--primary-color, var(--primary-color-light));
            border-bottom: 2px solid var(--primary-color, var(--primary-color-light));
            padding-bottom: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: var(--footer-bg, var(--footer-bg-light));
            color: var(--footer-text, var(--footer-text-light));
            margin-top: 40px;
        }
        .dev-link { font-size: 0.8em; margin-top: 10px;}
        .dev-link a { color: var(--footer-text, var(--footer-text-light)); opacity: 0.8; }
        .dev-link a:hover { opacity: 1; }
    </style>
</head>
<body>
    <header class="header">
        <div>
            <h1>International School Portal</h1>
        </div>
        <div class="header-links">
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
        <div class="navigation-cards">
            <a href="auth/login.php?role=student" class="card"><h3>Student Portal</h3></a>
            <a href="auth/login.php?role=teacher" class="card"><h3>Teacher Portal</h3></a>
            <a href="auth/login.php?role=admin" class="card"><h3>Administration</h3></a>
            <a href="auth/login.php?role=director" class="card"><h3>Directorate</h3></a>
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

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> International School. All rights reserved.</p>
        <p class="dev-link"><a href="../setup_tables.php">Initialize Database (For Development Use Only)</a></p>
    </footer>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Function to set the theme
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            themeToggle.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
        }

        // Initialize theme
        if (currentTheme) {
            setTheme(currentTheme);
        } else if (prefersDark) {
            setTheme('dark'); // Default to system preference if no localStorage
        } else {
            setTheme('light'); // Default to light if no preference or localStorage
        }

        themeToggle.addEventListener('click', () => {
            let newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) { // Only change if user hasn't manually set a theme
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    </script>
</body>
</html>