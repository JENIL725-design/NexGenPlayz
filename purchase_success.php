<?php
session_start();

// Get the message from the session
$message = isset($_SESSION['purchase_message']) ? $_SESSION['purchase_message'] : "An unknown error occurred.";

// Clear the message from the session so it doesn't show up again on refresh
unset($_SESSION['purchase_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Status</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .message-box {
            background-color: #1a1a1a;
            border: 1px solid gray;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 0 25px rgba(211, 211, 211, 0.5);
        }
        h1 {
            background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
        }
        p {
            font-size: 18px;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(to right, #4acfee, #0099ff);
            color: black;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn:hover {
            box-shadow: 0 0 15px rgba(0, 153, 255, 0.8);
            transform: translateY(-2px);
        }
        @keyframes animate-gradient {
            to { background-position: 200%; }
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>Purchase Status</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
        <a href="Profile.php" class="btn">Go to Profile</a>
    </div>
</body>
</html>