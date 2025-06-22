<?php
session_start();
require_once __DIR__ . '/../helpers.php';

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page with a success message
redirect_with_message('/programing/schoo-main/schoo-main/schoo/frontend/public/auth/login.php', 'success', 'You have been logged out successfully.');
?>