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

// Handle login POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $admin_email = $_POST['email'];
    $admin_password = $_POST['password'];

    // Corrected SQL query to check the 'admins' table
    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE email = ?");
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
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

// Handle form submissions if an admin is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['add_game'])) {
            $game_title = $_POST['game_title'];
            $game_price = $_POST['game_price'];
            
            // Define the upload directories relative to your file
            $cover_dir = "img/";
            $video_dir = "video/";
            
            // Handle image upload
            $cover_image_path = "";
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $cover_image_path = $cover_dir . basename($_FILES['cover_image']['name']);
                if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
                    $message = "Error uploading cover image.";
                }
            }
            
            // Handle video upload
            $video_preview_path = "";
            if (isset($_FILES['video_preview']) && $_FILES['video_preview']['error'] == 0) {
                $video_preview_path = $video_dir . basename($_FILES['video_preview']['name']);
                if (!move_uploaded_file($_FILES['video_preview']['tmp_name'], $video_preview_path)) {
                    $message = "Error uploading video preview.";
                }
            }
            
            // Check if both files were uploaded successfully before inserting into the database
            if ($cover_image_path !== "" && $video_preview_path !== "") {
                $stmt = $conn->prepare("INSERT INTO games (title, cover_image, video_preview, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssd", $game_title, $cover_image_path, $video_preview_path, $game_price);
                
                if ($stmt->execute()) {
                    $message = "New game added successfully!";
                } else {
                    $message = "Error adding game: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "Please upload both an image and a video.";
            }
        }
        
        // --- START OF UPDATED CODE BLOCK ---
        if (isset($_POST['delete_game'])) {
            $game_id = $_POST['game_id'];
        
            // Start a transaction to ensure atomicity
            $conn->begin_transaction();
        
            try {
                // First, delete related records from the payments table
                $stmt_payments = $conn->prepare("DELETE FROM payments WHERE game_id = ?");
                $stmt_payments->bind_param("i", $game_id);
                $stmt_payments->execute();
                $stmt_payments->close();
        
                // Then, delete the game from the games table
                $stmt_games = $conn->prepare("DELETE FROM games WHERE game_id = ?");
                $stmt_games->bind_param("i", $game_id);
                
                if ($stmt_games->execute()) {
                    $message = "Game and related payments deleted successfully!";
                    $conn->commit(); // Commit the transaction
                } else {
                    throw new Exception("Error deleting game: " . $stmt_games->error);
                }
                $stmt_games->close();
        
            } catch (Exception $e) {
                // An error occurred, rollback the transaction
                $conn->rollback();
                $message = "Error: " . $e->getMessage();
            }
        }
        // --- END OF UPDATED CODE BLOCK ---
    }
}

// Handle logout action
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Admin_Login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #1a1a1a;
            color: #d3d3d3;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .login-container, .form-container {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 1px solid #444;
        }

        h1 {
            color: #4acfee;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input, .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #555;
            background-color: #3a3a3a;
            color: #d3d3d3;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .form-group input:focus, .form-group textarea:focus {
            border-color: #4acfee;
            box-shadow: 0 0 8px rgba(74, 207, 238, 0.5);
            outline: none;
        }

        .submit-button {
            background-color: #4acfee;
            color: #1a1a1a;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #53f8c9;
        }

        .error-message {
            color: #ff4d4d;
            font-weight: bold;
            margin-top: 10px;
        }

        .success-message {
            color: #53f8c9;
            font-weight: bold;
            margin-top: 10px;
        }

        .logout-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff4d4d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .logout-link:hover {
            background-color: #ff3333;
        }
        
        /* Table Styles */
        .games-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .games-table th, .games-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        .games-table th {
            background-color: #2a2a2a;
        }
        .games-table img.game-image {
            width: 80px;
            height: auto;
            border-radius: 5px;
        }
        .games-table video.game-video {
            width: 120px;
            height: auto;
            border-radius: 5px;
        }
        .edit-btn, .delete-btn {
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            margin-right: 5px;
        }
        .edit-btn {
            background-color: #4acfee;
            color: black;
        }
        .delete-btn {
            background-color: #ff4d4d;
            color: white;
        }
        .no-games {
            text-align: center;
            color: #999;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.7);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #1a1a1a;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 20px;
            position: relative;
            box-shadow: 0 0 25px rgba(211, 211, 211, 0.5);
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close-btn:hover, .close-btn:focus {
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // Display admin panel content
?>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <a href="?logout=true" class="logout-link">Log Out</a>

        <?php if (!empty($message)): ?>
            <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="form-container">
    <h2 class="section-title">Add New Game</h2>
    <form action="Admin_Login.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <input type="text" name="game_title" placeholder="Game Title" required>
        </div>
        <div class="form-group">
            <label for="cover_image">Cover Image:</label>
            <input type="file" name="cover_image" id="cover_image" required>
        </div>
        <div class="form-group">
            <label for="video_preview">Video Preview:</label>
            <input type="file" name="video_preview" id="video_preview" required>
        </div>
        <div class="form-group">
            <input type="text" name="game_price" placeholder="Price (e.g., 59.99)" required>
        </div>
        <button type="submit" name="add_game" class="submit-button">Add Game</button>
    </form>
</div>


        <div class="form-container">
            <h2 class="section-title">Manage Games</h2>
            <?php
            $result = $conn->query("SELECT * FROM games ORDER BY game_id ASC");

            if ($result->num_rows > 0) {
                echo '<table class="games-table">';
                echo '<thead><tr><th>ID</th><th>Title</th><th>Cover</th><th>Video</th><th>Price</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                while($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['game_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                    echo '<td><img src="' . htmlspecialchars($row['cover_image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="game-image"></td>';
                    echo '<td><video src="' . htmlspecialchars($row['video_preview']) . '" class="game-video" muted plays-inline></video></td>';
                    echo '<td>$' . htmlspecialchars($row['price']) . '</td>';
                    echo '<td>';
                    echo '<button class="edit-btn" data-id="' . htmlspecialchars($row['game_id']) . '" data-title="' . htmlspecialchars($row['title']) . '" data-cover="' . htmlspecialchars($row['cover_image']) . '" data-video="' . htmlspecialchars($row['video_preview']) . '" data-price="' . htmlspecialchars($row['price']) . '">Edit</button>';
                    echo '<form action="Admin_Login.php" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this game?\');">';
                    echo '<input type="hidden" name="game_id" value="' . htmlspecialchars($row['game_id']) . '">';
                    echo '<button type="submit" name="delete_game" class="delete-btn">Delete</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="no-games">No games found in the database.</p>';
            }
            ?>
        </div>
    </div>

    <div id="edit-game-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Game</h2>
            <form action="Admin_Login.php" method="POST">
                <input type="hidden" name="game_id" id="edit-game-id">
                <div class="form-group">
                    <input type="text" name="game_title" id="edit-game-title" placeholder="Game Title" required>
                </div>
                <div class="form-group">
                    <input type="text" name="cover_image" id="edit-cover-image" placeholder="Cover Image Path" required>
                </div>
                <div class="form-group">
                    <input type="text" name="video_preview" id="edit-video-preview" placeholder="Video Preview Path" required>
                </div>
                <div class="form-group">
                    <input type="text" name="game_price" id="edit-game-price" placeholder="Price (e.g., 59.99)" required>
                </div>
                <button type="submit" name="update_game" class="submit-button">Update Game</button>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('edit-game-modal');
            const closeBtn = document.querySelector('.close-btn');
            const editButtons = document.querySelectorAll('.edit-btn');
            const gameIdInput = document.getElementById('edit-game-id');
            const titleInput = document.getElementById('edit-game-title');
            const coverInput = document.getElementById('edit-cover-image');
            const videoInput = document.getElementById('edit-video-preview');
            const priceInput = document.getElementById('edit-game-price');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const gameData = this.dataset;
                    gameIdInput.value = gameData.id;
                    titleInput.value = gameData.title;
                    coverInput.value = gameData.cover;
                    videoInput.value = gameData.video;
                    priceInput.value = gameData.price;
                    modal.style.display = 'block';
                });
            });

            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
<?php
    } else {
        // Display admin login form
        $error_message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
            $admin_email = $_POST['email'];
            $admin_password = $_POST['password'];
            
            $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE email = ?");
            $stmt->bind_param("s", $admin_email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
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

    $conn->close();

    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: Admin_Login.php");
        exit();
    }
?>
</body>
</html>