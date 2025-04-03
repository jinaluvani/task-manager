<?php
session_start();
require_once 'config/config.php';
require_once 'classes/Task.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$task = new Task();
$task_id = $_POST['task_id'] ?? null;
$assigned_to = $_POST['assigned_to'] ?? null;

if ($task_id && $assigned_to) {
    $success = $task->assignTask($task_id, $assigned_to);
    echo json_encode(['success' => $success, 'message' => $success ? 'Task assigned successfully' : 'Failed to assign task']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
