<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Autoload using Composer
require 'vendor/autoload.php';

// Create instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP server configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aditya.wagh0211@gmail.com';       // Your Gmail
    $mail->Password   = 'hmzgfipvdmwntyv';                 // App password (remove spaces!)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & receiver
    $mail->setFrom('aditya.wagh0211@gmail.com', 'Task Reminder System');
    $mail->addAddress('santrabilla72@gmail.com', 'You');  // Change this to your recipient

    // Email content
    $mail->isHTML(true);
    $mail->Subject = '🔔 Task Reminder';
    $mail->Body    = '<h3>Don\'t forget to complete your pending tasks today!</h3>';

    // Send email
    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}
