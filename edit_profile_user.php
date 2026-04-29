<?php
session_start();
require 'dbConnect.php';



// Check if user_id is provided
if (!isset($_GET['user_id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['user_id'];
$errorMsg = '';

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE ID = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $errorMsg = "User not found.";
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errorMsg)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];

    // Input validation
    if (empty($name) || empty($email)) {
        $errorMsg = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } elseif (!empty($newPassword) && strlen($newPassword) < 8) {
        $errorMsg = "New password must be at least 8 characters long.";
    } else {
        try {
            // Hash new password if provided, or keep the existing one
            $password = !empty($newPassword) ? password_hash($newPassword, PASSWORD_DEFAULT) : $user['password'];

            // Update user information
            $updateStmt = $pdo->prepare("
                UPDATE users 
                SET name = :name, email = :email, password = :password 
                WHERE ID = :id
            ");
            $updateStmt->bindParam(':name', $name);
            $updateStmt->bindParam(':email', $email);
            $updateStmt->bindParam(':password', $password);
            $updateStmt->bindParam(':id', $user_id, PDO::PARAM_INT);

          if ($updateStmt->execute()) {
    header("Location: edit_profile_user.php?user_id=$user_id&success=1");
    exit();


            } else {
                $errorMsg = "Failed to update user information.";
            }
        } catch (PDOException $e) {
            $errorMsg = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="background">
    <div class="profile-check">
        <h1>Edit User Profile</h1>
        
        <!-- Display error messages -->
        <?php if (!empty($errorMsg)) : ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
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
                <input type="password" name="new_password" id="new_password"placeholder="New Password">
                <i id="eye-new" class="fa fa-eye"></i>
            </div>

            <button type="submit">Update Profile</button>
        </form>
        <?php else: ?>
            <p>User profile not available.</p>
        <?php endif; ?>

        <a href="manage_users.php" class="back-home">Back to Manage Users</a>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("success")) {
            alert("Profile updated successfully!");
            // Optionally, remove the `success` parameter from the URL after showing the alert
            urlParams.delete("success");
            history.replaceState(null, "", window.location.pathname + "?" + urlParams.toString());
        }
    });
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

        togglePasswordVisibility("new_password", "eye-new");
    });
</script>

</body>
</html>
