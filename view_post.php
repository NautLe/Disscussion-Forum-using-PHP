<?php
session_start();
require 'dbConnect.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

// If post_id is invalid, exit
if (!$post_id) {
    echo "No post ID provided.";
    exit();
}

// Handle the comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user']['id']; // Logged-in user ID
    $name = $_SESSION['user']['name'] ?? 'Anonymous'; // User's name or 'Anonymous'

    if (!empty($comment)) {
        $stmt = $pdo->prepare("
            INSERT INTO comments (post_id, user_id, name, comment, created_at) 
            VALUES (:post_id, :user_id, :name, :comment, NOW())
        ");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Redirect to avoid resubmission
            header("Location: view_post.php?post_id=$post_id");
            exit();
        } else {
            echo "Failed to post your comment. Please try again.";
        }
    } else {
        echo "Comment cannot be empty.";
    }
}

// Fetch the post, user, and module data
$stmt = $pdo->prepare("
    SELECT posts.*, users.name AS user_name, modules.name AS module_name 
    FROM posts 
    LEFT JOIN users ON posts.user_id = users.id 
    LEFT JOIN modules ON posts.module_id = modules.id 
    WHERE posts.id = :id
");
$stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "No results found.";
    exit();
}

// Fetch comments for this post
$comments_stmt = $pdo->prepare("
    SELECT comments.comment, comments.created_at, IFNULL(users.name, 'Anonymous') AS user_name 
    FROM comments 
    LEFT JOIN users ON comments.user_id = users.id 
    WHERE comments.post_id = :post_id 
    ORDER BY comments.created_at DESC
");
$comments_stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
$comments_stmt->execute();
$comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
</head>
<body>
<div class="background">
    <video autoplay muted loop class="video">
        <source src="2.mp4" type="video/mp4">
    </video>
    <div class="post_container">
        <!-- Post Details -->
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><strong>Posted by: </strong><?php echo htmlspecialchars($post['user_name']); ?></p>
        <p><strong>Module:</strong> <?php echo htmlspecialchars($post['module_name']); ?></p>
        <p class="post-date"><strong>Posted on: </strong><?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></p>

        <div class="post-content">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if (!empty($post['image'])): ?>
                <div class="post-image">
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                </div>
            <?php endif; ?>
        </div>

        <!-- Comment Form -->
        <form method="POST" action="">
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <h2>Your Answer:</h2>
            <textarea name="comment" id="comment" required></textarea>
            <button type="submit">Post Your Answer</button>
        </form>

        <!-- Display Comments -->
        <h2>Answers:</h2>
        <?php if ($comments): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="answer">
                    <p><strong style="width: 125px;" ><?php echo htmlspecialchars($comment['user_name']); ?></strong>answered:</p>
                    <p class="comment"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    <p class="comment-date"><?php echo date("F j, Y, g:i a", strtotime($comment['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No answers yet. Be the first to answer!</p>
        <?php endif; ?>

        <a href="home.php" class="back-home">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
