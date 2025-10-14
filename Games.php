<?php
session_start();
require 'db_connect.php'; // Ensure this file exists and connects to your database

// Initialize the $games variable as an empty array
$games = [];

// Fetch all games from the database
try {
    $stmt = $conn->prepare("SELECT * FROM games ORDER BY game_id ASC");
    $stmt->execute();
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there's an error, log it and the $games variable remains an empty array.
    error_log("Database Error: " . $e->getMessage());
}

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGenPlayz</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
body {
    margin: 0;
    padding: 0;
    background-color: black;
    scroll-behavior: smooth;
}

.container {
    font-family: Arial, Helvetica, sans-serif;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

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

.head-left {
    display: flex;
    align-items: center;
}

.head-left img {
    width: 80px;
    height: 80px;
    margin-right: 20px;
}

.head-left button {
    border: none;
    padding: 10px 30px;
    border-radius: 20px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}

.head-left button:hover {
    opacity: 0.5;
}

.nav-links {
    display: flex;
}

.nav-links a {
    text-decoration: none;
    padding-left: 25px;
    color: white;
    font-size: 15px;
}

.head-section {
    position: relative;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}

@keyframes animate-gradient {
    to {
        background-position: 200%;
    }
}

.info-section {
    display: flex;
    flex-direction: column;
    position: relative;
    width: 80%;
    margin-top: 100px;
}

.info-section h1 {
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
    font-weight: 900;
    font-size: 100px;
    text-align: center;
    margin: 25px 0;
    text-transform: uppercase;
    background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
    background-size: 200%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: animate-gradient 2.5s linear infinite;
}

.info-section h3 {
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
    font-size: 50px;
    text-align: center;
    color: gray;
    background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
    background-size: 200%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: animate-gradient 2.5s linear infinite;
}

.info-card {
    display: grid;
    grid-template-columns: auto auto;
    gap: 25px;
    width: 100%;
    height: 100%;
    margin-top: 30px;
}

.card {
    position: relative;
    width: auto;
    height: 41vh;
    overflow: hidden;
    border: 1px solid gray;
    border-radius: 20px;
    transition: 0.5s;
}

.card h1 {
    position: absolute;
    margin: 0;
    top: 10px;
    left: 5%;
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
    font-size: 40px;
    z-index: 1;
    background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
    background-size: 200%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: animate-gradient 2.5s linear infinite;
    max-width: 300px;
}

.card p {
    position: absolute;
    top: 80px;
    left: 5%;
    z-index: 1;
    max-width: 250px;
    color: white;
}

.card video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card button {
    text-decoration: none;
    padding-left: 25px;
    color: white;
    font-size: 15px;
    position: absolute;
    bottom: 15px;
    left: 21px;
    padding: 10px 30px;
    border: 1px solid grey;
    background-color: #0f1217;
    color: gray;
    border-radius: 20px;
    box-shadow: 0 0 5px lightgray;
    cursor: pointer;
    transition: 0.3s;
}

.card button a{
    color: green;
}

.card button:hover {
    box-shadow: 0 0 25px lightgray;
    opacity: 0.75;
}

.card:hover {
    box-shadow: 0 0 25px rgb(211, 211, 211);
}

.card:nth-child(1) {
    grid-column: span 2;
}

.contact-section {
    position: relative;
    width: 100%;
    margin-top: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    margin-bottom: 100px;
}

.contact-section .img1 {
    position: absolute;
    top: 10%;
    right: 100px;
    height: 400px;
    clip-path: polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%);
}

.contact-section .img2 {
    position: absolute;
    top: 10px;
    left: 50px;
    height: 200px;
    clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
}

.contact-section .img3 {
    position: absolute;
    left: 3.5%;
    bottom: -6%;
    height: 250px;
    width: 250px;
    clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
}

.contact-section p {
    margin-top: 100px;
}

.contact-section h1 {
    font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
    font-size: 70px;
    max-width: 550px;
    text-align: center;
    background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
    background-size: 200%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: animate-gradient 2.5s linear infinite;
}

.contact-section button {
    padding: 10px 25px;
    border: 1px solid grey;
    background-color: #0f1217;
    color: gray;
    border-radius: 20px;
    box-shadow: 0 0 5px lightgray;
    cursor: pointer;
    transition: 0.3s;
}

.contact-section button:hover {
    box-shadow: 0 0 25px lightgray;
    opacity: 0.7;
}

.footer {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    backdrop-filter: blur(10px);
    height: 100px;
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

.stone-img {
    position: absolute;
    margin-top: 22%;
    width: 500px;
}

.footer a:hover {
    background-color: rgba(0, 130, 211, 0.3);
    box-shadow: 0 0 10px rgba(0, 130, 211, 0.7);
}

/*Card Animations*/

.autoDisplay {
    animation: autoDisplayAnimation both;
    animation-timeline: view(70% 5%);
}

@keyframes autoDisplayAnimation {
    from {
        opacity: 0;
        transform: translateY(200px) scale(0.3);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Mobile Responsiveness */

/* Header adjustments for smaller screens */

/* Info Section adjustments */
@media (max-width: 768px) {
    .info-section {
        width: 90%; /* Increase width for better use of space */
        margin-top: 50px;
    }

    .info-section h1 {
        margin-top: 100px;
        font-size: 60px; /* Smaller heading for readability */
    }

    .info-section h3 {
        font-size: 30px; /* Smaller sub-heading */
    }

    .info-card {
        grid-template-columns: 1fr; /* Single column layout for cards */
        gap: 20px;
    }

    .card:nth-child(1) {
        grid-column: span 1; /* Ensure the first card takes full width */
    }

    .card {
        height: 30vh; /* Adjust card height */
    }

    .card h1 {
        font-size: 30px; /* Smaller card title */
        top: 5%; /* Adjust position */
    }

    .card p {
        font-size: 14px; /* Smaller paragraph text */
        top: 25%; /* Adjust position */
        max-width: 90%;
    }

    .card button {
        bottom: 10px;
        left: 5%;
        padding: 8px 20px;
    }
}

/* Contact Section adjustments */
@media (max-width: 768px) {
    .contact-section {
        margin-top: 50px;
        margin-bottom: 50px;
        padding: 0 20px; /* Add some horizontal padding */
    }

    .contact-section h1 {
        font-size: 40px; /* Smaller contact heading */
        max-width: 90%;
    }
}

/* Footer adjustments */
@media (max-width: 768px) {
    .footer {
        flex-direction: column; /* Stack footer items */
        height: auto;
        padding: 20px 0;
        text-align: center;
    }

    .footer p {
        margin: 10px 0;
        font-size: 14px;
    }

    .footer ul {
        margin-right: 0;
        padding: 0;
        justify-content: center; /* Center social icons */
        flex-wrap: wrap; /* Allow icons to wrap if needed */
    }

    .footer a {
        margin: 5px; /* Adjust spacing between icons */
    }
}

/* Further refinements for very small screens (e.g., iPhone SE) */
@media (max-width: 480px) {
    .info-section h1 {
        font-size: 45px;
    }

    .info-section h3 {
        font-size: 25px;
    }

    .card h1 {
        font-size: 25px;
    }

    .contact-section h1 {
        font-size: 30px;
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
            
        <section class="info-section">
            <h1>Welcome To GamePage</h1>
            <h3>Soo Many Games To Choose !</h3>

            <div class="info-card">

                <div class="card autoDisplay">
                    <h1>Top</h1>
                    <video src="video/feature-1.mp4" autoplay loop muted plays-inline></video>
                </div>
                
                <?php foreach ($games as $game): ?>
                    <div class="card autoDisplay">
                    <h1><?php echo htmlspecialchars($game['title']); ?></h1>
                    <video src="<?php echo htmlspecialchars($game['video_preview']); ?>" autoplay loop muted plays-inline></video>
                    <button><a href="PaymentGateway.php?game_id=<?php echo htmlspecialchars($game['game_id']); ?>">$<?php echo htmlspecialchars($game['price']); ?></a></button>
                    </div>
                <?php endforeach; ?>
                <div class="card">
                    <h1>Many More Coming Soon !</h1>
                </div>
              
        </div>
            
        </section>

        <section class="contact-section">
                <h1>Join Us </h1>
            <div class="autoBlur">
                <h1>Let's Build New Era Of Gaming</h1>
            </div>

        </section>
<?php include 'footer.php'; ?>
    </div>
    
</body>
</html>