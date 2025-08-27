<?php
session_start();
require 'db_connect.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: Login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$status_message = '';

// Handle the form submission to update the profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $update_query = "UPDATE users SET username = :username WHERE user_id = :user_id";

    try {
        // Handle profile picture upload
        if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $upload_dir = 'uploads/profile_photos/';

            // Create the directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = uniqid('profile_') . '.' . pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                // Get the old profile picture path to delete it later
                $old_pic_query = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = :user_id");
                $old_pic_query->bindParam(':user_id', $user_id);
                $old_pic_query->execute();
                $old_profile_pic = $old_pic_query->fetchColumn();

                // If an old picture exists and is not the default, delete it
                if ($old_profile_pic && file_exists($old_profile_pic) && !str_contains($old_profile_pic, 'default')) {
                    unlink($old_profile_pic);
                }

                $update_query = "UPDATE users SET username = :username, profile_picture = :profile_picture WHERE user_id = :user_id";
            }
        }

        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':username', $new_username);
        $stmt->bindParam(':user_id', $user_id);

        if (isset($destination)) {
            $stmt->bindParam(':profile_picture', $destination);
        }

        $stmt->execute();

        // Update the session to reflect the change
        $_SESSION['username'] = $new_username;

        // Redirect back to profile page with a success message
        header("Location: Profile.php?status=success");
        exit;

    } catch(PDOException $e) {
        $status_message = "Error: " . $e->getMessage();
    }
}

// Fetch user data for displaying on the page
$stmt_user = $conn->prepare("SELECT username, tagline, profile_picture FROM users WHERE user_id = :user_id");
$stmt_user->bindParam(':user_id', $user_id);
$stmt_user->execute();
$user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Check if a profile picture exists, otherwise use a default one
$profile_pic = $user_data['profile_picture'] ?? 'img/default-profile-photo.png';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        /* This CSS is only for the edit form */
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .edit-profile-container {
            width: 100%;
            max-width: 500px;
            background-color: #1a1a1a;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .edit-profile-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        .user-profile-img-form {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #4acfee;
            cursor: pointer;
        }
        .user-profile-img-form img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .file-input-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            text-align: center;
            padding: 5px 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .user-profile-img-form:hover .file-input-container {
            opacity: 1;
        }
        .file-input-container label {
            cursor: pointer;
            font-size: 14px;
        }
        .file-input-container input[type="file"] {
            display: none;
        }
        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            color: #4acfee;
        }
        .form-group input {
            width: 95%;
            background: #333;
            border: 1px solid #555;
            padding: 10px;
            color: white;
            border-radius: 5px;
        }
        .submit-button, .cancel-button {
            padding: 10px 20px;
            background-color: #4acfee;
            color: black;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .submit-button:hover, .cancel-button:hover {
            background-color: #53f8c9;
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h1>Edit Your Profile</h1>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
            <div class="user-profile-img-form">
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
                <div class="file-input-container">
                    <label for="profile_photo">Change Photo</label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>
            <button type="submit" class="submit-button">Save Changes</button>
            <a href="Profile.php" class="cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>