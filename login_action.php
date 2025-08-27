<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['loginEmail']);
    $password = trim($_POST['loginPassword']);

    if (empty($email) || empty($password)) {
        die("Please enter email and password.");
    }

    try {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Check if user exists
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Direct comparison of plain-text passwords
            if ($password === $user['password_hash']) {
                // Password is correct, start a new session
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to user profile page
                header("Location: Profile.php");
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
        die("Error: " . $e->getMessage());
    }
}

// Redirect back to the main login page if not a POST request
header("Location: Profile.php");
exit();
?>