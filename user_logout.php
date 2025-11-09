<?php
require_once 'dependencies/session.php';
require_once 'dependencies/config.php';

session_unset();
session_destroy();

// Redirect to homepage after logout
header("Location: login-signup.php");
exit;
?>
