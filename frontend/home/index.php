<?php
session_start();
// This is a public-facing page, so no authorization check is needed here.
// Users should be able to view the homepage without logging in or having specific roles.

// Define page-specific variables for the header
$page_title = "Welcome to Our School - International School Portal";
$header_title = "Welcome to Our School";
$body_class = "school-homepage"; // Custom class for specific styling on this page

// Include the common header. This header should ideally include global CSS,
// font links, and icon libraries.
// The path assumes header.php is in frontend/public/includes/
include_once __DIR__ . '/header.php';
?>

<div class="hero-section">
    <div class="container">
        <h1>Empowering Minds, Shaping Futures</h1>
        <p>Discover a world-class education experience at our International School, fostering innovation and global citizenship.</p>
        <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/admissions/index.php" class="btn">Apply Now <i class="fas fa-arrow-right"></i></a>
    </div>
</div>

<div class="section about-us">
    <div class="container">
        <h2>About Our School</h2>
        <p>We are dedicated to providing a nurturing and challenging environment where students can thrive academically, socially, and personally. Our diverse community and innovative curriculum prepare students for success in a rapidly changing world.</p>
        <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/about/index.php" class="btn">Learn More <i class="fas fa-info-circle"></i></a>
    </div>
</div>

<div class="section news-events">
    <div class="container">
        <h2>Latest News & Events</h2>
        <p>Stay updated with the latest happenings, achievements, and upcoming events at our school. From academic triumphs to community service, there's always something exciting happening!</p>
        <a href="/programing/schoo-main/schoo-main/schoo/frontend/public/news/index.php" class="btn">View All News <i class="fas fa-newspaper"></i></a>
    </div>
</div>

<?php
// Include the common footer.
include_once __DIR__ . '/footer.php'; // Now includes the footer from the 'home' folder
?>