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

    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        header("Location: manage_users.php");
        exit();
    } else {
        die("Invalid user ID.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
