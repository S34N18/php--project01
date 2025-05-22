<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Set a success message for the login page
session_start(); // Start a new session for the message
$_SESSION['success_message'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit;
?>