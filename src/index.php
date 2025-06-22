<?php
require_once 'functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        $task_name = trim($_POST['task-name']);
        if (!empty($task_name)) {
            addTask($task_name);
        }
    }
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            subscribeEmail($email);
        }
    }
    
    // Handle AJAX requests for task actions
    if (isset($_POST['action']) && isset($_POST['task_id'])) {
        header('Content-Type: application/json');
        $response = ['success' => false];
        
        switch ($_POST['action']) {
            case 'toggle':
                $task_id = $_POST['task_id'];
                $completed = $_POST['completed'] === 'true';
                $response['success'] = markTaskAsCompleted($task_id, $completed);
                break;
            case 'delete':
                $task_id = $_POST['task_id'];
                $response['success'] = deleteTask($task_id);
                break;
        }
        
        echo json_encode($response);
        exit;
    }
}

$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Planner</title>
    <style>
        .completed { text-decoration: line-through; opacity: 0.7; }
        #tasks-list { list-style: none; padding: 0; }
        .task-item { margin: 5px 0; padding: 8px; border: 1px solid #ddd; display: flex; align-items: center; }
        .task-name { flex-grow: 1; margin: 0 10px; }
        form { margin: 20px 0; }
        input[type="text"], input[type="email"] { padding: 8px; width: 300px; }
        button { padding: 8px 15px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Task Planner</h1>
    
    <h2>Add Task</h2>
    <form method="POST">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>
    
    <h2>Tasks</h2>
    <ul id="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>" data-task-id="<?= $task['id'] ?>">
                <input type="checkbox" class="task-status" <?= $task['completed'] ? 'checked' : '' ?>>
                <span class="task-name"><?= htmlspecialchars($task['name']) ?></span>
                <button class="delete-task">Delete</button>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <h2>Email Subscription</h2>
    <form method="POST">
        <input type="email" name="email" required placeholder="Enter your email">
        <button type="submit" id="submit-email">Subscribe</button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle task status toggling
            document.querySelectorAll('.task-status').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const taskItem = this.closest('.task-item');
                    const taskId = taskItem.dataset.taskId;
                    const completed = this.checked;
                    
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=toggle&task_id=${taskId}&completed=${completed}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            taskItem.classList.toggle('completed', completed);
                        } else {
                            this.checked = !completed;
                        }
                    });
                });
            });
            
            // Handle task deletion
            document.querySelectorAll('.delete-task').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const taskItem = this.closest('.task-item');
                    const taskId = taskItem.dataset.taskId;
                    
                    if (confirm('Are you sure you want to delete this task?')) {
                        fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=delete&task_id=${taskId}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                taskItem.remove();
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
