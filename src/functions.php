<?php

// -------------------- TASK FUNCTIONS --------------------

function loadTasks() {
    return file_exists('tasks.txt') ? json_decode(file_get_contents('tasks.txt'), true) : [];
}

function saveTasks($tasks) {
    file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
}

function addTask($task_name) {
    $tasks = loadTasks();
    foreach ($tasks as $task) {
        if (strtolower($task['name']) === strtolower($task_name)) {
            return; // Duplicate
        }
    }
    $tasks[] = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];
    saveTasks($tasks);
}

function getAllTasks() {
    return loadTasks();
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = loadTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }
    saveTasks($tasks);
}

function deleteTask($task_id) {
    $tasks = loadTasks();
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);
    saveTasks(array_values($tasks));
}

// -------------------- EMAIL VERIFICATION --------------------

function generateVerificationCode() {
    return strval(rand(100000, 999999));
}

function subscribeEmail($email) {
    $pending = file_exists('pending_subscriptions.txt') 
        ? json_decode(file_get_contents('pending_subscriptions.txt'), true)
        : [];

    if (isset($pending[$email])) return;

    $code = generateVerificationCode();
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];
    file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));

    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $link = "http://$host$path/verify.php?email=" . urlencode($email) . "&code=$code";

    $subject = "Verify subscription to Task Planner";
    $message = "<p>Click the link below to verify your subscription to Task Planner:</p>
               <p><a id='verification-link' href='$link'>Verify Subscription</a></p>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@example.com";

    mail($email, $subject, $message, $headers);
}

function verifySubscription($email, $code) {
    $pending = file_exists('pending_subscriptions.txt') 
        ? json_decode(file_get_contents('pending_subscriptions.txt'), true)
        : [];

    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) return false;

    unset($pending[$email]);
    file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));

    $subscribers = file_exists('subscribers.txt') 
        ? json_decode(file_get_contents('subscribers.txt'), true) 
        : [];

    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents('subscribers.txt', json_encode($subscribers, JSON_PRETTY_PRINT));
    }
    return true;
}

function unsubscribeEmail($email) {
    $subscribers = file_exists('subscribers.txt') 
        ? json_decode(file_get_contents('subscribers.txt'), true) 
        : [];

    $subscribers = array_filter($subscribers, fn($e) => $e !== $email);
    file_put_contents('subscribers.txt', json_encode(array_values($subscribers), JSON_PRETTY_PRINT));
}

// -------------------- REMINDER SYSTEM --------------------

function sendTaskReminders() {
    $tasks = array_filter(loadTasks(), fn($t) => !$t['completed']);
    if (empty($tasks)) return;

    $subscribers = file_exists('subscribers.txt') 
        ? json_decode(file_get_contents('subscribers.txt'), true)
        : [];

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $tasks);
    }
}

function sendTaskEmail($email, $pending_tasks) {
    $taskHtml = "";
    foreach ($pending_tasks as $task) {
        $taskHtml .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }

    $host = $_SERVER['HTTP_HOST'];
    $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $unsubscribeLink = "http://$host$path/unsubscribe.php?email=" . urlencode($email);

    $message = "<h2>Pending Tasks Reminder</h2>
        <p>Here are the current pending tasks:</p>
        <ul>$taskHtml</ul>
        <p><a id='unsubscribe-link' href='$unsubscribeLink'>Unsubscribe from notifications</a></p>";

    $subject = "Task Planner - Pending Tasks Reminder";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com";

    mail($email, $subject, $message, $headers);
}
