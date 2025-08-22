<?php
// includes/header.php

// This line MUST be at the very top of the file to use sessions.
session_start();

// This is your website's main address.
// The '/' means it will work from the root of your domain.
$baseUrl = '/'; 
?>
<header>
    <div class="head-left">
        <img src="img/2.png" alt="logo">
    </div>

    <div class="head-right">
        <p><a href="<?php echo $baseUrl; ?>Home.php">HOME</a></p>
        <p><a href="<?php echo $baseUrl; ?>Games.php">GAMES</a></p>
        <p><a href="<?php echo $baseUrl; ?>About.php">ABOUT</a></p>
        <p><a href="<?php echo $baseUrl; ?>Support.php">SUPPORT</a></p>
        
        <?php 
        if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): 
        ?>
            <p><a href="<?php echo $baseUrl; ?>Profile.php">PROFILE</a></p>
            <p><a href="<?php echo $baseUrl; ?>logout.php">LOGOUT</a></p>
        <?php else: ?>
            <p><a href="<?php echo $baseUrl; ?>Login.php">LOGIN</a></p>
        <?php endif; ?>

    </div>
</header>