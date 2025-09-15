<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nexgenplayz_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to handle form submissions and display the page
function handle_and_display($conn) {
    $message = "";

    // Handle form submissions if an admin is logged in
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add_game'])) {
                $game_title = $_POST['game_title'];
                $cover_image = $_POST['cover_image'];
                $video_preview = $_POST['video_preview'];
                
                $stmt = $conn->prepare("INSERT INTO games (title, cover_image, video_preview) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $game_title, $cover_image, $video_preview);
                
                if ($stmt->execute()) {
                    $message = "New game added successfully!";
                } else {
                    $message = "Error adding game: " . $stmt->error;
                }
                $stmt->close();
            } elseif (isset($_POST['add_admin'])) {
                $admin_username = $_POST['admin_username'];
                $admin_email = $_POST['admin_email'];
                $admin_password = $_POST['admin_password'];

                // Hash the password before storing it
                $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $admin_username, $admin_email, $hashed_password);
                
                if ($stmt->execute()) {
                    $message = "New admin added successfully!";
                } else {
                    $message = "Error adding admin: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    // Start HTML output
    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>Admin Panel</title>';
    echo '<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">';
    echo '<style>';
    echo 'body {
        margin: 0;
        padding: 0;
        background-color: black;
        font-family: Arial, Helvetica, sans-serif;
        color: white;
    }
    .container {
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
    }
    .header {
        text-align: center;
        margin-bottom: 40px;
    }
    .header h1 {
        font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
        font-weight: 900;
        font-size: 80px;
        background: linear-gradient(to right, #4acfee, #53f8c9, #02d79a, #6070fb, #2a46ff, #0099ff, #4acfee);
        background-size: 200%;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: animate-gradient 2.5s linear infinite;
    }
    @keyframes animate-gradient {
        to {
            background-position: 200%;
        }
    }
    .section-title {
        font-family: Impact, Haettenschweiler, "Arial Narrow Bold", sans-serif;
        font-weight: 900;
        font-size: 40px;
        border-bottom: 2px solid #53f8c9;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    .form-container, .messages-container, .login-container {
        background-color: #1a1a1a;
        border: 1px solid gray;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 40px;
        width: 100%;
        max-width: 800px;
    }
    .login-container {
        max-width: 400px;
        text-align: center;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: lightgray;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 10px;
        background-color: #0f1217;
        border: 1px solid #4acfee;
        border-radius: 5px;
        color: white;
    }
    .submit-button {
        padding: 10px 20px;
        border: none;
        background: linear-gradient(to right, #4acfee, #53f8c9);
        color: #1a1a1a;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }
    .submit-button:hover {
        opacity: 0.8;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        color: white;
    }
    table, th, td {
        border: 1px solid gray;
    }
    th, td {
        padding: 15px;
        text-align: left;
    }
    th {
        background-color: #0f1217;
    }
    .success-message {
        color: green;
        margin-top: 10px;
    }
    .error-message {
        color: red;
        margin-top: 10px;
    }
    .logout {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 10px 20px;
        background-color: #ff4c4c;
        color: white;
        border-radius: 10px;
        text-decoration: none;
    }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<div class="container">';

    // Conditional content based on login status
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // Admin is logged in, show the dashboard
        echo '<a href="?logout" class="logout">Logout</a>';
        echo '<div class="header">';
        echo '<h1>Admin Panel</h1>';
        echo '</div>';

        if (!empty($message)) {
            echo '<p class="success-message">' . htmlspecialchars($message) . '</p>';
        }
        
        // Add Game Section
        echo '<div class="form-container">';
        echo '<h2 class="section-title">Add a New Game</h2>';
        echo '<form action="Admin_Login.php" method="POST">';
        echo '<div class="form-group">';
        echo '<label for="game_title">Game Title:</label>';
        echo '<input type="text" id="game_title" name="game_title" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="cover_image">Cover Image URL (e.g., img/game_cover.jpg):</label>';
        echo '<input type="text" id="cover_image" name="cover_image" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="video_preview">Video Preview URL (e.g., video/game_trailer.mp4):</label>';
        echo '<input type="text" id="video_preview" name="video_preview" required>';
        echo '</div>';
        echo '<button type="submit" name="add_game" class="submit-button">Add Game</button>';
        echo '</form>';
        echo '</div>';
        
        // Add Admin Section
        echo '<div class="form-container">';
        echo '<h2 class="section-title">Add a New Admin</h2>';
        echo '<form action="Admin_Login.php" method="POST">';
        echo '<div class="form-group">';
        echo '<label for="admin_username">Username:</label>';
        echo '<input type="text" id="admin_username" name="admin_username" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="admin_email">Email:</label>';
        echo '<input type="email" id="admin_email" name="admin_email" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<label for="admin_password">Password:</label>';
        echo '<input type="password" id="admin_password" name="admin_password" required>';
        echo '</div>';
        echo '<button type="submit" name="add_admin" class="submit-button">Add Admin</button>';
        echo '</form>';
        echo '</div>';

        // Support Messages Section
        echo '<div class="messages-container">';
        echo '<h2 class="section-title">Support Messages</h2>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Name</th>';
        echo '<th>Email</th>';
        echo '<th>Subject</th>';
        echo '<th>Message</th>';
        echo '<th>Submitted At</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $sql = "SELECT * FROM support_messages ORDER BY submitted_at DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['message_id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['subject']) . '</td>';
                echo '<td>' . htmlspecialchars($row['message']) . '</td>';
                echo '<td>' . htmlspecialchars($row['submitted_at']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">No support messages found.</td></tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

    } else {
        // Admin is not logged in, show the login form
        $error_message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $admin_email = $_POST['email'];
            $admin_password = $_POST['password'];

            $stmt = $conn->prepare("SELECT admin_id, password_hash FROM admins WHERE email = ?");
            $stmt->bind_param("s", $admin_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Use password_verify to check the password against the stored hash
               if ($row['password_hash'] === $admin_password) {
    $_SESSION['admin_logged_in'] = true;
    header("Location: Admin_Login.php");
    exit();
} else {
    $error_message = "Invalid password.";
}
            } else {
                $error_message = "Invalid email or you are not an administrator.";
            }
            $stmt->close();
        }
        
        echo '<div class="login-container">';
        echo '<h1>Admin Login</h1>';
        echo '<form action="Admin_Login.php" method="POST">';
        echo '<div class="form-group">';
        echo '<input type="email" name="email" placeholder="Admin Email" required>';
        echo '</div>';
        echo '<div class="form-group">';
        echo '<input type="password" name="password" placeholder="Password" required>';
        echo '</div>';
        echo '<button type="submit" class="submit-button">Log In</button>';
        echo '</form>';
        if (!empty($error_message)) {
            echo '<p class="error-message">' . htmlspecialchars($error_message) . '</p>';
        }
        echo '</div>';
    }

    echo '</div>';
    echo '</body>';
    echo '</html>';
}

// Handle logout action
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: Admin_Login.php");
    exit();
}

handle_and_display($conn);

$conn->close();
?>