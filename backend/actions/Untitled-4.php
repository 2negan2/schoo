<?php

/**
 * Redirects to a specified location with a session flash message.
 */
function redirect_with_message($location, $type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
    header("Location: $location");
    exit();
}
?>