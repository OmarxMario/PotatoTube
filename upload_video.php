<?php
session_start();
include('includes/header.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You need to be logged in to upload a video.";
    header('Location: login.php');
    exit;
}

$current_user = $_SESSION['username']; // Get the logged-in user's username

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    $video = $_FILES['video'];
    $video_description = $_POST['description'];

    // Check if there was an error during the upload
    if ($video['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "There was an error uploading your file. Error code: " . $video['error'];
        header('Location: upload_video.php');
        exit;
    }

    // Define the target directory and file path
    $target_dir = "uploads/videos/";
    $target_file = $target_dir . basename($video["name"]);

    // Check if the file already exists
    if (file_exists($target_file)) {
        $_SESSION['error'] = "Sorry, file already exists.";
        header('Location: upload_video.php');
        exit;
    }

    // Check file size (e.g., max 50MB)
    if ($video['size'] > 50000000) {
        $_SESSION['error'] = "Sorry, your file is too large.";
        header('Location: upload_video.php');
        exit;
    }

    // Check file type (e.g., mp4 only)
    $videoFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($videoFileType != "mp4") {
        $_SESSION['error'] = "Sorry, only MP4 files are allowed.";
        header('Location: upload_video.php');
        exit;
    }

    // Move the uploaded file to the server
    if (move_uploaded_file($video["tmp_name"], $target_file)) {
        // If the upload is successful, update the user's profile with the new video
        $users_file = 'data/users.json';
        $users = json_decode(file_get_contents($users_file), true);

        // Find the user and add the uploaded video to their profile
        foreach ($users as &$user) {
            if ($user['username'] === $current_user) {
                // Add video data to the user's profile
                $new_video = [
                    'id' => uniqid(),
                    'filename' => $video["name"],
                    'description' => $video_description,
                ];
                $user['videos'][] = $new_video;
                break;
            }
        }

        // Save the updated users data back to the JSON file
        file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));

        // Redirect the user to their profile after successful upload
        header("Location: profile.php?username=" . $current_user);
        exit;
    } else {
        $_SESSION['error'] = "Sorry, there was an error uploading your file.";
        header('Location: upload_video.php');
        exit;
    }
}
?>

<h2>Upload Video</h2>

<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
?>

<form action="upload_video.php" method="POST" enctype="multipart/form-data">
    <label for="video">Choose Video:</label>
    <input type="file" name="video" id="video" required>
    <br>
    <label for="description">Video Description:</label>
    <textarea name="description" id="description" required></textarea>
    <br>
    <button type="submit">Upload Video</button>
</form>
