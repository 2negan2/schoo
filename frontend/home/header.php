<?php
// This header is specifically for the public-facing school homepage and related public pages.
// It does not contain the administrative navigation.

// Define default values if not set by the including page
$page_title = $page_title ?? 'International School Portal';
$header_title = $header_title ?? 'International School Portal';
$body_class = $body_class ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Main stylesheet for the school portal -->
    <!-- Path to style.css is relative to frontend/home/ -->
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Homepage-specific stylesheet -->
    <link rel="stylesheet" href="home.css">
    <!-- Google Fonts - Open Sans (example from index.php for modern font) -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo $body_class; ?>">
    <header class="header">
        <div class="header-links">
            <h1><?php echo $header_title; ?></h1>
        </div>
        <div class="header-links">
            <!-- Public-facing navigation links -->
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/home/index.php"><i class="fas fa-home"></i> Home</a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/admissions/index.php"><i class="fas fa-user-plus"></i> Admissions</a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/news/index.php"><i class="fas fa-newspaper"></i> News</a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/library/index.php"><i class="fas fa-book-reader"></i> Library</a>
            <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <button id="theme-toggle" class="theme-toggle-btn"><i id="theme-icon" class="fas fa-moon"></i></button>
        </div>
    </header>