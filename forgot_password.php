<?php

// Include database connection
$pdo = require __DIR__ . "/dbConnect.php";

// Initialize a message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["email"])) {
        // Get the email from the form
        $email = $_POST["email"];

        // Generate a secure random token and its hash
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);

        // Set the expiration time (30 minutes from now)
        $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

        // Update user with token and expiry
        $sql = "UPDATE users 
                SET reset_token_hash = :token_hash, 
                    reset_token_expires_at = :expiry 
                WHERE email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(':expiry', $expiry, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Load mailer configuration
            $mail = require __DIR__ . "/mailer.php";

            // Configure and send the email
            $mail->setFrom("noreply@example.com");
            $mail->addAddress($email);
            $mail->Subject = "Password Reset";
            $mail->Body = <<<END
            
Welcome to my website...
if you are requesting a password recovery please click in the link below and 
following the instructions in the link.... thank you so much..... ❤️❤️❤️
<br></br>
Click <a href="https://askme.io.vn/reset_password.php?token=$token"> HERE!! </a> to reset your password

END;

            try {
                $mail->send();
                $message = "Password reset email sent. The code will be expired in 30minutes. Please check your inbox. ";
            } catch (Exception $e) {
                $message = "Failed to send email. Error: " . $mail->ErrorInfo;
            }
        } else {
            $message = "Email not found. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<body>
    <div class="background">
        <video autoplay muted loop class="video">
            <source src="2.mp4" type="video/mp4" class="v"></source>
        </video>
    <div class="forgot-password-container">
        <!-- Display the message if set -->
        <?php if (!empty($message)): ?>
            <script>
                alert("<?php echo $message; ?>");
            </script>
        <?php endif; ?>

        <div class="profile-check">
            <h1 class="form-title">Forgot Password</h1>
            <form method="POST" action="forgot_password.php">
                <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="Email" required>
                </div>
                <div>
                    <button type="submit">Send Reset Link</button>
                </div>
            </form>
        <a href="index.php" class="back-home">Back to Login Page</a>
            
    </div>
</body>
</html>
