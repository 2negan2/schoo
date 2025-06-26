<?php
// Use the new bootstrap file for common includes
require_once __DIR__ . '/../../src/bootstrap.php';

// If user is already logged in, redirect them to the main index page
// Updated: Redirect to the appropriate dashboard based on role.
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
        header('Location: ' . BASE_PATH . '/frontend/public/student_portal.php');
    } else {
        header('Location: ' . BASE_PATH . '/frontend/public/index.php');
    }
    exit();
}

$page_title = "Login - International School Portal";
$header_title = "User Login";
$body_class = "animated-background";
$container_class = "form-container";

// Check for session-based messages from redirects
$flash_message = $_SESSION['flash_message'] ?? null;
if ($flash_message) {
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"> <!-- Default theme for login page -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> <!-- Consider hosting locally -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/assets/css/style.css">
</head>
<body class="<?php echo $body_class; ?>">
    <div class="container <?php echo $container_class; ?>" style="margin-top: 10vh;">
        <h1 style="text-align: center; color: var(--primary-color, #004080);"><?php echo $header_title; ?></h1>
        
        <?php if ($flash_message): ?>
            <div class="message <?php echo $flash_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($flash_message['message']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_PATH; ?>/backend/actions/login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn" style="width: 100%;"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
        <div style="text-align: center; margin-top: 20px;"> 
            <a href="<?php echo BASE_PATH; ?>/frontend/public/index.php">Back to Home</a>
        </div>
    </div>
    
    <script>
        // A minimal script to set the theme on the login page if it exists in localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    </script>
</body>
</html>