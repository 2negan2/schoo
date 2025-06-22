<?php
session_start();

// If user is already logged in, redirect them to the main index page
if (isset($_SESSION['user_id'])) {
    header('Location: /programing/schoo-main/schoo-main/schoo/frontend/public/index.php');
    exit();
}

$page_title = "Login - International School Portal";
$header_title = "User Login";
$body_class = "animated-background";
$container_class = "form-container";

// Check for session-based messages from redirects
$session_message = $_SESSION['message'] ?? null;
if ($session_message) {
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light"> <!-- Default theme for login page -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/programing/schoo-main/schoo-main/schoo/assets/css/style.css">
</head>
<body class="<?php echo $body_class; ?>">
    <div class="container <?php echo $container_class; ?>" style="margin-top: 10vh;">
        <h1 style="text-align: center; color: var(--primary-color, #004080);"><?php echo $header_title; ?></h1>
        
        <?php if ($session_message): ?>
            <div class="message <?php echo $session_message['type'] === 'success' ? 'success-message' : 'error-message'; ?>">
                <?php echo nl2br(htmlspecialchars($session_message['text'])); ?>
            </div>
        <?php endif; ?>

        <form action="/programing/schoo-main/schoo-main/schoo/backend/actions/login_process.php" method="POST">
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
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/index.php">Back to Home</a>
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