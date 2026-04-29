<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $admin_key = $_POST['admin_key'];

    // Validate input
    $errors = [];
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    }
    if ($admin_key !== '0810005') {
        $errors['admin_key'] = 'Invalid Admin Key!! Please Try Again!!';
    }

    if (empty($errors)) {
        try {
            // Connect to the database
            $pdo = new PDO('mysql:host=localhost;dbname=askmecom_login', 'askmecom', 'ZO0r2YrisSVL');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert admin data into the database
            $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (:username, :password)');
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Admin registered successfully!!';
            }
        } catch (PDOException $e) {
            $errors['general'] = 'ERROR!! The username has been taken!!';
        }
    }

    $_SESSION['errors'] = $errors;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="background">
    <div class="form-container">
        <h1>Admin Registration</h1>

        <!-- Display success message -->
        <?php if (isset($_SESSION['success'])): ?>
            <script>
                alert('<?php echo $_SESSION['success']; ?>');
            </script>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Display errors -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="error-alert">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <i class="fas fa-key"></i>
                <input type="text" name="admin_key" placeholder="Admin Key" required>
            </div>
            <div class="additional-links">
                <button type="submit">Sign Up</button>
                <a href="admin_login.php" class="back-links">Back to Admin Login</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
