<?php

$token = $_GET["token"] ?? null;

if (!$token) {
    die("Invalid or missing token.");
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

if (!$users) {
    die("Token not found.");
}

// Check if the token has expired
if (strtotime($users["reset_token_expires_at"]) <= time()) {
    die("Token has expired.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Toggle visibility for "New Password"
            const eyeCurrent = document.getElementById("eye-current");
            const newPassword = document.getElementById("new_password");

            eyeCurrent.addEventListener("click", () => {
                const type = newPassword.type === "password" ? "text" : "password";
                newPassword.type = type;
                eyeCurrent.classList.toggle("fa-eye");
                eyeCurrent.classList.toggle("fa-eye-slash");
            });

            // Toggle visibility for "Confirm Password"
            const eyeNew = document.getElementById("eye-new");
            const confirmPassword = document.querySelector("input[name='password_confirmation']");

            eyeNew.addEventListener("click", () => {
                const type = confirmPassword.type === "password" ? "text" : "password";
                confirmPassword.type = type;
                eyeNew.classList.toggle("fa-eye");
                eyeNew.classList.toggle("fa-eye-slash");
            });

            // Validate passwords on form submission
            const form = document.querySelector("form");
            form.addEventListener("submit", (event) => {
                const password = newPassword.value.trim();
                const confirm = confirmPassword.value.trim();

                // Check if passwords are at least 8 characters long
                if (password.length < 8) {
                    alert("Password must be at least 8 characters long.");
                    event.preventDefault(); // Stop form submission
                    return;
                }

                // Check if passwords match
                if (password !== confirm) {
                    alert("Passwords do not match. Please try again.");
                    event.preventDefault(); // Stop form submission
                }
            });
        });
    </script>
</head>
<body>
<body>
<div class="background">
        <video autoplay muted loop class="video">
            <source src="2.mp4" type="video/mp4" class="v"></source>
        </video>
<div class="profile-check">
    <h1>Reset Password</h1>
    <form method="POST" action="process_reset_password.php">
        <div class="input-group password">
            <i class="fas fa-lock"></i>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
            <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
            <i id="eye-current" class="fa fa-eye"></i>
        </div>

        <div class="input-group password">
            <i class="fas fa-lock"></i>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
            <i id="eye-new" class="fa fa-eye"></i>
        </div>

        <button type="submit">Send</button>
    </form>
</div>
</body>
</html>
