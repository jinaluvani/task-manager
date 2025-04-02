<?php
session_start();
require_once 'classes/Task.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task = new Task();
$user_id = $_SESSION['user_id'];
$isAdmin = ($_SESSION['role'] === 'admin');


$tasks = $task->getTasks($user_id, $isAdmin);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h2>My Tasks</h2>


<table border="1">
    <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Due Date</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($tasks as $task): ?>
    <tr>
        <td><?= htmlspecialchars($task['title']) ?></td>
        <td><?= htmlspecialchars($task['description']) ?></td>
        <td><?= htmlspecialchars($task['due_date']) ?></td>
        <td><?= htmlspecialchars($task['priority']) ?></td>
        <td id = "status"><?= htmlspecialchars($task['status']) ?></td>
        <td>
            <a href="edit_task.php?id=<?= $task['id'] ?>" id="edit-option">Edit</a> | 
            <a href="delete_task.php?id=<?= $task['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            <?php if ($task['status'] !== 'Completed'): ?>
                <button class="complete-task" data-id="<?= $task['id'] ?>">Mark as Completed</button>
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<script>
document.querySelectorAll('.complete-task').forEach(button => {
    button.addEventListener('click', function () {
        let taskId = this.getAttribute('data-id');
        let buttonElement = this;
        let row = buttonElement.closest('tr');
        let statusCell = row.querySelector('td#status');

        fetch('mark_task_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'task_id=' + taskId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                buttonElement.style.display = 'none';

                statusCell.textContent = 'Completed';
            } else {
                alert(data.message || "Failed to update task.");
            }
        });
    });
});
</script>

</body>
</html>
