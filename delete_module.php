<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['module_id']) || !is_numeric($_GET['module_id'])) {
    header('Location: manage_modules.php');
    exit();
}

try {
    // Database connection
    $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
    $username = "askmecom";
    $password = "ZO0r2YrisSVL";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve module ID from the URL
    $module_id = intval($_GET['module_id']);

    // Delete the module
    $stmt = $pdo->prepare("DELETE FROM modules WHERE id = :module_id");
    $stmt->bindParam(':module_id', $module_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: manage_modules.php?message=Module+DELETED+successfully ! ! !');
        exit();
    } else {
        header('Location: manage_modules.php?error=Unable+to+delete+module');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

