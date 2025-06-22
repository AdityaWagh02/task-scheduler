<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';




/**
 * Adds a new task to the task list
 * 
 * @param string $task_name The name of the task to add.
 * @return bool True on success, false on failure.
 */
function addTask(string $task_name): bool
{
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	if (!file_exists($file)) {
		file_put_contents($file, json_encode([]));
	}

	$tasks = json_decode(file_get_contents($file), true);

	// Prevent duplicates (case-insensitive)
	foreach ($tasks as $task) {
		if (strtolower($task['name']) === strtolower($task_name)) {
			return false;
		}
	}

	$tasks[] = [
		'id' => uniqid('task_', true),
		'name' => $task_name,
		'completed' => false
	];

	file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
	return true;
}


/**
 * Retrieves all tasks from the tasks.txt file
 * 
 * @return array Array of tasks. -- Format [ id, name, completed ]
 */
function getAllTasks(): array
{
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	if (!file_exists($file)) {
		return [];
	}
	$tasks = json_decode(file_get_contents($file), true);
	return is_array($tasks) ? $tasks : [];
}


/**
 * Marks a task as completed or uncompleted
 * 
 * @param string  $task_id The ID of the task to mark.
 * @param bool $is_completed True to mark as completed, false to mark as uncompleted.
 * @return bool True on success, false on failure
 */
function markTaskAsCompleted(string $task_id, bool $is_completed): bool
{
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function

	$tasks = getAllTasks();

	foreach ($tasks as &$task) {
		if ($task['id'] === $task_id) {
			$task['completed'] = $is_completed;
			file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
			return true;
		}
	}
	return false;
}


/**
 * Deletes a task from the task list
 * 
 * @param string $task_id The ID of the task to delete.
 * @return bool True on success, false on failure.
 */
function deleteTask(string $task_id): bool
{
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	$tasks = getAllTasks();

	$new_tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);

	if (count($tasks) === count($new_tasks)) return false;

	file_put_contents($file, json_encode(array_values($new_tasks), JSON_PRETTY_PRINT));
	return true;
}


/**
 * Generates a 6-digit verification code
 * 
 * @return string The generated verification code.
 */
function generateVerificationCode(): string
{
	// TODO: Implement this function
	$length = 6;
	return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}



function sendEmail(string $to, string $subject, string $body): bool
{
	$mail = new PHPMailer(true);
	try {
		// SMTP configuration (for Gmail)
		$mail->isSMTP();
		$mail->Host       = 'smtp.gmail.com';
		$mail->SMTPAuth   = true;
		$mail->Username   = ''; // replace with your Gmail
		$mail->Password   = '';     // replace with your app password
		$mail->SMTPSecure = 'tls';
		$mail->Port       = 587;

		// Email content
		$mail->setFrom('enter email address', 'Task Planner'); // sender
		$mail->addAddress($to);                                 // receiver
		$mail->isHTML(true);
		$mail->Subject = $subject;
		$mail->Body    = $body;

		return $mail->send();
	} catch (Exception $e) {
		error_log("Mail Error: " . $mail->ErrorInfo);
		return false;
	}
}

/**
 * Subscribe an email address to task notifications.
 *
 * Generates a verification code, stores the pending subscription,
 * and sends a verification email to the subscriber.
 *
 * @param string $email The email address to subscribe.
 * @return bool True if verification email sent successfully, false otherwise.
 */
function subscribeEmail(string $email): bool
{
	$file = __DIR__ . '/pending_subscriptions.txt';
	// TODO: Implement this function
	$pending = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

	$code = generateVerificationCode();
	$pending[$email] = [
		'code' => $code,
		'timestamp' => time()
	];

	file_put_contents($file, json_encode($pending, JSON_PRETTY_PRINT));

	$link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/verify.php?email=' . urlencode($email) . '&code=' . $code;
	$subject = 'Verify your Task Planner subscription';
	$message = "<p>Click below to verify your subscription:</p>
                <p><a href='$link'>Verify Subscription</a></p>";

	return sendEmail($email, $subject, $message);
}


/**
 * Verifies an email subscription
 * 
 * @param string $email The email address to verify.
 * @param string $code The verification code.
 * @return bool True on success, false on failure.
 */
function verifySubscription(string $email, string $code): bool
{
	$pending_file = __DIR__ . '/pending_subscriptions.txt';
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	$pending = [];
	if (file_exists($pending_file)) {
		$json = file_get_contents($pending_file);
		$pending = json_decode($json, true);
		if (!is_array($pending)) {
			$pending = []; // fallback if decode fails
		}
	}

	// Check if email exists and code matches
	if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) {
		return false;
	}

	// Remove verified email from pending list
	unset($pending[$email]);
	file_put_contents($pending_file, json_encode($pending, JSON_PRETTY_PRINT));

	// Load subscribers safely
	$subscribers = [];
	if (file_exists($subscribers_file)) {
		$json = file_get_contents($subscribers_file);
		$subscribers = json_decode($json, true);
		if (!is_array($subscribers)) {
			$subscribers = []; // fallback if decode fails
		}
	}

	// Add email to subscribers if not already present
	if (!in_array($email, $subscribers)) {
		$subscribers[] = $email;
		file_put_contents($subscribers_file, json_encode($subscribers, JSON_PRETTY_PRINT));
	}

	return true;
}


/**
 * Unsubscribes an email from the subscribers list
 * 
 * @param string $email The email address to unsubscribe.
 * @return bool True on success, false on failure.
 */
function unsubscribeEmail(string $email): bool
{
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	$subscribers = file_exists($subscribers_file) ? json_decode(file_get_contents($subscribers_file), true) : [];

	$new_list = array_filter($subscribers, fn($e) => $e !== $email);

	if (count($subscribers) === count($new_list))
		return false;

	file_put_contents($subscribers_file, json_encode(array_values($new_list), JSON_PRETTY_PRINT));
	return true;
}

/**
 * Sends task reminders to all subscribers
 * Internally calls  sendTaskEmail() for each subscriber
 */
function sendTaskReminders(): void
{
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	$subscribers = file_exists($subscribers_file)
		? json_decode(file_get_contents($subscribers_file), true)
		: [];

	// Filter to include only verified users
	$verifiedSubscribers = array_filter($subscribers, function ($subscriber) {
		return is_array($subscriber) && !empty($subscriber['email']) && ($subscriber['verified'] ?? false);
	});

	// Fetch only pending tasks
	$tasks = array_filter(getAllTasks(), fn($task) => !$task['completed']);

	// Send task email to each verified subscriber
	foreach ($verifiedSubscribers as $subscriber) {
		sendTaskEmail($subscriber['email'], $tasks);
	}
}


/**
 * Sends a task reminder email to a subscriber with pending tasks.
 *
 * @param string $email The email address of the subscriber.
 * @param array $pending_tasks Array of pending tasks to include in the email.
 * @return bool True if email was sent successfully, false otherwise.
 */
function sendTaskEmail(string $email, array $pending_tasks): bool
{
	$subject = 'Task Planner - Pending Tasks Reminder';
	// TODO: Implement this function


	$message = '<h2>Pending Tasks Reminder</h2>';
	$message .= '<p>Here are the pending tasks:</p><ul>';
	foreach ($pending_tasks as $task) {
		$message .= '<li>' . htmlspecialchars($task['name']) . '</li>';
	}
	$message .= '</ul>';

	// Encode the email for unsubscribe link
	$encoded_email = urlencode(base64_encode($email));
	$unsubscribe_link = 'http://' . $_SERVER['HTTP_HOST'] . '/unsubscribe.php?email=' . $encoded_email;

	$message .= "<p>If you no longer wish to receive these reminders, <a href='$unsubscribe_link'>Unsubscribe</a>.</p>";

	return sendEmail($email, $subject, $message);

}
