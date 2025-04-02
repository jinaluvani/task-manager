<?php
session_start();
require_once 'config/config.php';
require_once 'classes/Task.php';
require_once 'classes/User.php';

$task = new Task($pdo);
$user = new User($pdo);

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management System</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <header>
        <h1>Task Management System</h1>
        <button id="dark-mode-toggle">Toggle Dark Mode</button>
    </header>

    <?php if ($isLoggedIn): ?>
        
        <nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Task Management</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($_SESSION['name']) ?></a></li>
                        <li><a href="tasks.php"><span class="glyphicon glyphicon-tasks"></span> My Tasks</a></li>
                        <li><a href="task_create.php"><span class="glyphicon glyphicon-plus"></span> Add Task</a></li>
                        <?php if ($isAdmin): ?>
                            <li><a href="admin_dashboard.php"><span class="glyphicon glyphicon-cog"></span> Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                        <li><a href="register.php"><span class="glyphicon glyphicon-user"></span> Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <h2>Task Overview</h2>
        <table border="1">
            <tr>
                <th>Title</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
            <?php
            $tasks = $task->getTasks($_SESSION['user_id'], $isAdmin);
            foreach ($tasks as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['title']) ?></td>
                    <td><?= htmlspecialchars($t['due_date']) ?></td>
                    <td id = "status"><?= htmlspecialchars($t['status']) ?></td>
                    <td>
                        <?php if ($t['status'] !== 'Completed'): ?>
                            <button class="complete-task" data-id="<?= $t['id'] ?>">Mark as Completed</button>
                        <?php endif; ?>
                    </td>

                </tr>
            <?php endforeach; ?>
        </table>

    <?php else: ?>
        <div class="alert alert-warning">You are not logged in. Please <a href="login.php">Login</a> or <a href="register.php">Register</a>.</div>
    <?php endif; ?>
</div>

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

                // Update the status column to "Completed"
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
