<?php
// Start the session to use session variables
session_start();

// Include the database connection file.
// Assumes 'db_connect.php' contains the PDO connection object '$conn'
require 'db_connect.php'; 

// Check if a game_id is passed in the URL. If not, redirect the user back to the games page.
if (!isset($_GET['game_id'])) {
    header("Location: Games.php");
    exit();
}

// Fetch game details from the database using the game_id
$game_id = htmlspecialchars($_GET['game_id']);
$game_title = "Game Not Found";
$game_price = "0.00";

try {
    $stmt = $conn->prepare("SELECT title, price FROM games WHERE game_id = :game_id");
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        $game_title = htmlspecialchars($game['title']);
        $game_price = htmlspecialchars($game['price']);
    }

} catch (PDOException $e) {
    // In a real application, you would log this error and show a user-friendly message
    // For this project, we'll just set an error message.
    $game_title = "Error loading game details";
    $game_price = "N/A";
    error_log("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway - NexGenPlayz</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* General body and container styles from your project theme */
        body {
            margin: 0;
            padding: 0;
            background-color: black;
            scroll-behavior: smooth;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Header styles from your project theme */
        header {
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

        .head-left {
            display: flex;
            align-items: center;
        }

        .head-left img {
            width: 80px;
            height: 80px;
            margin-right: 20px;
        }

        .head-right a {
            text-decoration: none;
            padding-left: 25px;
            color: white;
            font-size: 15px;
        }

        .payment-section {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('img/contact-2.webp');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .payment-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 1;
        }

        .payment-card {
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 20px;
            padding: 40px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 0 25px rgba(0, 153, 255, 0.3);
            text-align: center;
            z-index: 2;
        }

        .payment-card h1 {
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-weight: 900;
            font-size: 50px;
            text-transform: uppercase;
            background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes animate-gradient {
            to {
                background-position: 200%;
            }
        }
        
        .payment-methods {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 20px;
        }

        .method-btn {
            background-color: #0f1217;
            border: 1px solid grey;
            color: gray;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 0 5px lightgray;
        }

        .method-btn:hover {
            box-shadow: 0 0 25px lightgray;
            opacity: 0.7;
        }

        .method-btn.active {
            box-shadow: 0 0 25px #0099ff;
            border-color: #0099ff;
            color: #fff;
            background-color: #2a46ff;
        }

        .form-container, .message-container {
            display: none;
            margin-top: 20px;
        }

        .form-container.active, .message-container.active {
            display: block;
        }

        .form-container input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            background-color: #2b2b2b;
            border: 1px solid #444;
            border-radius: 10px;
            color: white;
        }

        .form-container input:focus {
            outline: none;
            border-color: #0099ff;
            box-shadow: 0 0 10px #0099ff;
        }

        .form-container label {
            text-align: left;
            display: block;
            margin-top: 10px;
        }

        .form-container .input-group {
            display: flex;
            gap: 15px;
        }

        .form-container .input-group > div {
            flex: 1;
        }

        .submit-btn {
            background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
            background-size: 200%;
            color: black;
            border: none;
            padding: 15px 30px;
            margin-top: 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background-position: right center;
            box-shadow: 0 0 25px rgba(0, 153, 255, 0.7);
        }

        .message-container p {
            font-size: 1.2rem;
            line-height: 1.5;
            color: #ccc;
        }

        .price-display {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 20px;
            background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
        }

        /* Footer styles from your project theme */
        .footer {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(10px);
            height: 100px;
            margin-top: 50px;
        }

        .footer p {
            margin: 0 20px;
        }

        .footer ul {
            display: flex;
            list-style: none;
            margin-right: 140px;
        }

        .footer a {
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
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            header {
                padding: 10px 20px;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .head-left {
                width: 100%;
                justify-content: center;
            }

            .head-left img {
                width: 60px;
                height: 60px;
                margin-right: 0;
            }

            .head-right {
                flex-wrap: wrap;
                justify-content: center;
                width: 100%;
            }

            .head-right a {
                text-align: center;
                font-size: 12px;
                padding: 5px 8px;
            }

            .payment-card {
                padding: 20px;
            }

            .payment-card h1 {
                font-size: 30px;
            }

            .price-display {
                font-size: 1.5rem;
            }

            .payment-methods {
                flex-direction: column;
                gap: 10px;
            }

            .form-container .input-group {
                flex-direction: column;
                gap: 10px;
            }

            .footer {
                flex-direction: column;
                height: auto;
                padding: 20px 0;
            }

            .footer p {
                margin: 10px 0;
            }

            .footer ul {
                margin: 0;
                padding: 0;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="head-left">
                <img src="img/2.png" alt="logo">
            </div>
            <div class="head-right">
                <p><a href="Login.php">LOGIN</a></p>
                <p><a href="Home.php">HOME</a></p>
                <p><a href="Games.php">GAMES</a></p>
                <p><a href="About.php">ABOUT</a></p>
                <p><a href="Support.php">SUPPORT</a></p>
                <p><a href="Profile.php">PROFILE</a></p>
            </div>
        </header>

        <section class="payment-section">
            <div class="payment-card">
                <h1>Pay for Your Game</h1>
                <p>You are about to purchase **<?php echo htmlspecialchars($game_title); ?>** for</p>
                <div class="price-display">$<?php echo htmlspecialchars($game_price); ?></div>

                <div class="payment-methods">
                    <button class="method-btn active" data-method="debit">
                        <i class='bx bxs-credit-card-alt'></i> Debit Card
                    </button>
                    <button class="method-btn" data-method="other">
                        <i class='bx bxs-wallet-alt'></i> Other Methods
                    </button>
                </div>
                
                <div id="debit-form" class="form-container active">
    <form action="process_purchase.php" method="POST">
        <input type="hidden" name="game_id" value="<?php echo htmlspecialchars($game_id); ?>">
        <label for="card_number">Card Number</label>
        <input type="text" id="card_number" name="card_number" placeholder="0000 0000 0000 0000" required>
        
        <label for="card_holder">Card Holder Name</label>
        <input type="text" id="card_holder" name="card_holder" placeholder="Jhon Doe" required>

        <div class="input-group">
            <div>
                <label for="expiry">Expiry Date</label>
                <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
            </div>
            <div>
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" placeholder="123" required>
            </div>
        </div>
        
        <button type="submit" class="submit-btn">Pay $<?php echo htmlspecialchars($game_price); ?></button>
    </form>
</div>
                
                <div id="other-message" class="message-container">
                    <p>We are currently working on other payment methods.</p>
                    <p>It will take some time, thanks for your understanding!</p>
                </div>
            </div>
        </section>

        <?php include 'footer.php'; ?>
    </div>
    
    <script>
        const debitBtn = document.querySelector('.method-btn[data-method="debit"]');
        const otherBtn = document.querySelector('.method-btn[data-method="other"]');
        const debitForm = document.getElementById('debit-form');
        const otherMessage = document.getElementById('other-message');

        debitBtn.addEventListener('click', () => {
            debitBtn.classList.add('active');
            otherBtn.classList.remove('active');
            debitForm.classList.add('active');
            otherMessage.classList.remove('active');
        });

        otherBtn.addEventListener('click', () => {
            otherBtn.classList.add('active');
            debitBtn.classList.remove('active');
            otherMessage.classList.add('active');
            debitForm.classList.remove('active');
        });
    </script>
</body>
</html>
