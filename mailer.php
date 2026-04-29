<?php

use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->Username = getenv('MAIL_USER');
$mail->Password = getenv('MAIL_PASS');

$mail->isHTML(true);

return $mail;