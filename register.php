<?php
// WARNING: Storing passwords in plain text is a severe security risk and is highly discouraged.
// This script registers a new user, saving the password as plain text and checking for email duplication.
require 'db_connect.php'; // Ensure your database connection file is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['registerName']);
    $email = trim($_POST['registerEmail']);
    $password = trim($_POST['registerPassword']); // Plain text
    $confirm_password = trim($_POST['confirmPassword']);

    // Input Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("Please fill all required fields.");
    }

    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    try {
        // 1. FIX: Check for duplicate email before proceeding (Prevents the "email already registered" error)
        $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // Found a duplicate email
            die("Error: This email is already registered. Please use a different one or log in.");
        }
        
        // Prepare SQL statement to insert the new user
        // The plain password is bound and stored in the 'password_hash' column.
        $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password)");
        
        $stmt_insert->bindParam(':username', $username);
        $stmt_insert->bindParam(':email', $email);
        $stmt_insert->bindParam(':password', $password); // Insert the PLAIN password
        
        $stmt_insert->execute();

        // Redirect to login page after successful registration
        header("Location: Login.php#login"); 
        exit();

    } catch(PDOException $e) {
        error_log("Registration Error: " . $e->getMessage());
        die("An unexpected error occurred during registration. Please try again later.");
    }
}
?>
