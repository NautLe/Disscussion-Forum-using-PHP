<?php
session_start();

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
    $username = "askmecom";
    $password = "ZO0r2YrisSVL";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all modules
    $stmt = $pdo->query("SELECT id, name FROM modules");
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Create a New Question</title>
</head>
<body>
<div class="background">
  <video autoplay muted loop class="video">
    <source src="2.mp4" type="video/mp4" class="v"></source>
  </video>
    <div class="question_container">
        <h1>Create A New Question</h1>
        <form method="POST" action="create_post_action.php" enctype="multipart/form-data">
            <label for="title">Title: </label>
            <input type="text-post" name="title" id="title" required>
            
            <label for="content">Content: </label>
            <textarea name="content" id="content" required></textarea>  

            <label for="module_id">Module: </label>
            <select name="module_id" id="module_id" required>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= htmlspecialchars($module['id']) ?>">
                        <?= htmlspecialchars($module['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="image">Upload Image:</label> 
            <input type="file" name="image" id="image">
            <button type="submit">Create Question</button>
        </form>
        <a href="home.php" class="back-home">Back to Dashboard</a>
    </div>
</body>
</html>
