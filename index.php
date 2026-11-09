<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - ApexPlanet Task 3</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Student Management System</h1>

    <p style="font-size: 16px; margin-top: 15px; margin-bottom: 25px; color: #475569;">
        A modern <strong>PHP & MySQL</strong> web application with secure authentication, full <em>CRUD</em> functionality, keyword search filtering, and single post view for managing student posts and records.
    </p>

    <?php if (isset($_SESSION['user_id'])): ?>

        <div class="alert alert-info">
            👋 Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
        </div>

        <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
            <a href="posts/index.php" class="btn btn-primary">Manage & Search Posts →</a>
            <a href="posts/create.php" class="btn btn-secondary">+ Create Post</a>
            <a href="auth/logout.php" class="btn btn-secondary">Logout</a>
        </div>

    <?php else: ?>

        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <a href="auth/login.php" class="btn btn-primary">Sign In</a>
            <a href="auth/register.php" class="btn btn-secondary">Create Account</a>
        </div>

    <?php endif; ?>

</div>

</body>
</html>