<link rel="stylesheet" href="assets/css/style.css">
<?php
session_start();
include('includes/header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users = json_decode(file_get_contents('data/users.json'), true);
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        }
    }
    $error = "Invalid username or password!";
}
?>

<h2>Login</h2>
<form method="POST" action="login.php">
    <label>Username:</label>
    <input type="text" name="username" required>
    <label>Password:</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
<?php if (isset($error)) echo "<p>$error</p>"; ?>

<?php include('includes/footer.php'); ?>
