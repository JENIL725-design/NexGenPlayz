<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body{
            margin: 0;
            padding: 0;
            background-color: black;
            scroll-behavior: smooth;
        }

        .container{
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
        }

        @keyframes animate-gradient{
            to{
                background-position: 200%;
            }
        }

        .about-section{
            display: flex;
            flex-direction: column;
            position: relative;
            width: 80%;
            margin-top: 150px; /* Adjusted margin for header */
            margin-bottom: 100px;
            align-items: center;
            text-align: center;
        }

        .about-section h1{
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

        .about-section h3{
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

        .about-content{
            display: flex;
            flex-direction: column;
            gap: 40px;
            max-width: 900px;
            line-height: 1.8;
            font-size: 18px;
            text-align: justify;
        }

        .about-content p {
            color: lightgray;
        }

        .about-card-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            width: 100%;
            margin-top: 80px;
        }

        .about-card{
            background-color: #1a1a1a;
            border: 1px solid gray;
            border-radius: 20px;
            padding: 30px;
            text-align: left;
            transition: 0.5s;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .about-card:hover{
            box-shadow: 0 0 25px rgb(211, 211, 211);
        }

        .about-card h2{
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
            font-weight: 900;
            font-size: 35px;
            margin-top: 0;
            background: linear-gradient(to right, #4acfee , #53f8c9 , #02d79a , #6070fb ,#2a46ff , #0099ff , #4acfee );
            background-size: 200%;
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: animate-gradient 2.5s linear infinite;
        }

        .about-card p{
            color: lightgray;
            font-size: 16px;
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
        .footer{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(10px);
            height: 100px;
        }

        .footer p{
            margin: 0 20px;
        }

        .footer a:hover {
            background-color: rgba(0, 130, 211, 0.3);
            box-shadow: 0 0 10px rgba(0, 130, 211, 0.7);
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
        /* Mobile Responsiveness for About.html */

/* Header adjustments already present and good */
@media (max-width: 900px) {
    header {
        padding: 10px 20px;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .head-left {
        width: 100%;
        justify-content: left;
    }

    .head-left img {
        width: 60px;
        height: 60px;
        margin-right: 0;
    }

    .nav-links {
        font-size: 12px;
        padding: 0 5px;
        /* Ensure head-right remains visible and adjusts */
        display: flex; /* Ensure it's not hidden if a previous rule set display:none */
        flex-wrap: wrap; /* Allow links to wrap */
        justify-content: center; /* Center links if they wrap */
        width: 100%; /* Take full width to allow wrapping */
    }
    .nav-links a{
        text-align: center; /* Center text within links */
        font-size: 12px;
        padding: 5px 8px; /* Adjust padding for smaller links */
    }
}


/* About Section adjustments */
@media (max-width: 768px) {
    .about-section {
        width: 90%; /* Increase width for better use of space */
        margin-top: 100px; /* Adjust top margin to account for stacked header */
        margin-bottom: 50px;
    }

    .about-section h1 {
        font-size: 50px; /* Smaller heading for readability */
        line-height: 1.2; /* Adjust line height for better readability */
    }

    .about-section h3 {
        font-size: 28px; /* Smaller sub-heading */
        margin-bottom: 30px;
    }

    .about-content {
        gap: 20px; /* Reduce gap between paragraphs */
        font-size: 16px; /* Slightly smaller font for paragraphs */
        text-align: left; /* Keep justified, but left align if justified looks weird on narrow screens */
    }

    .about-card-grid {
        grid-template-columns: 1fr; /* Single column layout for cards */
        gap: 20px; /* Adjust gap between stacked cards */
        margin-top: 50px;
    }

    .about-card {
        padding: 20px; /* Adjust card padding */
    }

    .about-card h2 {
        font-size: 28px; /* Smaller card title */
    }

    .about-card p {
        font-size: 14px; /* Smaller card paragraph text */
    }
}

/* Further refinements for very small screens (e.g., iPhone SE) */
@media (max-width: 480px) {
    .about-section h1 {
        font-size: 35px;
    }

    .about-section h3 {
        font-size: 20px;
    }

    .about-content {
        font-size: 14px;
    }

    .about-card h2 {
        font-size: 24px;
    }

    .about-card p {
        font-size: 13px;
    }
}

/* Footer adjustments for mobile */
@media (max-width: 768px) {
    .footer {
        flex-direction: column; /* Stack footer items vertically */
        height: auto; /* Auto height to accommodate stacked content */
        padding: 20px 0; /* Add vertical padding */
        text-align: center;
    }

    .footer p {
        margin: 10px 0; /* Adjust margin for stacked paragraphs */
        font-size: 14px;
    }

    .footer ul {
        margin-right: 0; /* Remove right margin */
        padding: 0; /* Remove default list padding */
        justify-content: center; /* Center the social icons */
        flex-wrap: wrap; /* Allow icons to wrap if space is tight */
    }

    .footer a {
        margin: 5px; /* Adjust spacing between social icons */
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
        <section class="about-section">
            <h1 class="autoDisplay">NexGenPlayz The Best Choice for Gaming!</h1>
            <h3 class="autoDisplay">Enhance Your New Gaming Experience With Us! </h3>

            <div class="about-content">
                <p class="autoDisplay">Welcome to NexGenPlayz, truly the best gaming site you'll ever find. We don't just say we're good; we're the top choice for gamers who want awesome experiences. We always try new things to make gaming better for everyone, everywhere. Our goal is simple: to make sure you have the most fun and easy gaming time possible, every single time you visit.</p>

                <p class="autoDisplay">Why are we the best? It's because of our super-fast setup. We have special tech that makes games run smoothly with almost no delay, no matter where you are in the world. Imagine playing "Apex Legends" or exploring "GTA VI" and it feels just like it's right there in front of you, with no hiccups or annoying pauses. We have game servers placed all over, from big cities to faraway places, to make sure your connection is always perfect. No other site can give you this kind of fast, smooth gaming!</p>

                <p class="autoDisplay">But we're more than just fast tech. Our game list is amazing. We don't just have many games; we have the top games ever made. You can play special games like "Cybernetic Odyssey" and "BlackMyth Wukong" only here first! We pick only the best games, from action-packed "Call of Duty: Black Ops 6" to story-rich "Assassin's Creed Brotherhood." Every game is chosen because it's super fun and looks great. This isn't just playing games; it's stepping into a fantastic digital world where every adventure is waiting for you.</p>

                <p class="autoDisplay">We care a lot about how games perform. Our system is made to give you the best speed and amazing graphics, even for really big games like "Forza 5" and "ILL." This means your games will always look stunning and play without any issues. We use smart ways to show off every little detail, making sure you see the games exactly how the people who made them wanted you to see them. This focus on perfect performance is why we are the best choice for gaming anywhere.</p>

                <p class="autoDisplay">Choosing games for our library is a serious job. We don't just throw games on our site; we carefully pick only the best. This means our collection isn't just big, it's full of gaming masterpieces. You'll find popular favorites and games that have changed how we play, like "JUST CAUSE 4" and "Sekiro." Each game on NexGenPlayz is there because it offers an incredible experience, confirming our spot as the ultimate place for all your gaming needs.</p>

                <p class="autoDisplay">We are always thinking about what's next in gaming. Our team is always working on new ideas and amazing technology that will change how games are played in the future. We're building special tools and features that no one else has. This means NexGenPlayz is not just ready for the future; we are creating it, making sure we're always ahead of everyone else. When you join us, you're becoming part of the future of gaming, happening right now.</p>

                <p class="autoDisplay">So, if you want the very best in how games play, the best games to choose from, and a great community to be a part of, NexGenPlayz is the only place for you. We're more than just a site; we are a promise of awesome gaming. Join us and you'll quickly see why millions of players agree: for the top gaming experience, there's no better choice. We are the best of the best, and you deserve nothing less!</p>
            </div>

            <div class="about-card-grid">
                <div class="about-card autoDisplay">
                    <h2>Super Fast, Everywhere!</h2>
                    <p>Our site works perfectly, giving you the best gaming no matter where you are. Games like "Forza 5" and "ILL" run smoothly and look great, so you never miss a beat. We make sure your game always feels quick and real, solidifying our status as the best gaming platform anywhere in the universe. You'll truly feel the speed!</p>
                </div>
                <div class="about-card autoDisplay">
                    <h2>Only the Best Games</h2>
                    <p>Our list of games is unmatched. We don't just have many games; we have the top games ever made, carefully chosen just for you. Play special titles and popular hits like "JUST CAUSE 4" and "Sekiro" that make us the ultimate place for gaming fun. Every game is a winner, promising hours of excitement and adventure.</p>
                </div>
                <div class="about-card autoDisplay">
                    <h2>Always New and Exciting</h2>
                    <p>We are always working on new ideas and tech to make gaming even better. NexGenPlayz is always ahead, making sure you get the newest and most exciting gaming experiences first. We love to surprise you with cool new features and games that you won't find anywhere else. The future of gaming starts here!</p>
                </div>
                <div class="about-card autoDisplay">
                    <h2>The Only Pick for True Gamers</h2>
                    <p>If you want the very best in how games play, what games you can play, and how you connect with other gamers, NexGenPlayz is your perfect match. We are not just a service; we are the clear champion of gaming. Join us and see why millions of players agree: for the ultimate gaming experience, there's simply no other choice.</p>
                </div>
            </div>
        </section>
<?php include 'footer.php'; ?>
    </div>
</body>
</html>