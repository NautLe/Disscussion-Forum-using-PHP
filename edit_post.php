<?php
session_start();
require 'dbConnect.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$admin = $_SESSION['admin']; // Admin session data
$post_id = $_GET['post_id'] ?? null;

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
// Check if post_id is provided
if (!$post_id) {
    echo "Invalid post ID.";
    exit();
}

try {
    // Fetch the post data
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found.";
        exit();
    }

    // If the form is submitted, update the post
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $module_id = $_POST['module_id'];
        $imagePath = $post['image']; // Default to existing image

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            $imagePath = $targetDir . basename($_FILES['image']['name']);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                echo "Failed to upload image.";
                exit();
            }
        }

        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, module_id = ?, image = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$title, $content, $module_id, $imagePath, $post_id]);

        header("Location: home.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Edit Post</h2>
    <form action="edit_post.php?post_id=<?php echo $post_id; ?>" method="post" enctype="multipart/form-data">
    <label for="title">Title: </label>
    <input type="text-post" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label for="content">Content: </label>
        <textarea name="content" id="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>

        <label for="module_id">Module: </label>
        <select name="module_id" id="module_id" required>
            <option value="1" <?php echo $post['module_id'] == 1 ? 'selected' : ''; ?>>GENERAL</option>
            <option value="14" <?php echo $post['module_id'] == 14 ? 'selected' : ''; ?>>HTML</option>
            <option value="12" <?php echo $post['module_id'] == 12 ? 'selected' : ''; ?>>JAVA</option>
            <option value="15" <?php echo $post['module_id'] == 15 ? 'selected' : ''; ?>>Space</option>
        </select>

        <label for="image">Upload Image (optional): </label>
        <input type="file" name="image" id="image">
        <?php if ($post['image']): ?>
            <p>Current Image: <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" width="100"></p>
        <?php endif; ?>

        <button type="submit">Update Post</button>
    </form>
    <a href="manage_posts.php" class="back-home">Back to manage posts</a>
</div>
</body>
</html>
