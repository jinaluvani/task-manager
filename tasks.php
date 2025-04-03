<?php
session_start();
require_once 'classes/Task.php';
require_once 'classes/Comment.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$task = new Task();
$user_id = $_SESSION['user_id'];
$isAdmin = ($_SESSION['role'] === 'admin');

$task->updateOverdueTasks(); 
$tasks = $task->getTasks($user_id, $isAdmin);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Manager</title>
    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Tasks</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['description']) ?></td>
                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                    <td><?= htmlspecialchars($task['priority']) ?></td>
                    <td id="status" class="font-weight-bold text-<?= $task['status'] === 'Completed' ? 'success' : 'warning' ?>">
                        <?= htmlspecialchars($task['status']) ?>
                    </td>
                    <td><?= $task['assigned_user'] ? htmlspecialchars($task['assigned_user']) : 'Unassigned' ?></td>
                    <td>
                        <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-info btn-sm">Edit</a>
                        <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        <?php if ($task['status'] !== 'Completed'): ?>
                            <button class="btn btn-success btn-sm complete-task" data-id="<?= $task['id'] ?>">Mark as Completed</button>
                        <?php endif; ?>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <h5>Comments</h5>
                        <div id="comments-<?= $task['id'] ?>">
                            <?php foreach ($task['comments'] as $comment): ?>
                                <p><strong><?= htmlspecialchars($comment['name']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
                            <?php endforeach; ?>
                        </div>
                        <textarea id="comment-text-<?= $task['id'] ?>" class="form-control" placeholder="Add a comment..."></textarea>
                        <button class="btn btn-primary btn-sm mt-2 add-comment" data-task="<?= $task['id'] ?>">Post Comment</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="main.js"></script>
<script>

$(document).ready(function () {
    $(".add-comment").click(function () {
        let taskId = $(this).data("task");
        let commentText = $("#comment-text-" + taskId).val().trim();

        if (commentText === "") {
            alert("Please enter a comment.");
            return;
        }

        $.post("submit_comment.php", { task_id: taskId, comment: commentText }, function (response) {
            let res = JSON.parse(response);
            if (res.success) {
                $("#comments-" + taskId).append("<p><strong>You:</strong> " + commentText + "</p>");
                $("#comment-text-" + taskId).val("");
            } else {
                alert("Failed to add comment.");
            }
        });
    });
});
</script>

</body>
</html>
