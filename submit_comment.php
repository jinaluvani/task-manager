<?php
session_start();
require_once 'classes/Comment.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['task_id']) || !isset($_POST['comment'])) {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
    exit();
}

$task_id = $_POST['task_id'];
$user_id = $_SESSION['user_id'];
$comment_text = trim($_POST['comment']);

if ($comment_text === "") {
    echo json_encode(["success" => false, "message" => "Comment cannot be empty"]);
    exit();
}

$comment = new Comment();
$success = $comment->addComment($task_id, $user_id, $comment_text);

echo json_encode(["success" => $success]);
?>
