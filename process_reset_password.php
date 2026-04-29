<?php

$token = $_POST["token"] ?? null;

if (!$token) {
    die("Token is required.");
}

// Hash the token
$token_hash = hash("sha256", $token);

// Include database connection
$pdo = require __DIR__ . "/dbConnect.php";

// Prepare the SQL query
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";

// Prepare the statement
$stmt = $pdo->prepare($sql);

// Execute the query with the token hash
$stmt->execute([$token_hash]);

// Fetch the user data
$users = $stmt->fetch(PDO::FETCH_ASSOC);

if ($users === false) {
    die("Token not found.");
}

// Check if the token has expired (you can add logic for expiration here if needed)

// Hash the new password
$password = password_hash($_POST["new_password"], PASSWORD_DEFAULT);

// Update the user's password in the database
$update_sql = "UPDATE users 
               SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL 
               WHERE ID = ?";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->execute([$password, $users['ID']]);

// PHP will echo the JS for the alert and redirection
echo "<script>
        alert('Password updated successfully. You will now be redirected to the login page.');
        window.location.href = 'index.php'; // Redirect to login page
      </script>";
?>
