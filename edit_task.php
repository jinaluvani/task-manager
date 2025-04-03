<?php
session_start();
require_once 'classes/Task.php';
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$task = new Task();
$user_id = $_SESSION['user_id'];
$isAdmin = $user_id && $_SESSION['role'] === 'admin';

$user = new User($pdo);
$users = $user->getAllUsers();

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
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed!");
    }
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $assigned_to = isset($_POST['assigned_to']) ? $_POST['assigned_to'] : null;

    if ($task->updateTask($task_id, $title, $description, $due_date, $priority, $status, $assigned_to)) {
        header("Location: tasks.php?updated=1");
        exit();
    } else {
        echo "Failed to update task!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Task</title>
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
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h4 class="panel-title">Edit Task</h4>
        </div>
        <div class="panel-body">
            <form method="POST" id="editTaskForm">
                <input type="hidden" id="csrf_token" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <label class="control-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($taskData['title']) ?>" required>
                    <small id="titleError" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="control-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"><?= htmlspecialchars($taskData['description']) ?></textarea>
                    <small id="descriptionError" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="control-label">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="<?= $taskData['due_date'] ?>" required>
                    <small id="dateError" class="text-danger"></small>
                </div>

                <div class="form-group">
                    <label class="control-label">Priority</label>
                    <select name="priority" class="form-control">
                        <option value="Low" <?= $taskData['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= $taskData['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High" <?= $taskData['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="Pending" <?= $taskData['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="In Progress" <?= $taskData['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Past Due" <?= $taskData['status'] == 'Past Due' ? 'selected' : '' ?>>Past Due</option>
                        <option value="Completed" <?= $taskData['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>


                <?php if ($isAdmin): ?>
                    <div class="form-group">
                        <label class="control-label">Assign To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">Unassigned</option>
                            <?php
                            foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= $taskData['assigned_to'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">Update Task</button>
                    <a href="tasks.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="main.js"></script>
</body>
</html>