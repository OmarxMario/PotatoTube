<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PotatoTube</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
</head>
<body>

    <!-- Navigation Bar -->
    <header>
        <nav>
            
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="upload_video.php">Upload Video</a></li>
                
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php?username=<?php echo $_SESSION['username']; ?>">My Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Error Message (if any) -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <p><?php echo $_SESSION['error']; ?></p>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

</body>
</html>
