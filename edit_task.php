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
    $taskData = $task->getEditedTasks($task_id);
    if (!$taskData) {
        echo "Unauthorized access!";
        exit();
    }
    $taskData = $taskData[0];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    if ($task->updateTask($task_id, $title, $description, $due_date, $priority, $status)) {
        header("Location: tasks.php?updated=1");
        exit();
    } else {
        echo "Failed to update task!";
    }
}
?>

<form method="POST">
    <input type="text" name="title" value="<?= $taskData['title'] ?>" required>
    <textarea name="description"><?= $taskData['description'] ?></textarea>
    <input type="date" name="due_date" value="<?= $taskData['due_date'] ?>" required>
    <select name="priority">
        <option value="Low" <?= $taskData['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
        <option value="Medium" <?= $taskData['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
        <option value="High" <?= $taskData['priority'] == 'High' ? 'selected' : '' ?>>High</option>
    </select>
    <select name="status">
        <option value="Pending" <?= $taskData['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="In Progress" <?= $taskData['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="Past Due" <?= $taskData['status'] == 'Past Due' ? 'selected' : '' ?>>Past Due</option>
        <option value="Completed" <?= $taskData['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
    </select>
    <button type="submit">Update Task</button>
</form>
