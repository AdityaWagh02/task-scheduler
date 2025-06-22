<?php
require_once 'functions.php';

// Send task reminders to all subscribers.
sendTaskReminders();

// Log execution
file_put_contents(__DIR__ . '/cron.log', date('Y-m-d H:i:s') . " - Sent reminders\n", FILE_APPEND);
