<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional helper functions
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login($redirect = 'login-signup.php') {
    if (!is_logged_in()) {
        header("Location: $redirect");
        exit();
    }
}
?>