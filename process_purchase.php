<?php
session_start();
require 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: Login.php");
    exit;
}

// Check if a game_id was passed
if (!isset($_GET['game_id']) || !is_numeric($_GET['game_id'])) {
    header("Location: Games.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$game_id = $_GET['game_id'];

try {
    // Check if the user already owns the game
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM user_owned_games WHERE user_id = :user_id AND game_id = :game_id");
    $stmt_check->bindParam(':user_id', $user_id);
    $stmt_check->bindParam(':game_id', $game_id);
    $stmt_check->execute();
    
    if ($stmt_check->fetchColumn() > 0) {
        // User already owns the game, redirect with a message
        $_SESSION['purchase_message'] = "You already own this game!";
        header("Location: purchase_success.php");
        exit;
    }

    // Process the "purchase" - in a real app this would be a payment gateway call
    $purchase_successful = true;

    if ($purchase_successful) {
        // Add the game to the user's owned games in the database
        $stmt_insert = $conn->prepare("INSERT INTO user_owned_games (user_id, game_id) VALUES (:user_id, :game_id)");
        $stmt_insert->bindParam(':user_id', $user_id);
        $stmt_insert->bindParam(':game_id', $game_id);
        
        if ($stmt_insert->execute()) {
            // Purchase was successful, set a session message
            $_SESSION['purchase_message'] = "Purchase successful! The game has been added to your library.";
        } else {
            // Insertion failed
            $_SESSION['purchase_message'] = "An error occurred during the purchase. Please try again.";
        }
    } else {
        // Payment failed
        $_SESSION['purchase_message'] = "Payment failed. Please try again.";
    }
} catch (PDOException $e) {
    // Handle database error
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['purchase_message'] = "A database error occurred. Please try again later.";
}

// Redirect to the success page to display the message
header("Location: purchase_success.php");
exit;
?>