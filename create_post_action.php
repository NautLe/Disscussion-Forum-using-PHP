<?php
session_start();
require 'dbConnect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $module_id = intval($_POST['module_id']); // Get the module_id from the form
    $imagePath = null; // Default to null if no image is provided

    // Validate required fields
    if (empty($title) || empty($content) || empty($module_id)) {
        echo "All fields are required.";
        exit();
    }

    // Check if an image file is uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . uniqid() . "_" . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile; // Store the full relative path
        } else {
            echo "Error uploading image.";
            exit();
        }
    }

    // Save the post to the database
    $stmt = $pdo->prepare("
        INSERT INTO posts (title, content, image, user_id, module_id, created_at) 
        VALUES (:title, :content, :image, :user_id, :module_id, NOW())
    ");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':user_id', $_SESSION['user']['id']); // Assuming user info is stored in the session
    $stmt->bindParam(':module_id', $module_id);

    // Bind the image parameter only if an image path exists
    if ($imagePath !== null) {
        $stmt->bindParam(':image', $imagePath);
    } else {
        $stmt->bindValue(':image', null, PDO::PARAM_NULL);
    }

    if ($stmt->execute()) {
        header("Location: home.php"); // Redirect to home or post page after success
        exit();
    } else {
        echo "Error saving post.";
    }
}
?>
