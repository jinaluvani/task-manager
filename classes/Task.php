<?php
require_once __DIR__ .'/../config/config.php';

class Task {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createTask($user_id, $title, $description, $due_date, $priority) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, description, due_date, priority) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $title, $description, $due_date, $priority]);
    }

    public function getTasks($user_id, $isAdmin) {
        if ($isAdmin) {
            $stmt = $this->conn->prepare("SELECT * FROM tasks");
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
            $stmt->execute([$user_id]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEditedTasks($task_id) {
        
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTask($task_id, $title, $description, $due_date, $priority, $status) {
        $stmt = $this->conn->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, priority = ?, status = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $due_date, $priority, $status, $task_id]);
    }

    public function deleteTask($task_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        return $stmt->execute([$task_id, $user_id]);
    }

    public function markTaskAsCompleted($task_id, $user_id) {
        $stmt = $this->conn->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ? AND user_id = ?");
        return $stmt->execute([$task_id, $user_id]);
    }

    
}
?>
