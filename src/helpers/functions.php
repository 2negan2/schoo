<?php
// schoo/src/helpers/functions.php

/**
 * Redirects to a specified URL with an optional flash message.
 *
 * @param string $url The URL to redirect to.
 * @param string $type The type of message (e.g., 'success', 'error', 'info').
 * @param string $message The message content.
 */
function redirect_with_message(string $url, string $type = 'info', string $message = '') {
    if (!empty($message)) {
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    header("Location: " . $url);
    exit();
}

// Add other helper functions here as needed
// For example, a function to sanitize input:
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in and has a specific role
function check_auth_and_role(string $required_role = null) {
    if (!isset($_SESSION['user_id'])) {
        redirect_with_message(
            BASE_PATH . '/frontend/public/auth/login.php',
            'error',
            'You must be logged in to access this page.'
        );
    }
    if ($required_role && $_SESSION['role'] !== $required_role) {
        redirect_with_message(
            BASE_PATH . '/frontend/public/index.php',
            'error',
            'You do not have permission to access this page. ' . ucfirst($required_role) . ' access required.'
        );
    }
}

/**
 * Calculates GPA based on a list of grades.
 * Assumes 'total' is a score out of 100.
 * 90-100 -> 4.0, 80-89 -> 3.0, 70-79 -> 2.0, 60-69 -> 1.0
 *
 * @param array $grades Array of grade records, each with a 'total' key.
 * @return string The calculated GPA formatted to 2 decimal places, or 'N/A'.
 */
function calculate_gpa(array $grades): string {
    if (empty($grades)) {
        return 'N/A';
    }

    $total_points = 0;
    $total_subjects = 0;

    foreach ($grades as $grade) {
        $score = $grade['total'] ?? null;
        if ($score !== null) {
            $point = 0.0;
            if ($score >= 90) $point = 4.0;
            elseif ($score >= 80) $point = 3.0;
            elseif ($score >= 70) $point = 2.0;
            elseif ($score >= 60) $point = 1.0;
            $total_points += $point;
            $total_subjects++;
        }
    }

    if ($total_subjects > 0) {
        return number_format($total_points / $total_subjects, 2);
    }

    return 'N/A';
}

/**
 * Calculates age based on a date of birth string, with an adjustment.
 * The Ethiopian calendar is roughly 7-8 years behind the Gregorian.
 * This function applies a simple 8-year subtraction as requested.
 *
 * @param string $date_of_birth The date of birth in a format compatible with DateTime.
 * @return int The calculated age.
 */
function calculate_age(string $date_of_birth): int {
    try {
        $dob = new DateTime($date_of_birth);
        $now = new DateTime();
        $interval = $now->diff($dob);
        $age = $interval->y - 8; // Applying the 8-year adjustment
        return max(0, $age); // Ensure age is not negative
    } catch (Exception $e) {
        return 0; // Return 0 if date is invalid
    }
}
?>