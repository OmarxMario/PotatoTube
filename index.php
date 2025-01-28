<title>PotatoTube</title>

<?php
session_start();
include('includes/header.php');



// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You need to be logged in to view videos.";
    header('Location: login.php');
    exit;
}

// Fetch users data from the JSON file
$users_file = 'data/users.json';
$users = json_decode(file_get_contents($users_file), true);

// Check if there are any users with videos
if (!$users || empty($users)) {
    echo "<p>No users found.</p>";
    exit;
}

?>

<h2>Welcome to PotatoTube</h2>

<!-- Display the videos uploaded by all users -->
<div class="video-feed">
    <?php
    // Loop through users and display their videos
    foreach ($users as $user) {
        if (isset($user['videos']) && is_array($user['videos']) && count($user['videos']) > 0) {
            foreach ($user['videos'] as $video) {
                ?>
                <div class="video-card">
                    <video controls>
                        <source src="uploads/videos/<?php echo $video['filename']; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p><?php echo htmlspecialchars($video['description']); ?></p>
                    <p>Uploaded by: <?php echo htmlspecialchars($user['username']); ?></p>
                    
                    <!-- Option to visit the uploader's profile -->
                    <form action="profile.php" method="GET">
                        <input type="hidden" name="username" value="<?php echo $user['username']; ?>">
                        <button type="submit">Visit Profile</button>
                    </form>
                    
                    <!-- Like and Comment options (example) -->
                    <form action="like_video.php" method="POST">
                        <input type="hidden" name="video_id" value="<?php echo $video['id']; ?>">
                        <button type="submit">Like</button>
                    </form>

                    
                </div>
                <?php
            }
        } else {
            echo "<p>No videos yet</p>";
        }
    }
    ?>
</div>

<?php
// Display any session errors or success messages
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>";
    unset($_SESSION['success']);
}
?>

<!-- Link to upload a video -->
<?php
if (isset($_SESSION['username'])) {
    echo '<a href="upload_video.php">Upload a New Video</a>';
}
?>

<?php include('includes/footer.php'); ?>
