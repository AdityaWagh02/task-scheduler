<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';
$success = false;

if (!empty($email) && !empty($code)) {
    $success = verifySubscription($email, $code);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subscription Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; }
        .message { padding: 15px; margin: 20px 0; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        a { color: #007BFF; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2 id="verification-heading">📩 Subscription Verification</h2>
    
    <?php if ($success): ?>
        <div class="message success">
            <p>Your email has been verified successfully. You will now receive task reminders.</p>
        </div>
    <?php else: ?>
        <div class="message error">
            <p>❌ Invalid verification link. Please try subscribing again.</p>
        </div>
    <?php endif; ?>
    
    <p><a href="index.php">⬅️ Return to Task Planner</a></p>
</body>
</html>
