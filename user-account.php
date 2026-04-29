<?php
require_once 'dbConnect.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $created_at = date('Y-m-d H:i:s');

    // Validation checks
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        $errors['user_exist'] = 'Email is already registered';
    }

    // If there are errors, redirect back to the registration page
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: register.php');
        exit();
    }

    // Hash the password and create the user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password, name, created_at) VALUES (:email, :password, :name, :created_at)");
    $stmt->execute([
        'email' => $email, 
        'password' => $hashedPassword, 
        'name' => $name, 
        'created_at' => $created_at
    ]);
    $_SESSION['success'] = "Registration successful! You can now log in.";
    header('Location: register.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate email and password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }

    // Fetch the user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verify password and set session variables
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['ID'];  // Set user_id as session variable for consistency
        $_SESSION['user'] = [
            'id' => $user['ID'],
            'email' => $user['email'],
            'name' => $user['name'],
            'created_at' => $user['created_at']
        ];

        header('Location: home.php');
        exit();
    } else {
        $errors['login'] = 'Invalid email or password';
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }
}
?>
