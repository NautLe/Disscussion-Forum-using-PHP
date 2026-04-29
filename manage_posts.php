<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
    $username = "askmecom";
    $password = "ZO0r2YrisSVL";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all posts with user and module details
    $stmt = $pdo->query("SELECT posts.id, posts.title, posts.content, posts.created_at, users.name AS user_name, modules.name AS module_name
                         FROM posts
                         LEFT JOIN users ON posts.user_id = users.id
                         LEFT JOIN modules ON posts.module_id = modules.id
                         ORDER BY posts.created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function deletePost(postId) {
            if (confirm("Are you sure you want to delete this post?")) {
                fetch('delete_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ post_id: postId })
                })
                .then(response => response.text())
                .then(message => {
                    alert(message); // Show the response message
                    if (message.trim() === "Post deleted successfully.") {
                        document.getElementById(`post-${postId}`).remove(); // Remove the post element from DOM
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred. Please try again.");
                });
            }
        }
    </script>
</head>
<body>

    <div class="container">
        <h1>Manage Posts</h1>
        <a href="add_post.php" class="button">Add New Post</a>
        <ul>
            <?php foreach ($posts as $post): ?>
                <li id="post-<?= $post['id'] ?>">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <p><strong>Author:</strong> <?= htmlspecialchars($post['user_name']) ?></p>
                    <p><strong>Module:</strong> <?= htmlspecialchars($post['module_name']) ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
                    <p><strong>Content:</strong><br><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <a href="edit_post.php?post_id=<?= $post['id'] ?>" class="button">Edit</a>
                    <a href="javascript:void(0);" class="button" onclick="deletePost(<?= $post['id'] ?>)">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="admin_dashboard.php" class="back-home">Back to admin dashboard</a>
    </div>
</body>
</html>
