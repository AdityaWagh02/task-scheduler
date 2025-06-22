<?php
session_start();
require_once 'functions.php';

// TODO: Implement the task scheduler, email form and logic for email registration.

// In HTML, you can add desired wrapper `<div>` elements or other elements to style the page. Just ensure that the following elements retain their provided IDs.

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['task-name'])) {
		addTask($_POST['task-name']);
		$_SESSION['message'] = "Task added successfully!";
	} elseif (isset($_POST['email'])) {
		$email = $_POST['email'];
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$result = subscribeEmail($email);
			if ($result) {
				$_SESSION['message'] = " Success! Please check your email for the verification link.";
			} else {
				$_SESSION['message'] = " Failed to send verification email. Please try again.";
			}
		} else {
			$_SESSION['message'] = " Invalid email address.";
		}
	} elseif (isset($_POST['toggle'], $_POST['task_id'])) {
		markTaskAsCompleted($_POST['task_id'], true);
		$_SESSION['message'] = "Task marked as completed.";
	} elseif (isset($_POST['untoggle'], $_POST['task_id'])) {
		markTaskAsCompleted($_POST['task_id'], false);
		$_SESSION['message'] = "Task marked as incomplete.";
	} elseif (isset($_POST['delete'], $_POST['task_id'])) {
		deleteTask($_POST['task_id']);
		$_SESSION['message'] = "Task deleted.";
	}

	header("Location: index.php");
	exit;
}


$tasks = getAllTasks();

?>
<!DOCTYPE html>
<html>

<head>
	<!-- Implement Header !-->
	<title>Task Planner</title>
	<style>
		.completed {
			text-decoration: line-through;
			opacity: 0.6;
		}
	</style>
</head>

<body>
	<?php if (isset($_SESSION['message'])): ?>
		<div class="message-box">
			<?php echo htmlspecialchars($_SESSION['message']); ?>
		</div>
		<?php unset($_SESSION['message']); ?>
	<?php endif; ?>

	<h1>Task Scheduler</h1>

	<!-- Add Task Form -->
	<form method="POST" action="">
		<!-- Implement Form !-->
		<input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
		<button type="submit" id="add-task">Add Task</button>
	</form>

	<!-- Tasks List -->
	<ul id="tasks-list">
		<!-- Implement Tasks List (Your task item must have below
		provided elements you can modify there position, wrap them
		in another container, or add styles but they must contain
		specified classnames and input type )!-->
		<?php foreach ($tasks as $task): ?>
			<li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
				<form method="POST" action="" style="display:inline;">
					<input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
					<input type="hidden" name="<?php echo $task['completed'] ? 'untoggle' : 'toggle'; ?>" value="1">
					<input type="checkbox" class="task-status" onchange="this.form.submit()" <?php echo $task['completed'] ? 'checked' : ''; ?>>
				</form>
				<?php echo htmlspecialchars($task['name']); ?>
				<form method="POST" action="" style="display:inline;">
					<input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
					<button class="delete-task" name="delete">Delete</button>
				</form>
			</li>
		<?php endforeach; ?>
	</ul>

	<!-- Subscription Form -->
	<form method="POST" action="">
		<!-- Implement Form !-->
		<input type="email" name="email" placeholder="Enter your email" required>
		<button type="submit" id="submit-email">Subscribe</button>
	</form>

</body>

</html>