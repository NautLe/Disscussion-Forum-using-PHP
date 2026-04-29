<?php
session_start();
require 'dbConnect.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE ID = :id");
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$errorMsg = ''; // Initialize error message variable

// Update user details if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($currentPassword)) {
        $errorMsg = "All fields except 'New Password' are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } elseif (!empty($newPassword) && strlen($newPassword) < 8) {
        // Check if the new password is at least 8 characters long
        $errorMsg = "New password must be at least 8 characters long.";
    } else {
        // Check if the email is already taken by another user
        $emailCheckStmt = $pdo->prepare("SELECT ID FROM users WHERE email = :email AND ID != :id");
        $emailCheckStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $emailCheckStmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
        $emailCheckStmt->execute();

        if ($emailCheckStmt->rowCount() > 0) {
            $errorMsg = "Email is already registered!!";
        } else {
            // Check if the current password matches
            if (password_verify($currentPassword, $user['password'])) {
                // If a new password is provided, hash it; otherwise, keep the current password
                $password = !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : $user['password'];

                // Update user in the database
                $updateStmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE ID = :id");
                $updateStmt->bindParam(':name', $name);
                $updateStmt->bindParam(':email', $email);
                $updateStmt->bindParam(':password', $password);
                $updateStmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

                if ($updateStmt->execute()) {
                    // Refresh session data
                    $_SESSION['user']['name'] = $name;
                    $_SESSION['user']['email'] = $email;
                
                    // Redirect with a success message
                    header("Location: home.php?status=success&message=" . urlencode("Profile updated successfully!"));
exit();

                } else {
                    $errorMsg = "Error updating profile.";
                }
            } else {
                $errorMsg = "Current password is incorrect.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background">
    <video autoplay muted loop class="video">
        <source src="2.mp4" type="video/mp4" class="v">
    </video>
    <div class="profile-check">
        <h1>Edit Profile</h1>
        <!-- Display error message if it exists -->
        <?php if (!empty($errorMsg)) : ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
             <i class="fas fa-user"></i>
                <input type="text" name="name" id="name" placeholder="Name" value=<?php echo htmlspecialchars($user['name']); ?> required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="input-group password">
                 <i class="fas fa-lock"></i>
                <input type="password" name="current_password" id="current_password"placeholder="Current Password" required>
                <i id="eye-current" class="fa fa-eye"></i>
            </div>

            <div class="input-group password">
                <i class="fas fa-lock"></i>
                <input type="password" name="new_password" id="new_password"placeholder="New Password">
                <i id="eye-new" class="fa fa-eye"></i>
            </div>

            <button type="submit">Update Profile</button>
        </form>
        <a href="home.php" class="back-home">Back to dashboard</a>
    </div>
</div>

<script>
    // Toggle password visibility
    document.addEventListener("DOMContentLoaded", function () {
        const togglePasswordVisibility = (fieldId, toggleIconId) => {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(toggleIconId);

            toggleIcon.addEventListener("click", function () {
                const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
                passwordField.setAttribute("type", type);
                toggleIcon.classList.toggle("fa-eye");
                toggleIcon.classList.toggle("fa-eye-slash");
            });
        };

        togglePasswordVisibility("current_password", "eye-current");
        togglePasswordVisibility("new_password", "eye-new");
    });
</script>

</body>
</html>
