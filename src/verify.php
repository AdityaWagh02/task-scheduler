<?php
require_once 'functions.php';

// TODO: Implement verification logic.
$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';
$verified = false;

if ($email && $code) {
	$verified = verifySubscription($email, $code);
}

?>

<!DOCTYPE html>
<html>

<head>
	<!-- Implement Header ! -->
	<title>Email Verification</title>
</head>

<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="verification-heading">Subscription Verification</h2>
	<!-- Implemention body -->
	<?php if ($verified): ?>
		<p>Your subscription has been successfully verified. You will now receive task reminders.</p>
	<?php else: ?>
		<p>Verification failed. The link may be invalid or expired.</p>
	<?php endif; ?>
</body>

</html>