<?php
require_once 'functions.php';

// TODO: Implement the unsubscription logic.
$email = $_GET['email'] ?? '';
$unsubscribed = false;
if ($email) {
	unsubscribeEmail($email);
	$unsubscribed = true;
}

?>

<!DOCTYPE html>
<html>

<head>
	<!-- Implement Header ! -->
	<title>Unsubscribe</title>
</head>

<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
	<!-- Implemention body -->
	<?php if ($unsubscribed): ?>
		<p>You have been successfully unsubscribed from task reminders.</p>
	<?php else: ?>
		<p>Invalid request. Email not found or not provided.</p>
	<?php endif; ?>
</body>

</html>