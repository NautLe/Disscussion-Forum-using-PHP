<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$messageSent = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure the form fields are set
    $userName = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $userEmail = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $messageContent = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    if (empty($userName) || empty($userEmail) || empty($messageContent)) {
        $errorMsg = 'All fields are required.';
    } elseif (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Invalid email format.';
    } else {
        // PHPMailer logic
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = getenv('MAIL_USER');
            $mail->Password = getenv('MAIL_PASS');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom(getenv('MAIL_USER'), 'Contact Form');
            $mail->addAddress('dauding159@gmail.com'); // Admin's email

            // Content
            $mail->isHTML(false); 
            $mail->Subject = "Message from $userName <$userEmail>";
            $mail->Body = "Name: $userName\nEmail: $userEmail\n\nMessage:\n$messageContent";

            // Send the email
            $mail->send();
            $messageSent = true;
        } catch (Exception $e) {
            $errorMsg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./themify-icons/themify-icons.css">
</head>
<body>
<div class="background">
    <video autoplay muted loop class="video">
        <source src="2.mp4" type="video/mp4" class="v"></source>
    </video>
    <div id="contact" class="content-section">
        <h2 style="font-size: 30px; font-weight: 500; text-align: center; letter-spacing: 4px;" class="section-heading">CONTACT</h2>
        <p style="font-size: 15px; text-align: center; margin-top: 25px; font-style: italic; opacity: 0.7;" class="section-sub-heading">Find me? Drop a note!!</p>
        <div class="row contact-content">
            <div class="col col-half contact-info">
                <p><i class="ti-location-pin"></i> TP HCM, VIETNAM</p>
                <p><i class="ti-mobile"></i> Phone: 0333208169</p>
                <p><i class="ti-email"></i> Email: dauding159@gmail.com</p>
                <a href="home.php" class="home-button" style="margin-top : 100px;">Back to Dashboard</a>
            </div>

            <div class="col col-half contact-form">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col col-half">
                            <input type="text" name="name" placeholder="Name" required class="form-control" style="background-color: #fff">
                        </div>
                        <div class="col col-half">
                            <input type="email" name="email" placeholder="Email" required class="form-control"style="background-color: #fff">
                        </div>
                    </div>
                    <div class="row mt-8">
                        <div class="col col-full">
                            <textarea name="message" placeholder="Message" required class="form-control" style ="border-radius: unset;" ></textarea>
                        </div>
                    </div>
                    <input class="home-button" type="submit" value="Send">
                </form>
            </div>
        </div>

        <!-- Success/Error messages -->
        <?php if ($messageSent): ?>
            <div class="success-message">Your message has been sent successfully.</div>
        <?php elseif (!empty($errorMsg)): ?>
            <div class="error-message"><?php echo $errorMsg; ?></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>