<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You need to be logged in to follow users.";
    header('Location: login.php');
    exit;
}

$current_user = $_SESSION['username']; // Logged-in user
$profile_username = $_POST['profile_username']; // Profile user to follow/unfollow
$action = $_POST['action']; // Either 'follow' or 'unfollow'

// Fetch users data
$users_file = 'data/users.json';
$users = json_decode(file_get_contents($users_file), true);

// Check if the user and the profile exist
$user_profile = null;
foreach ($users as &$user) {
    if ($user['username'] === $profile_username) {
        $user_profile = &$user;
        break;
    }
}

if (!$user_profile) {
    $_SESSION['error'] = "User not found.";
    header('Location: index.php');
    exit;
}

// Perform the follow or unfollow action
if ($action === 'follow') {
    if (!in_array($current_user, $user_profile['followers'])) {
        $user_profile['followers'][] = $current_user;
        $user_profile['following'][] = $profile_username; // Add to following list of current user
    }
} elseif ($action === 'unfollow') {
    if (($key = array_search($current_user, $user_profile['followers'])) !== false) {
        unset($user_profile['followers'][$key]);
    }
    if (($key = array_search($profile_username, $users[$current_user]['following'])) !== false) {
        unset($users[$current_user]['following'][$key]);
    }
}

// Save the updated users data back to the file
file_put_contents($users_file, json_encode($users));

// Redirect back to the profile page
header("Location: profile.php?username=" . $profile_username);
exit;
