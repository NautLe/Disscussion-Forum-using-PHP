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

    // Fetch all users for the dropdown
    $stmt = $pdo->query("SELECT id, name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all modules for the dropdown
    $stmt = $pdo->query("SELECT id, name FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['user_id']; // Selected user ID
        $moduleId = $_POST['module_id'];
        $title = $_POST['title'];
        $content = $_POST['content'];
        $imagePath = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            $imagePath = $targetDir . basename($_FILES['image']['name']);
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                throw new Exception("Failed to upload image.");
            }
        }

        // Insert the post into the database
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, module_id, title, content, image, created_at)
                               VALUES (:user_id, :module_id, :title, :content, :image, NOW())");
        $stmt->execute([
            ':user_id' => $userId,
            ':module_id' => $moduleId,
            ':title' => $title,
            ':content' => $content,
            ':image' => $imagePath,
        ]);

        echo "<script>alert('Post added successfully!'); window.location.href = 'manage_posts.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New Post</h1>
        <form action="add_post.php" method="post" enctype="multipart/form-data">
            <label for="user_id">Author</label>
            <select name="user_id" id="user_id" required>
                <option value="">Select a User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="module_id">Module</label>
            <select name="module_id" id="module_id" required>
                <option value="">Select a Module</option>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= htmlspecialchars($module['id']) ?>"><?= htmlspecialchars($module['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="title">Title: </label>
            <input type="text-post" name="title" id="title" required>

            <label for="content">Content</label>
            <textarea name="content" id="content" rows="5" required></textarea>

            <label for="image">Upload Image (optional)</label>
            <input type="file" name="image" id="image" accept="image/*">

            <button type="submit" class="button">Submit</button>
        </form>
        <a href="manage_posts.php" class="back-home">Back to manage posts</a>
    </div>
</body>
</html>
