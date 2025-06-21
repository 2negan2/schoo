<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirects to a given URL with a message stored in the session.
 *
 * @param string $url The URL to redirect to.
 * @param string $type The type of message ('success', 'error', 'info', etc.).
 * @param string $message The message to display.
 */
function redirect_with_message(string $url, string $type, string $message): void {
    $_SESSION['message'] = ['type' => $type, 'text' => $message];
    header("Location: " . $url);
    exit();
}