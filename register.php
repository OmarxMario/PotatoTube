<link rel="stylesheet" href="assets/css/style.css">
<?php
session_start();
include('includes/header.php');

// Check if the users.json file exists and is readable
$users_file = 'data/users.json';

if (!file_exists($users_file)) {
    file_put_contents($users_file, json_encode([])); // Initialize with an empty array if file doesn't exist
}

// Fetch users data
$users = json_decode(file_get_contents($users_file), true);

// Check if json_decode returns an array, if not initialize it as an empty array
if ($users === null) {
    $users = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    // Generate user ID based on the current highest ID
    $user_id = count($users) + 1;

    $new_user = [
        'id' => $user_id,
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'following' => [],
        'followers' => [],
        'profile_pic' => 'default.png'
    ];

    $users[] = $new_user;

    // Save updated users list to users.json
    file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));

    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit();
}
?>

<h2>Register</h2>
<form method="POST" action="register.php">
    <label>Username:</label>
    <input type="text" name="username" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>

<?php include('includes/footer.php'); ?>
