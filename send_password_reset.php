<?php

// Check if the form is submitted and "email" exists in $_POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["email"])) {
    // Get email from POST data
    $email = $_POST["email"];

    // Generate a secure random token and its hash
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);

    // Set the expiration time (30 minutes from now)
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    // Include database connection
    $pdo = require __DIR__ . "/dbConnect.php";

    // Define the SQL query
    $sql = "UPDATE users 
            SET reset_token_hash = :token_hash, 
                reset_token_expires_at = :expiry 
            WHERE email = :email";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind values
    $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
    $stmt->bindValue(':expiry', $expiry, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END
        
Welcome to my website...
if you are requesting a password recovery please click in the link below and 
following the instructions in the link.... thank you so much..... :333

Click <a href="https://askme.io.vn/Stackoverflow2.0/reset_password.php?token=$token"> here </a> to reset your password

END;

        try {
            $mail->send();
            echo "Message sent and will expire in 30 minutes. Please check your inbox.";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found or update failed.";
    }
} else {
    // If accessed without POST data, redirect to the form
    header("Location: send_password_reset.php");
    exit();
}

?>

