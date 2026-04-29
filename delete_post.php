<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = intval($_POST['post_id']);

    try {
        $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
        $username = "askmecom";
        $password = "ZO0r2YrisSVL";

        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$postId]);

        if ($stmt->rowCount() > 0) {
            echo "Post deleted successfully.";
        } else {
            echo "Post not found or already deleted.";
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
