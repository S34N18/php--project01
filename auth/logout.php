<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set a message for the login page
session_start();
$_SESSION['success_message'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit;
?>