<?php
session_start();
require_once 'config/config.php'; 
require_once 'classes/Task.php';
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$isAdmin = $user_id && $_SESSION['role'] === 'admin';
$task = new Task($pdo);
$user = new User($pdo);
$users = $user->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $assignedUserId = $isAdmin ? $_POST['assigned_user_id'] ?? null : $user_id;
    
    if (!$isAdmin) {
        $taskCreated = $task->createTask($user_id, $title, $description, $due_date, $priority, $user_id);
    } else {
        $taskCreated = $task->createTask($user_id, $title, $description, $due_date, $priority, $assignedUserId);
    }

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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading text-center">
                    <h3>Create New Task</h3>
                </div>
                <div class="panel-body">
                    <form method="POST" action="task_create.php" id="createTaskForm">
                        <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="title" id="title" required>
                            <small id="titleError" class="text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" name="description" id="description" required></textarea>
                            <small id="descriptionError" class="text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date:</label>
                            <input type="date" class="form-control" name="due_date" id="due_date" required>
                            <small id="dateError" class="text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority:</label>
                            <select class="form-control" name="priority" id="priority" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <?php if ($isAdmin): ?>
                            <div class="form-group">
                            <label for="user">Assign To:</label>
                                        <select class="form-control assign-task" id="user" name="assigned_user_id" data-id="<?= $t['id'] ?>">
                                            <option value="">Select User</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                            </div>
                                <?php endif; ?>

                        <button type="submit" class="btn btn-success btn-block">Create Task</button>
                    </form>
                </div>
            </div>
            <a href="tasks.php" class="btn btn-default btn-block">Back to My Tasks</a>
        </div>
    </div>
</div>
<script src="main.js"></script>
    
</body>
</html>
