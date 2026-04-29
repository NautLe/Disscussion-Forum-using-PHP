<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}
if (isset($_GET['message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            alert("<?php echo htmlspecialchars($_GET['message']); ?>");
            // Remove the query parameter from the URL after showing the alert
            const url = new URL(window.location.href);
            url.searchParams.delete('message');
            window.history.replaceState(null, '', url);
        });
    </script>
<?php endif; 

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
    <title>Manage Modules</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Manage Modules</h1>
        <a href="add_module.php" class="button">Add New Module</a>
        <ul>
            <?php foreach ($modules as $module): ?>
                <li>
                    <h2><?= htmlspecialchars($module['name']) ?></h2>
                    <a href="edit_module.php?module_id=<?= $module['id'] ?>" class="button">Edit</a>
                    <a href="delete_module.php?module_id=<?= $module['id'] ?>" class="button"
                       onclick="return confirm('Are you sure you want to delete this module?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="admin_dashboard.php" class="back-home">Back to admin dashboard</a>
    </div>
</body>
</html>
