<?php
session_start();
require_once 'config/config.php'; 
require_once 'classes/Task.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task = new Task($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    
    $taskCreated = $task->createTask($_SESSION['user_id'], $title, $description, $due_date, $priority);

    if ($taskCreated) {
        echo "Task created successfully!";
        header("Location: index.php");
        exit();
    } else {
        echo "Error creating task.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Task</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <header>
        <h1>Create New Task</h1>
    </header>

    <form method="POST" action="task_create.php">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" id="due_date" required>

        <label for="priority">Priority:</label>
        <select name="priority" id="priority" required>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>

        <button type="submit">Create Task</button>
    </form>
    
    <a href="tasks.php">Back to My Tasks</a>
</div>

</body>
</html>
