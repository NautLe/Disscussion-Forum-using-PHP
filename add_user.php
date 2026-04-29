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

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Validate inputs
        if (empty($name) || empty($email) || empty($_POST['password'])) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $emailExists = $stmt->fetchColumn() > 0;

            if ($emailExists) {
                echo "<script>alert('Email already exists!');</script>";
            } else {
                // Insert new user into the database
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ]);

                echo "<script>alert('User added successfully!'); window.location.href = 'manage_users.php';</script>";
                exit();
            }
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
    <title>Add New User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Add New User</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="add_user.php">
            <label for="name">Name:</label>
            <input type="text-post" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="text-post" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="text-post" id="password" name="password" required>

            <button type="submit" class="button">Add User</button>
        </form>
        <a href="manage_users.php" class="back-home">Back to Manage Users</a>
    </div>
</body>
</html>
