<?php
session_start();

// Check if the user is logged in and the video ID is provided
if (isset($_SESSION['username']) && isset($_POST['video_id'])) {
    $video_id = $_POST['video_id'];
    $username = $_SESSION['username'];

    // Fetch videos data
    $videos_file = 'data/videos.json';
    $videos = json_decode(file_get_contents($videos_file), true);
    if ($videos === null) {
        $videos = [];
    }

    // Find the video by ID
    foreach ($videos as $key => $video) {
        if ($video['id'] === $video_id) {
            // Check if the logged-in user is the uploader
            if ($video['uploader'] === $username) {
                // Delete the video from the array
                unset($videos[$key]);

                // Delete the video file from the server
                $video_path = 'uploads/videos/' . $video['filename'];
                if (file_exists($video_path)) {
                    unlink($video_path); // Remove the video file from the server
                }

                // Save the updated videos data to the JSON file
                file_put_contents($videos_file, json_encode(array_values($videos))); // reindex the array

                $_SESSION['message'] = "Video deleted successfully!";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "You can only delete your own videos.";
                header('Location: index.php');
                exit;
            }
        }
    }

    $_SESSION['error'] = "Video not found.";
    header('Location: index.php');
    exit;
} else {
    $_SESSION['error'] = "Invalid request.";
    header('Location: index.php');
    exit;
}
?>
