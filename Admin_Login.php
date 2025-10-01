<?php
session_start();

// --- Database Connection Settings ---
// NOTE: Make sure these details match your 'nexgenplayz_db' connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nexgenplayz_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize messages
$message = "";
$error_message = "";
$support_message_status = "";

// --- Handle Logout Action (Check first to clear session) ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: Login.php");
    exit();
}

// --- Handle Login POST Request (Only if not already logged in) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && !isset($_SESSION['admin_logged_in'])) {
    $admin_email = $_POST['email'];
    $admin_password = $_POST['password'];

    // Check the 'admins' table
    $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE email = ?");
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // IMPORTANT: Replace this with password_verify($admin_password, $row['password_hash']) 
        // if you use secure hashing (recommended).
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

// --- Admin Panel Logic (Runs only if logged in) ---
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    // --- General POST Handlers ---
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // --- 1. HANDLE ADD NEW GAME ---
        if (isset($_POST['add_game'])) {
            $game_title = $_POST['game_title'];
            $game_price = (float)$_POST['game_price']; // Cast to float for price
            
            // Define the upload directories relative to your file
            $cover_dir = "img/";
            $video_dir = "video/";
            
            $cover_image_path = "";
            $video_preview_path = "";
            
            // Handle image upload
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $cover_image_path = $cover_dir . basename($_FILES['cover_image']['name']);
                if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image_path)) {
                    $message = "Error uploading cover image.";
                }
            }
            
            // Handle video upload
            if (isset($_FILES['video_preview']) && $_FILES['video_preview']['error'] == 0) {
                $video_preview_path = $video_dir . basename($_FILES['video_preview']['name']);
                if (!move_uploaded_file($_FILES['video_preview']['tmp_name'], $video_preview_path)) {
                    $message = "Error uploading video preview.";
                }
            }
            
            if (!empty($cover_image_path) && !empty($video_preview_path)) {
                $stmt = $conn->prepare("INSERT INTO games (title, cover_image, video_preview, price) VALUES (?, ?, ?, ?)");
                // Use "sssd" (string, string, string, double) for binding
                $stmt->bind_param("sssd", $game_title, $cover_image_path, $video_preview_path, $game_price);
                
                if ($stmt->execute()) {
                    $message = "New game added successfully!";
                } else {
                    $message = "Error adding game: " . $stmt->error;
                }
                $stmt->close();
            } else if (empty($message)) { // Only show this if no specific file upload error occurred
                $message = "Please upload both an image and a video.";
            }
        }
        
        // --- 2. HANDLE DELETE GAME ---
        if (isset($_POST['delete_game'])) {
            $game_id = $_POST['game_id'];
        
            // Start a transaction for atomicity
            $conn->begin_transaction();
        
            try {
                // Delete related records from payments
                $stmt_payments = $conn->prepare("DELETE FROM payments WHERE game_id = ?");
                $stmt_payments->bind_param("i", $game_id);
                $stmt_payments->execute();
                $stmt_payments->close();
                
                // Delete related records from user_owned_games (game_id is VARCHAR in your DB, so use "s")
                $stmt_owned_games = $conn->prepare("DELETE FROM user_owned_games WHERE game_id = ?");
                $stmt_owned_games->bind_param("s", $game_id); 
                $stmt_owned_games->execute();
                $stmt_owned_games->close();
        
                // Delete the game itself
                $stmt_games = $conn->prepare("DELETE FROM games WHERE game_id = ?");
                $stmt_games->bind_param("i", $game_id);
                
                if ($stmt_games->execute()) {
                    $message = "Game and all related data deleted successfully!";
                    $conn->commit(); 
                } else {
                    throw new Exception("Error deleting game: " . $stmt_games->error);
                }
                $stmt_games->close();
        
            } catch (Exception $e) {
                $conn->rollback();
                $message = "Error: " . $e->getMessage();
            }
        }
        
        // --- 3. NEW: HANDLE ADD NEW ADMIN ---
        if (isset($_POST['add_admin'])) {
            $new_username = $_POST['new_username'];
            $new_email = $_POST['new_email'];
            $new_password = $_POST['new_password'];

            // In a production app, use $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $password_hash = $new_password; 

            $stmt = $conn->prepare("INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $new_username, $new_email, $password_hash);
            
            if ($stmt->execute()) {
                $message = "New administrator added successfully!";
            } else {
                $message = "Error adding admin: " . $stmt->error;
                if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                    $message = "Error adding admin: An admin with this email already exists.";
                }
            }
            $stmt->close();
        }

        // --- 4. NEW: HANDLE UPDATE GAME (from modal) ---
        if (isset($_POST['update_game'])) {
            $game_id = $_POST['game_id'];
            $game_title = $_POST['game_title'];
            $cover_image = $_POST['cover_image']; 
            $video_preview = $_POST['video_preview']; 
            $game_price = (float)$_POST['game_price']; 
            
            if (filter_var($game_id, FILTER_VALIDATE_INT) !== false && $game_id > 0) {
                $stmt = $conn->prepare("UPDATE games SET title = ?, cover_image = ?, video_preview = ?, price = ? WHERE game_id = ?");
                // Use "sssdi" (string, string, string, double, integer) for binding
                $stmt->bind_param("sssdi", $game_title, $cover_image, $video_preview, $game_price, $game_id);
                
                if ($stmt->execute()) {
                    $message = "Game updated successfully!";
                } else {
                    $message = "Error updating game: " . $stmt->error;
                }
                $stmt->close();
            } else {
                 $message = "Invalid Game ID for update.";
            }
        }
    }

    // --- 5. NEW: HANDLE DELETE MESSAGE ---
        if (isset($_POST['delete_message'])) {
            $message_id = $_POST['message_id'];
            
            // Validate that the ID is a positive integer
            if (filter_var($message_id, FILTER_VALIDATE_INT) !== false && $message_id > 0) {
                $stmt = $conn->prepare("DELETE FROM support_messages WHERE message_id = ?");
                $stmt->bind_param("i", $message_id); // "i" for integer
                
                if ($stmt->execute()) {
                $support_message_status = "Support message deleted successfully!"; // <--- This sets the status
            } else {
                $support_message_status = "Error deleting message: " . $stmt->error;
            }
                $stmt->close();
            } else {
                 // *** CHANGED VARIABLE HERE ***
                 $support_message_status = "Invalid Message ID for deletion.";
            }
        }
    
    // --- Data Fetching for Display ---
    
    // Fetch all games
    $games_result = $conn->query("SELECT * FROM games ORDER BY game_id ASC");
    $games = $games_result ? $games_result->fetch_all(MYSQLI_ASSOC) : [];
    if ($games_result) $games_result->free();
    
    // Fetch support messages for display
    $support_messages = [];
    $result_messages = $conn->query("SELECT * FROM support_messages ORDER BY submitted_at DESC");
    if ($result_messages) {
        while($row = $result_messages->fetch_assoc()) {
            $support_messages[] = $row;
        }
        $result_messages->free();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* --- Styles Section (Simplified for a single file) --- */
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
        
        .form-container.messages-table-container {
            text-align: left; /* Align table content to left */
        }
        
        h1 {
            color: #4acfee;
        }

        h2.section-title {
            color: #53f8c9;
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #444;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input:not([type='file']), .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #555;
            background-color: #3a3a3a;
            color: #d3d3d3;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .form-group input[type='file'] {
            width: 100%;
            padding: 10px 0;
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
        .games-table, .messages-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .games-table th, .games-table td, .messages-table th, .messages-table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        .games-table th, .messages-table th {
            background-color: #2a2a2a;
            color: #4acfee;
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
        .no-records {
            text-align: center;
            color: #999;
            padding: 15px;
        }
        .messages-table textarea {
            width: 100%;
            height: 80px;
            resize: vertical;
            background-color: #3a3a3a;
            border: 1px solid #555;
            color: #d3d3d3;
            padding: 5px;
            box-sizing: border-box;
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
            text-align: left;
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
        .modal-content .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #d3d3d3;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php
// --- Conditional HTML Display ---
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // --- Admin Dashboard Content ---
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
                    <label for="cover_image">Cover Image (Upload):</label>
                    <input type="file" name="cover_image" id="cover_image" required>
                </div>
                <div class="form-group">
                    <label for="video_preview">Video Preview (Upload):</label>
                    <input type="file" name="video_preview" id="video_preview" required>
                </div>
                <div class="form-group">
                    <input type="number" step="0.01" name="game_price" placeholder="Price (e.g., 59.99)" required>
                </div>
                <button type="submit" name="add_game" class="submit-button">Add Game</button>
            </form>
        </div>

        <div class="form-container">
            <h2 class="section-title">Add New Admin</h2>
            <form action="Admin_Login.php" method="POST">
                <div class="form-group">
                    <input type="text" name="new_username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="new_email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="new_password" placeholder="Password" required>
                </div>
                <button type="submit" name="add_admin" class="submit-button">Add Admin</button>
            </form>
        </div>

        <div class="form-container">
            <h2 class="section-title">Manage Games</h2>
            <?php
            if (!empty($games)) {
                echo '<table class="games-table">';
                echo '<thead><tr><th>ID</th><th>Title</th><th>Cover</th><th>Video</th><th>Price</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                foreach($games as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['game_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                    echo '<td><img src="' . htmlspecialchars($row['cover_image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="game-image"></td>';
                    echo '<td><video src="' . htmlspecialchars($row['video_preview']) . '" class="game-video" muted playsinline></video></td>';
                    echo '<td>$' . htmlspecialchars($row['price']) . '</td>';
                    echo '<td>';
                    // The data-* attributes pass current game info to the JavaScript for the modal
                    echo '<button class="edit-btn" data-id="' . htmlspecialchars($row['game_id']) . '" data-title="' . htmlspecialchars($row['title']) . '" data-cover="' . htmlspecialchars($row['cover_image']) . '" data-video="' . htmlspecialchars($row['video_preview']) . '" data-price="' . htmlspecialchars($row['price']) . '">Edit</button>';
                    echo '<form action="Admin_Login.php" method="POST" style="display:inline;" onsubmit="return confirm(\'Are you sure you want to delete this game and ALL its related payment/ownership records?\');">';
                    echo '<input type="hidden" name="game_id" value="' . htmlspecialchars($row['game_id']) . '">';
                    echo '<button type="submit" name="delete_game" class="delete-btn">Delete</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="no-records">No games found in the database.</p>';
            }
            ?>
        </div>

                <div class="form-container messages-table-container" id="support-messages">
                    <h2 class="section-title">View Support Messages</h2>
                    <?php 
                    // *** CHANGED VARIABLE HERE ***
                    if (!empty($support_message_status)): 
                    ?>
                    <p class="success-message"><?php echo htmlspecialchars($support_message_status); ?></p> 
                    <?php endif; ?>
        <?php
            if (!empty($support_messages)) {
                echo '<table class="messages-table">';
                echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Time</th><th>Action</th></tr></thead>';
                echo '<tbody>';
                foreach($support_messages as $message_row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($message_row['message_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($message_row['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($message_row['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($message_row['subject']) . '</td>';
                    // Display message in a scrollable text area
                    echo '<td><textarea readonly>' . htmlspecialchars($message_row['message']) . '</textarea></td>';
                    echo '<td>' . htmlspecialchars($message_row['submitted_at']) . '</td>';
                    echo '<td>'; // Start of 'Action' CELL
                    // Delete Form (NO onsubmit confirmation)
                    // New line:
                    echo '<form action="Admin_Login.php#support-messages" method="POST" style="display:inline;">';
                    echo '<input type="hidden" name="message_id" value="' . htmlspecialchars($message_row['message_id']) . '">';
                    echo '<button type="submit" name="delete_message" class="delete-btn">Delete</button>';
                    echo '</form>';
                    echo '</td>'; // End of 'Action' CELL
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="no-records">No support messages found.</p>';
            }
            ?>
        </div>
    </div>

    <div id="edit-game-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Edit Game Details</h2>
            <form action="Admin_Login.php" method="POST"> 
                <input type="hidden" name="game_id" id="edit-game-id">
                <div class="form-group">
                    <label for="edit-game-title">Game Title</label>
                    <input type="text" name="game_title" id="edit-game-title" placeholder="Game Title" required>
                </div>
                <div class="form-group">
                    <label for="edit-cover-image">Cover Image Path</label>
                    <input type="text" name="cover_image" id="edit-cover-image" placeholder="Cover Image Path (e.g., img/game.png)" required>
                </div>
                <div class="form-group">
                    <label for="edit-video-preview">Video Preview Path</label>
                    <input type="text" name="video_preview" id="edit-video-preview" placeholder="Video Preview Path (e.g., video/game.mp4)" required>
                </div>
                <div class="form-group">
                    <label for="edit-game-price">Price</label>
                    <input type="number" step="0.01" name="game_price" id="edit-game-price" placeholder="Price (e.g., 59.99)" required>
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
            
            // Get modal form fields
            const gameIdInput = document.getElementById('edit-game-id');
            const titleInput = document.getElementById('edit-game-title');
            const coverInput = document.getElementById('edit-cover-image');
            const videoInput = document.getElementById('edit-video-preview');
            const priceInput = document.getElementById('edit-game-price');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const gameData = this.dataset;
                    
                    // 1. Populate the modal fields with current game data
                    gameIdInput.value = gameData.id;
                    titleInput.value = gameData.title;
                    coverInput.value = gameData.cover;
                    videoInput.value = gameData.video;
                    priceInput.value = gameData.price; // Price is a number, passed as string

                    // 2. Display the modal
                    modal.style.display = 'block';
                });
            });

            // Close modal functions
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
        // --- Admin Login Form ---
        
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
?>
</body>
</html>