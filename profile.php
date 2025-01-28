<?php
session_start();
include('includes/header.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You need to be logged in to view profiles.";
    header('Location: login.php');
    exit;
}

// Check if the username is provided in the URL
if (!isset($_GET['username']) || empty($_GET['username'])) {
    $_SESSION['error'] = "No username specified.";
    header('Location: index.php');
    exit;
}

$profile_username = $_GET['username']; // The username from the URL
$current_user = $_SESSION['username']; // Current logged-in user

// Fetch users data
$users_file = 'data/users.json';
$users = json_decode(file_get_contents($users_file), true);

// Check if the user exists
$user_profile = null;
foreach ($users as &$user) {
    if ($user['username'] === $profile_username) {
        $user_profile = &$user;
        break;
    }
}

if (!$user_profile) {
    echo "User not found!";
    exit;
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && $current_user === $profile_username) {
    $profile_picture = $_FILES['profile_picture'];

    // Check for upload errors
    if ($profile_picture['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "Error uploading profile picture. Error code: " . $profile_picture['error'];
    } else {
        // Ensure the uploads directory exists
        $target_dir = "uploads/profile_pictures/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate a unique file name
        $file_extension = strtolower(pathinfo($profile_picture['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['error'] = "Invalid file type. Allowed types: JPG, PNG, GIF.";
        } elseif ($profile_picture['size'] > 5000000) { // Limit file size to 5MB
            $_SESSION['error'] = "File size exceeds the 5MB limit.";
        } else {
            $new_file_name = $current_user . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $new_file_name;

            // Move the uploaded file
            if (move_uploaded_file($profile_picture['tmp_name'], $target_file)) {
                // Update the user's profile picture in JSON
                $user_profile['profile_picture'] = $target_file;
                file_put_contents($users_file, json_encode($users));
                $_SESSION['success'] = "Profile picture updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to save the uploaded file.";
            }
        }
    }
}
?>

<h2>Profile of <?php echo htmlspecialchars($user_profile['username']); ?></h2>

<!-- Display Profile Picture -->
<div class="profile-picture">
    <img src="<?php echo htmlspecialchars($user_profile['profile_picture'] ?? 'uploads/profile_pictures/default.png'); ?>" 
         alt="Profile Picture" 
         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
</div>

<!-- Display feedback messages -->
<?php
if (isset($_SESSION['error'])) {
    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>";
    unset($_SESSION['success']);
}
?>

<!-- Change Profile Picture Form -->
<?php if ($current_user === $profile_username): ?>
    <form action="profile.php?username=<?php echo htmlspecialchars($profile_username); ?>" method="POST" enctype="multipart/form-data">
        <label for="profile_picture">Change Profile Picture:</label>
        <input type="file" name="profile_picture" id="profile_picture" required>
        <button type="submit">Upload</button>
    </form>
<?php endif; ?>

<p>Followers: <?php echo isset($user_profile['followers']) ? count($user_profile['followers']) : 0; ?></p>
<p>Following: <?php echo isset($user_profile['following']) ? count($user_profile['following']) : 0; ?></p>

<!-- Follow/Unfollow Button -->
<?php
if ($current_user !== $profile_username) {
    if (in_array($current_user, $user_profile['followers'])) {
        echo '<form action="follow.php" method="POST">
                <input type="hidden" name="action" value="unfollow">
                <input type="hidden" name="profile_username" value="' . $profile_username . '">
                <button type="submit">Unfollow</button>
              </form>';
    } else {
        echo '<form action="follow.php" method="POST">
                <input type="hidden" name="action" value="follow">
                <input type="hidden" name="profile_username" value="' . $profile_username . '">
                <button type="submit">Follow</button>
              </form>';
    }
}
?>

<!-- Display User's Videos -->
<h3>Videos Uploaded by <?php echo htmlspecialchars($user_profile['username']); ?></h3>
<div class="video-feed">
    <?php
    if (isset($user_profile['videos']) && is_array($user_profile['videos'])) {
        if (count($user_profile['videos']) > 0) {
            foreach ($user_profile['videos'] as $video) {
                ?>
                <div class="video-card">
                    <video controls>
                        <source src="uploads/videos/<?php echo $video['filename']; ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <p><?php echo htmlspecialchars($video['description']); ?></p>
                </div>
                <?php
            }
        } else {
            echo "<p>No videos uploaded by this user.</p>";
        }
    } else {
        echo "<p>No videos uploaded by this user.</p>";
    }
    ?>
</div>
