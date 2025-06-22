<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
$success = false;

if (!empty($email)) {
    $success = unsubscribeEmail($email);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unsubscribe from Task Updates</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .message { padding: 15px; margin: 20px 0; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
        a { color: #337ab7; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Do not modify the ID of the heading -->
    <h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>

    <?php if ($success): ?>
        <div class="message success">
            <p>You have been unsubscribed successfully. You will no longer receive task reminders.</p>
        </div>
    <?php else: ?>
        <div class="message error">
            <p>Email not found in our subscribers list or the unsubscribe link is invalid.</p>
        </div>
    <?php endif; ?>

    <p><a href="index.php">⬅️ Return to Task Planner</a></p>
</body>
</html>
