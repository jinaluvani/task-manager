<?php
require_once __DIR__ . '/../config/config.php';

class Comment {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addComment($task_id, $user_id, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO comments (task_id, user_id, comment) VALUES (?, ?, ?)");
        return $stmt->execute([$task_id, $user_id, $comment]);
    }

    public function getComments($task_id) {
        $stmt = $this->conn->prepare("SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.task_id = ? ORDER BY c.created_at DESC");
        $stmt->execute([$task_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
