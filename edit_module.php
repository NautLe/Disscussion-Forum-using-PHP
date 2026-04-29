<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['module_id'])) {
    header('Location: manage_modules.php?message=Module ID is required');
    exit();
}

$moduleId = $_GET['module_id'];

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
    $username = "askmecom";
    $password = "ZO0r2YrisSVL";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the module details
    $stmt = $pdo->prepare("SELECT id, name FROM modules WHERE id = :id");
    $stmt->execute(['id' => $moduleId]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$module) {
        header('Location: manage_modules.php?message=Module not found');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newName = trim($_POST['name']);

        if (empty($newName)) {
            $error = "Module name cannot be empty.";
        } else {
            // Update the module in the database
            $updateStmt = $pdo->prepare("UPDATE modules SET name = :name WHERE id = :id");
            $updateStmt->execute(['name' => $newName, 'id' => $moduleId]);

            header('Location: manage_modules.php?message=Module updated successfully');
            exit();
        }
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
    <title>Edit Module</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Module</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="name">Module Name:</label>
            <input type="text-post" id="name" name="name" value="<?= htmlspecialchars($module['name']) ?>" required>
            <button type="submit" class="button">Update Module</button>
        </form>
        <a href="manage_modules.php" class="back-home">Back to Manage Modules</a>
    </div>
</body>
</html>
