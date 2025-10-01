<?php
session_start();
// NOTE: Ensure 'db_connect.php' is available or place its content here if you want a true single file
require 'db_connect.php'; 

$thank_you_message = "";
$error_message = "";

// --- 1. HANDLE FORM SUBMISSION LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Sanitize and validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Check for empty fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "Please fill out all fields.";
    } else {
        // 2. Insert data into the database
        try {
            $stmt = $conn->prepare("INSERT INTO support_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            
            if ($stmt->execute()) {
                // Set a success flag in the session
                $_SESSION['form_submission_success'] = true;
                
                // CRITICAL: Redirect to the same page using GET. This clears the POST data and the form fields.
                header("Location: Support.php");
                exit();
            } else {
                $error_message = "There was an error saving your message.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
// --- END FORM SUBMISSION LOGIC ---

// --- 2. CHECK FOR SUCCESS FLAG AFTER REDIRECT ---
if (isset($_SESSION['form_submission_success'])) {
    $thank_you_message = "Thank you! Your message has been sent successfully. We Are Reach You Very Soon!.";
    // Clear the flag so it doesn't show up on a subsequent manual refresh
    unset($_SESSION['form_submission_success']); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPPORT</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body{
            margin: 0;
            padding: 0;
            background-color: black;
            scroll-behavior: smooth;
            overflow-x: hidden; 
            position: relative; 
        }


        body::before, body::after {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            opacity: 0.1;
            filter: blur(100px);
            z-index: -2;
            animation: backgroundGlow 15s infinite alternate;
        }

        body::before {
            background-color: #4acfee;
            top: -50px;
            left: -50px;
        }

        body::after {
            background-color: #53f8c9;
            bottom: -50px;
            right: -50px;
            animation-delay: 7.5s;
        }

        @keyframes backgroundGlow {
            0% { transform: translate(0, 0); opacity: 0.1; }
            25% { transform: translate(50px, 50px); opacity: 0.15; }
            50% { transform: translate(0, 100px); opacity: 0.1; }
            75% { transform: translate(-50px, 50px); opacity: 0.15; }
            100% { transform: translate(0, 0); opacity: 0.1; }
        }


        .container{
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        header{
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 40px;
            z-index: 999;
            backdrop-filter: blur(0px);
            opacity: 0;
            animation: headerSlideIn 0.8s ease-out forwards;
            animation-delay: 0.2s;
        }

        @keyframes headerSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .head-left{
            display: flex;
            align-items: center;
        }

        .head-left img{
            width: 80px;
            height: 80px;
            margin-right: 20px;
        }

        .head-left button{
            border: none;
            padding: 10px 30px;
            border-radius: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .head-left button:hover{
            opacity: 0.5;
        }

        .nav-links{
            display: flex;
        }

        .nav-links a{
            text-decoration: none;
            padding-left: 25px;
            color: white;
            font-size: 15px;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-links a::after {
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(to right, #4acfee, #0099ff);
            transition: width 0.3s ease;
        }

        @keyframes animate-gradient{
            to{
                background-position: 200%;
            }
        }

        .support-section{
            display: flex;
            flex-direction: column;
            position: relative;
            width: 80%;
            margin-top: 150px;
            margin-bottom: 100px;
            align-items: center;
            text-align: center;
            background: radial-gradient(circle at top left, rgba(74, 207, 238, 0.1) 0%, transparent 30%),
                        radial-gradient(circle at bottom right, rgba(83, 248, 201, 0.1) 0%, transparent 30%);
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border-radius: 25px;
            padding: 50px 0;
        }

        .support-section h1{
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-weight: 900;
            font-size: 100px;
            margin: 25px 0;
            text-transform: uppercase;
            background: linear-gradient(to right, #4acfee , #53f8c9 , #02d79a , #6070fb ,#2a46ff , #0099ff , #4acfee );
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
        }

        .support-section h3{
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 50px;
            color: gray;
            background: linear-gradient(to right, #4acfee , #53f8c9 , #02d79a , #6070fb ,#2a46ff , #0099ff , #4acfee );
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
            margin-bottom: 50px;
        }

        .contact-form-container{
            background-color: #1a1a1a;
            border: 1px solid gray;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 700px;
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
            text-align: left;
            animation: fadeInScale 0.8s ease-out forwards;
            position: relative;
            overflow: hidden;
        }

        .contact-form-container::before {
            content: ' ';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
            background-size: 200%;
            z-index: -1;
            filter: blur(15px);
            opacity: 0.7;
            border-radius: 25px;
            animation: animate-gradient 2.5s linear infinite;
        }

        @keyframes animate-gradient{
            to{
                background-position: 200%;
            }
        }  

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .form-group{
            position: relative;
            margin-bottom: 30px;
        }

        .form-group label{
            position: absolute;
            top: 15px;
            left: 20px;
            color: gray;
            font-size: 16px;
            pointer-events: none;
            transition: 0.3s ease all;
        }

        .form-group input,
        .form-group textarea{
            width: calc(100% - 40px);
            padding: 15px 20px;
            background-color: #0f1217;
            border: 1px solid #4acfee;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus{
            border-color: #53f8c9;
            box-shadow: 0 0 20px rgba(83, 248, 201, 0.7);
            transform: scale(1.01);
        }

        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label,
        .form-group textarea:focus + label,
        .form-group textarea:not(:placeholder-shown) + label{
            top: -10px;
            left: 15px;
            font-size: 12px;
            color: #4acfee;
            background-color: #1a1a1a;
            padding: 0 5px;
            border-radius: 5px;
        }

        .form-group textarea{
            resize: vertical;
            min-height: 120px;
        }

        .submit-button{
            padding: 15px 40px;
            border: none;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(to right, #4acfee, #0099ff);
            color: black;
            font-size: 18px;
            box-shadow: 0 5px 15px rgba(0, 153, 255, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            animation: pulse 2s infinite ease-in-out;
        }

        .submit-button:hover{
            background: linear-gradient(to right, #53f8c9, #02d79a);
            box-shadow: 0 8px 25px rgba(83, 248, 201, 0.6); 
            transform: translateY(-3px) scale(1.02); 
            animation: none;
        }

        .submit-button:active{
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 10px rgba(0, 153, 255, 0.3);
        }

        /* --- NEW SUCCESS MESSAGE CSS & ANIMATION --- */
        .new-message-btn {
            display: inline-block;
            padding: 15px 40px; /* Matching the original submit button size */
            margin-top: 30px;
            background-color: #4acfee; /* Accent color */
            color: black;
            text-decoration: none;
            border-radius: 25px; /* Matching the original submit button shape */
            font-weight: 700;
            font-size: 18px; /* Matching the original submit button font size */
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 153, 255, 0.4);
            animation: pulse 2s infinite ease-in-out; /* Animation */
        }
        .new-message-btn:hover {
            background-color: #53f8c9;
            box-shadow: 0 8px 25px rgba(83, 248, 201, 0.6); 
            transform: translateY(-3px) scale(1.02); 
            animation: none;
        }
        /* Keyframes for the animation */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(74, 207, 238, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(74, 207, 238, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(74, 207, 238, 0);
            }
        }
        /* Style for the thank you message text */
        .success-text {
            color: #53f8c9; 
            font-weight: bold; 
            font-size: 1.2em; 
            margin: 20px 0 30px 0; /* Adjusted margin to center with button */
            display: block;
        }
        .error-message {
            color: #ff4d4d;
            margin-bottom: 20px;
            font-weight: bold;
            display: block;
        }

        .footer{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(10px);
            height: 100px;
            opacity: 0;
            animation: footerSlideIn 0.8s ease-out forwards;
            animation-delay: 0.4s;
        }

        @keyframes footerSlideIn {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .contact-section h1{
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-size: 70px;
            max-width: 550px;
            text-align: center;
            background: linear-gradient(to right, #4acfee , #53f8c9 , #02d79a , #6070fb ,#2a46ff , #0099ff , #4acfee );
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
        }

        .footer p{
            color: white;
            margin: 0 20px;
        }

        .footer ul{
            display: flex;
            list-style: none;
            margin-right: 140px;
        }

        .footer a{
            text-decoration: none;
            color: white;
            padding: 7px 7px;
            border-radius: 50px;
            border: 1px solid rgb(0, 130, 211);
            transition: 0.3s;
            margin: 0 10px;
        }

        .footer a:hover {
            background-color: rgba(0, 130, 211, 0.3);
            box-shadow: 0 0 10px rgba(0, 130, 211, 0.7);
        }

        .autoDisplay{
            animation: autoDisplayAnimation both;
            animation-timeline: view(70% 5%);
        }
        @keyframes autoDisplayAnimation{
            from{
                opacity: 0;
                transform: translateY(200px) scale(0.3);
            }
            to{
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .support-section h1 {
                font-size: 60px;
            }
            .support-section h3 {
                font-size: 30px;
            }
            header {
                padding: 10px 20px;
            }
            .head-left img {
                width: 60px;
                height: 60px;
            }
            .nav-links a {
                padding-left: 15px;
                font-size: 13px;
            }
            .contact-form-container {
                padding: 25px;
                width: 90%;
            }
            .form-group input,
            .form-group textarea {
                width: calc(100% - 30px);
                padding: 12px 15px;
            }
            .submit-button {
                padding: 12px 30px;
                font-size: 16px;
            }
            .footer {
                flex-direction: column;
                height: auto;
                padding: 20px 0;
            }
            .footer ul {
                margin: 10px 0 0 0;
                padding: 0;
                justify-content: center;
            }
            .footer p {
                margin: 10px 0;
            }
        }

        @media (max-width: 480px) {
            .support-section h1 {
                font-size: 45px;
            }
            .support-section h3 {
                font-size: 25px;
            }
            .nav-links {
                flex-direction: column;
                align-items: flex-end;
            }
            .nav-links p {
                margin: 5px 0;
            }
            .nav-links a {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
            <header>
            <div class="head-left">
                <img src="img/2.png" alt="Logo">
            </div>

            <div class="nav-links">
                <p><a href="Home.php">HOME</a></p>
                <p><a href="Games.php">GAMES</a></p>
                <p><a href="About.php">ABOUT</a></p>
                <p><a href="Support.php">SUPPORT</a></p>

               <p><a href="Profile.php">PROFILE</a></p>
                <?php 
                // ONLY show the Logout link if the user is logged in
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): 
                ?>
                <?php endif; ?>

            </div>
            </header>
            
        <section class="support-section">
            <h1 class="autoDisplay">Need Help? We've Got You!</h1>
            <h3 class="autoDisplay">Our Team is Ready to Assist You, 24/7.</h3>

             <div class="contact-form-container autoDisplay">
                <?php if (!empty($thank_you_message)): ?>
                    <span class="success-text"><?php echo htmlspecialchars($thank_you_message); ?></span>
                    <center>
                    <a href="Support.php" class="new-message-btn">
                        Send Another Message
                    </a>
                    </center>


                <?php else: // Display the form (default state or on error) ?>
                    
                    <?php if (!empty($error_message)): ?>
                        <span class="error-message"><?php echo htmlspecialchars($error_message); ?></span>
                    <?php endif; ?>
                    
                    <p style="color: lightgray; margin-bottom: 30px; text-align: center;">Fill out the form below and we'll get back to you as soon as possible. Your ultimate gaming experience is our priority!</p>
                    
                    <form action="Support.php" method="POST">
                        <div class="form-group">
                            <input type="text" id="name" name="name" placeholder=" " required>
                            <label for="name">Your Name</label>
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" name="email" placeholder=" " required>
                            <label for="email">Your Email</label>
                        </div>
                        <div class="form-group">
                            <input type="text" id="subject" name="subject" placeholder=" " required>
                            <label for="subject">Subject</label>
                        </div>
                        <div class="form-group">
                            <textarea id="message" name="message" placeholder=" " required></textarea>
                            <label for="message">Your Message</label>
                        </div>
                        <center>
                        <button type="submit" class="submit-button">Send Message</button>
                        </center>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <section class="contact-section">
            <h1>Join Us </h1>
            <h1>Let's Build New Era Of Gaming</h1>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>