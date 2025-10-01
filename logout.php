<?php
session_start();
// Assuming db_connect.php contains your database connection object ($conn)
require 'db_connect.php'; 

// Get data before session is destroyed
$user_id = $_SESSION['user_id'] ?? null;
$start_time = $_SESSION['login_start_time'] ?? null;

// --- HOUR LOGGING LOGIC ---
if ($user_id && $start_time) {
    $end_time = time();
    $duration_seconds = $end_time - $start_time;
    
    // Only log time if duration is reasonable (e.g., more than 10 seconds)
    if ($duration_seconds > 10) {
        $duration_hours = $duration_seconds / 3600; // Convert seconds to hours
        
        try {
            // Using UPSERT: INSERT IF NOT EXISTS, OR ADD TO hours_logged IF EXISTS
            // Requires 'user_id' in 'user_stats' to be a PRIMARY KEY or UNIQUE INDEX.
            $stmt = $conn->prepare("
                INSERT INTO user_stats (user_id, hours_logged) 
                VALUES (:user_id, :hours)
                ON DUPLICATE KEY UPDATE hours_logged = hours_logged + :hours_update
            ");
            
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':hours', $duration_hours);
            $stmt->bindParam(':hours_update', $duration_hours);
            $stmt->execute();

        } catch (PDOException $e) {
            // Log error
            error_log("Logout hour logging failed for User ID {$user_id}: " . $e->getMessage());
        }
    }
}
// --- END HOUR LOGGING LOGIC ---


// --- SESSION CLEARING & REDIRECTION (Standard Logout Process) ---

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie (good practice)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: Login.php");
exit;
?>