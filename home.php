<?php
session_start();
require 'dbConnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];

// Fetch all posts from the database


$stmt = $pdo->query("SELECT posts.id, posts.title, posts.created_at, posts.updated_at, posts.user_id, users.name AS name 
                     FROM posts 
                     JOIN users ON posts.user_id = users.id 
                     ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background">
  <video autoplay muted loop class="video">
    <source src="2.mp4" type="video/mp4" class="v"></source>
  </video>
<div class="home_container">
    <div class="welcome">
        <h1>👋🏻 Welcome back, <?php echo htmlspecialchars($user['name']); ?>!🥳</h1>
        <p class="description">Find answers to your technical questions and help others answer theirs.</p>
        <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>

        <a href="logout.php" class="home-button">Logout</a>
        <a href="create_post.php" class="home-button">Create post</a>
        <a href="edit_profile.php" class="home-button">Edit profile</a>
        <a href="contact_admin.php" class="home-button">Contact Admin</a>
        <div class="posts">
    <h2>All Posts</h2>
    <?php foreach ($posts as $post): ?>
        <div class="post" id="post-<?php echo $post['id']; ?>">
            <h3><a href="view_post.php?post_id=<?php echo $post['id']; ?>">
                <?php echo htmlspecialchars($post['title']); ?>
            </a></h3>
            <p>Posted by: <?php echo htmlspecialchars($post['name']); ?></p>
            <p>Posted on: <?php echo date("d/m/Y, H:i", strtotime($post['created_at'])); ?></p>
            <?php if (!empty($post['updated_at'])): ?>
                <p>Updated on: <?php echo date("d/m/Y, H:i", strtotime($post['updated_at'])); ?></p>
            <?php endif; ?>

            <!-- Check if the logged-in user is the creator of the post -->
            <?php if ($user['id'] === $post['user_id']): ?>
                <a href="edit_post_user.php?post_id=<?php echo $post['id']; ?>" class="home-button">Edit</a>
                <a href="delete_post_user.php?post_id=<?php echo $post['id']; ?>" class="home-button"
                   onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</div>

    </div>
</div>

<script>

// Display alert message based on URL parameters
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');
const message = urlParams.get('message');

if (status && message) {
    alert(decodeURIComponent(message));
    // Optionally clear query params from the URL after showing the alert
    history.replaceState(null, null, window.location.pathname);
}
</script>

</body>
</html>
