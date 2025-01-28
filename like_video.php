<?php
session_start();

// Check if the like form was submitted
if (isset($_POST['video_id'])) {
    $video_id = $_POST['video_id'];
    $username = $_SESSION['username']; // Assuming the user is logged in

    // Fetch videos data
    $videos_file = 'data/videos.json';
    $videos = json_decode(file_get_contents($videos_file), true);
    if ($videos === null) {
        $videos = [];
    }

    // Find the video by ID
    foreach ($videos as &$video) {
        if ($video['id'] === $video_id) {
            // Ensure 'liked_by' is initialized as an array if it's not set
            if (!isset($video['liked_by'])) {
                $video['liked_by'] = [];
            }

            // Check if the user has already liked the video
            if (in_array($username, $video['liked_by'])) {
                $_SESSION['error'] = "You have already liked this video.";
                header('Location: index.php');
                exit;
            }

            // Add the user's like
            $video['likes']++;
            $video['liked_by'][] = $username; // Add the user to the list of people who liked this video
            break;
        }
    }

    // Save the updated videos data to the JSON file
    file_put_contents($videos_file, json_encode($videos));

    $_SESSION['message'] = "You liked this video!";
    header('Location: index.php');
    exit;
} else {
    $_SESSION['error'] = "Invalid video ID.";
    header('Location: index.php');
    exit;
}
?>
