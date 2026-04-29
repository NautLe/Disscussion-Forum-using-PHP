<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

try {
    // Connect to the database using PDO
    $dsn = "mysql:host=localhost;dbname=login;charset=utf8mb4";
    $username = "root";
    $password = "";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);

        // Fetch the user data
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die("User not found!");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);

            if ($name && $email) {
                $updateStmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $updateStmt->execute([$name, $email, $user_id]);

                header("Location: manage_users.php");
                exit();
            } else {
                $error = "Please fill in all fields.";
            }
        }
    } else {
        die("Invalid user ID.");
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
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            <button type="submit" class="button">Save Changes</button>
        </form>
        <a href="manage_users.php" class="back-home">Back to Manage Users</a>
    </div>
</body>
</html>
