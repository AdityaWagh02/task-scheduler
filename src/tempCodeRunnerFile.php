<?php
require_once "functions.php";

$verificationMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['task-name'])) {
        addTask(trim($_POST['task-name']));
        header("Location: index.php");
        exit;
    }

    if (!empty($_POST['subscribe']) && !empty($_POST['email'])) {
        ob_start(); // capture simulated email output
        subscribeEmail(trim($_POST['email']));
        $verificationMessage = ob_get_clean();
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Planner</title>
    <style>
        body {
            font-family: Arial;
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #f9f9f9;
        }

        h2 { margin-top: 30px; }

        .task-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 5px 0;
        }

        .task-completed {
            text-decoration: line-through;
            color: gray;
        }

        .message-box {
            background: #e7f3fe;
            border-left: 6px solid #2196F3;
            padding: 10px;
            margin-top: 20px;
        }

        form {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        input[type="text"], input[type="email"] {
            padding: 6px;
            width: 60%;
        }

        button {
            padding: 6px 12px;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

    <h2>📝 Add Task</h2>
    <form method="POST">
        <input type="text" name="task-name" placeholder="Enter new task" required>
        <button type="submit">Add Task</button>
    </form>

    <h2>📋 Task List</h2>
    <ul>
        <?php foreach ($tasks as $index => $task): ?>
            <li class="task-item">
                <span class="<?= $task['completed'] ? 'task-completed' : '' ?>">
                    <?= htmlspecialchars($task['title']) ?>
                </span>
                <?php if (!$task['completed']): ?>
                    <a href="complete_task.php?index=<?= $index ?>">✅ Complete</a>
                <?php endif; ?>
                <a href="delete_task.php?index=<?= $index ?>">🗑️ Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>📧 Subscribe for Task Reminders</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" name="subscribe">Subscribe</button>
    </form>

    <?php if (!empty($verificationMessage)): ?>
        <div class="message-box"><?= $verificationMessage ?></div>
    <?php endif; ?>

</body>
</html>