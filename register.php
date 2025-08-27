<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['registerName']);
    $email = trim($_POST['registerEmail']);
    $password = trim($_POST['registerPassword']);
    $confirm_password = trim($_POST['confirmPassword']);

    // Basic Validation
    if (empty($username) || empty($email) || empty($password)) {
        die("Please fill all required fields.");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    try {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password)");
        
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password); // The password is not hashed
        
        $stmt->execute();

        // Redirect to login page after successful registration
        header("Location: Login.php");
        exit();

    } catch(PDOException $e) {
        // Check for duplicate email
        if ($e->errorInfo[1] == 1062) {
            die("Error: This email is already registered. Please use a different one.");
        } else {
            die("Error: " . $e->getMessage());
        }
    }
}
?>