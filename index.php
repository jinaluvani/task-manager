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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    

    <style>
        
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <header>
        <h1>Task Management System</h1>
    </header>

    <?php if ($isLoggedIn): ?>

        <div class="panel panel-primary">
            <div class="panel-heading">Task Overview</div>
            <div class="panel-body">
                <table class="table table-bordered" id="dataTables-task">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <?php if ($isAdmin): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tasks = $task->getTasks($_SESSION['user_id'], $isAdmin);
                        $users = $user->getAllUsers();
                        foreach ($tasks as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['title']) ?></td>
                                <td><?= htmlspecialchars($t['due_date']) ?></td>
                                <td><span class="label label-<?= $t['status'] === 'Completed' ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($t['status']) ?></span>
                                </td>
                                <td><?= $t['assigned_user'] ? htmlspecialchars($t['assigned_user']) : 'Unassigned' ?></td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <select class="form-control assign-task" data-id="<?= $t['id'] ?>">
                                            <option value="">Select User</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


    <?php else: ?>
        <div class="alert alert-warning">You are not logged in. Please <a href="login.php">Login</a> or <a href="register.php">Register</a>.</div>
    <?php endif; ?>
</div>

<script src="main.js"></script>
<script>
$(document).ready(function() {
    $('#dataTables-task').DataTable();
});


$(document).on('change', '.assign-task', function () {
    var taskId = $(this).data('id');
    var userId = $(this).val();

    $.post('assign_task.php', { task_id: taskId, assigned_to: userId }, function (response) {
        alert(response.message);
        location.reload();
    }, 'json');
});

</script>

</body>
</html>
