<?php
session_start();

// Check if the comment form was submitted
if (isset($_POST['video_id']) && isset($_POST['comment'])) {
    $video_id = $_POST['video_id'];
    $comment = $_POST['comment'];
    $username = $_SESSION['username']; // Assuming the user is logged in

    // Fetch videos data
    $videos_file = 'data/videos.json';
    $videos = json_decode(file_get_contents($videos_file), true);
    if ($videos === null) {
        $videos = [];
    }

    // Find the video by ID and add the comment
    foreach ($videos as &$video) {
        if ($video['id'] === $video_id) {
            $video['comments'][] = ['username' => $username, 'comment' => $comment];
            break;
        }
    }

    // Save the updated videos data to the JSON file
    file_put_contents($videos_file, json_encode($videos));

    $_SESSION['message'] = "Comment added successfully!";
    header('Location: index.php');
    exit;
} else {
    $_SESSION['error'] = "Invalid comment or video ID.";
    header('Location: index.php');
    exit;
}
?>
