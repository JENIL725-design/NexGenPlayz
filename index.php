<?php
// CRITICAL: Start the session to check if the user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is NOT logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    
    // If NOT logged in, redirect them to the Login page
    header("Location: Login.php");
    exit;
    
} else {
    
    // If LOGGED IN, redirect them to the main application page (e.g., Home or Profile)
    header("Location: Home.php");
    exit;
}
?>