<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

$success = false;

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=localhost;dbname=askmecom_login;charset=utf8mb4";
    $username = "askmecom";
    $password = "ZO0r2YrisSVL";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $moduleName = trim($_POST['module_name']);

        // Check if the module name is not empty
        if (empty($moduleName)) {
            $error = "Module name cannot be empty.";
        } else {
            // Insert the new module into the database
            $stmt = $pdo->prepare("INSERT INTO modules (name) VALUES (:name)");
            $stmt->execute(['name' => $moduleName]);

            $success = true; // Set success flag
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
    <title>Add Module</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function showSuccessAlert() {
            alert('Module ADDED successfully! ! !');
        }
    </script>
</head>
<body>

    <div class="container">
        <h1>Add New Module</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="add_module.php">
            <label for="module_name">Module Name:</label>
            <input type="text-post" id="module_name" name="module_name" required>
            <button type="submit" class="button">Add Module</button>
        </form>
        <a href="manage_modules.php" class="back-home">Back to manage module</a>
    </div>

    <?php if ($success): ?>
        <script>
            showSuccessAlert();
        </script>
    <?php endif; ?>
</body>
</html>
