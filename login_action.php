<?php
session_start();
require 'db_connect.php'; // Ensure your database connection file is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['loginEmail']);
    $password = trim($_POST['loginPassword']);

    if (empty($email) || empty($password)) {
        // Use non-specific error for security
        die("Invalid email or password.");
    }

    try {
        // Fetch user data including the stored password (now plain text)
        $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Check if user exists
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stored_password = $user['password_hash'];

            // CRITICAL NOTE: This directly compares the plain text password against the stored value.
            // This is INSECURE and highly discouraged for production systems.
            if ($password === $stored_password) {
                // Login successful
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_start_time'] = time(); 
                
                header("Location: Home.php");
                exit();
            } else {
                // Incorrect password
                die("Invalid email or password.");
            }
        } else {
            // User not found
            die("Invalid email or password.");
        }
    } catch(PDOException $e) {
        error_log("Login Error: " . $e->getMessage());
        die("An unexpected error occurred. Please try again later.");
    }
}

// Fallback redirect if script is accessed directly without POST data
header("Location: Login.php"); 
exit();
?>
