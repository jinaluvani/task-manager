<?php
session_start();
require_once 'classes/Task.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task = new Task();
$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    if ($task->deleteTask($task_id, $user_id, $_SESSION['role'])) {
        header("Location: tasks.php?deleted=1");
    } else {
        echo "Unauthorized!";
    }
}
?>
