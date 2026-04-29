<?php
session_start();
require 'dbConnect.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user']; // Get logged-in user details
$post_id = $_GET['post_id'] ?? null;

// Check if post_id is provided
if (!$post_id) {
    echo "Invalid post ID.";
    exit();
}

try {
    // Fetch the post data and ensure it belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found or you do not have permission to edit this post.";
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

        // Update the post in the database
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, module_id = ?, image = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $content, $module_id, $imagePath, $post_id, $user['id']]);

        header("Location: home.php"); // Redirect to the user's posts page
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
<div class="background">
        <video autoplay muted loop class="video">
            <source src="2.mp4" type="video/mp4" class="v"></source>
        </video>
<div class="container">
    <h2>Edit Your Post</h2>
    <form action="edit_post_user.php?post_id=<?php echo $post_id; ?>" method="post" enctype="multipart/form-data">
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
    <a href="home.php" class="back-home">Back home</a>
</div>
</body>
</html>
