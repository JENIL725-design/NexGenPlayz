<?php
session_start();
require 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: Login.php");
    exit;
}

// Check if the form was submitted using POST and required fields are set
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['game_id'], $_POST['card_number'], $_POST['card_holder'], $_POST['expiry'], $_POST['cvv'])) {
    $_SESSION['purchase_message'] = "Invalid request.";
    header("Location: purchase_success.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$game_id = $_POST['game_id'];

// Sanitize and validate payment data
$card_number = trim($_POST['card_number']);
$card_holder = trim($_POST['card_holder']);
$expiry = trim($_POST['expiry']);
$cvv = trim($_POST['cvv']);

// Basic validation (you should add more robust validation in a real app)
if (empty($card_number) || empty($card_holder) || empty($expiry) || empty($cvv)) {
    $_SESSION['purchase_message'] = "All payment fields are required.";
    header("Location: purchase_success.php");
    exit;
}

try {
    // Check if the user already owns the game
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM user_owned_games WHERE user_id = :user_id AND game_id = :game_id");
    $stmt_check->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':game_id', $game_id, PDO::PARAM_INT);
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
        // Step 1: Insert payment details into the 'payments' table
        $stmt_payment = $conn->prepare("INSERT INTO payments (user_id, game_id, card_number, card_holder, expiry, cvv) VALUES (:user_id, :game_id, :card_number, :card_holder, :expiry, :cvv)");
        $stmt_payment->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_payment->bindParam(':game_id', $game_id, PDO::PARAM_INT);
        $stmt_payment->bindParam(':card_number', $card_number);
        $stmt_payment->bindParam(':card_holder', $card_holder);
        $stmt_payment->bindParam(':expiry', $expiry);
        $stmt_payment->bindParam(':cvv', $cvv);

        if ($stmt_payment->execute()) {
            // Step 2: Add the game to the user's owned games
            $stmt_insert = $conn->prepare("INSERT INTO user_owned_games (user_id, game_id) VALUES (:user_id, :game_id)");
            $stmt_insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':game_id', $game_id, PDO::PARAM_INT);

            if ($stmt_insert->execute()) {
                $_SESSION['purchase_message'] = "Purchase successful! The game has been added to your library.";
            } else {
                $_SESSION['purchase_message'] = "An error occurred while updating your game library. Please contact support.";
            }
        } else {
            $_SESSION['purchase_message'] = "An error occurred during the payment process. Please try again.";
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