<?php
session_start();
require 'dbConnect.php';

if (!isset($_SESSION['user'])) {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['post_id'])) {
    $postId = intval($_GET['post_id']);
    $userId = $_SESSION['user']['id'];

    try {
        // Ensure the logged-in user owns the post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);

        if ($stmt->rowCount() > 0) {
            header("Location: home.php?status=success&message=Post%20deleted%20successfully");
            exit();
        } else {
            header("Location: home.php?status=error&message=Post%20not%20found%20or%20no%20permission");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: home.php?status=error&message=" . urlencode("Database error: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: home.php?status=error&message=Invalid%20request");
    exit();
}
