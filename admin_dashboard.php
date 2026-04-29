<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background">
    <div class="admin_dashboard">
        <h1>Admin Dashboard</h1>
        <h2>Manage Site Content</h2>
        <ul>
            <li><a href="manage_posts.php">Manage Posts</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="manage_modules.php">Manage Modules</a></li>
        </ul>
        <a href="admin_login.php" class="home-button">Back to Login</a>
    </div>


</body>
</html>
