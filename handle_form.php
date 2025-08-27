<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die("Please fill out all fields.");
    }

    try {
        $stmt = $conn->prepare("INSERT INTO support_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message);
        
        $stmt->execute();

        // Optional: Redirect to a "thank you" page or back to support with a success message
        echo "Thank you for your message! We will get back to you shortly.";
        // header("Support.php?status=success");
        exit();

    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>