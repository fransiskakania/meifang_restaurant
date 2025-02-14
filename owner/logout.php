<?php
session_start(); // Start the session

// Destroy the session and its variables
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: /meifang_resto_admin/login.php");
exit();
?>
