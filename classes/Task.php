<?php
require_once __DIR__ .'/../config/config.php';

class Task {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createTask($user_id, $title, $description, $due_date, $priority, $assigned_to = null) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (user_id, title, description, due_date, priority, assigned_to) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$user_id, $title, $description, $due_date, $priority, $assigned_to]);
    }

    public function getTasks($user_id, $isAdmin) {
        if ($isAdmin == 1) {
            $stmt = $this->conn->prepare("SELECT tasks.*, users.name AS assigned_user FROM tasks 
                                          LEFT JOIN users ON tasks.assigned_to = users.id");
            $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("SELECT tasks.*, users.name AS assigned_user FROM tasks 
                                          LEFT JOIN users ON tasks.assigned_to = users.id
                                          WHERE tasks.user_id = ? OR tasks.assigned_to = ?");
            $stmt->execute([$user_id, $user_id]);
        }
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch comments for each task
        require_once 'Comment.php';
        $comment = new Comment();
        foreach ($tasks as &$task) {
            $task['comments'] = $comment->getComments($task['id']);
        }
        return $tasks;
    }

    public function getEditedTasks($task_id) {
        
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateTask($task_id, $title, $description, $due_date, $priority, $status, $assigned_to) {
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

    public function updateOverdueTasks() {
        $stmt = $this->conn->prepare("UPDATE tasks SET status = 'Past Due' WHERE due_date < CURDATE() AND status != 'Completed'");
        $stmt->execute();
    }

    public function getDueAndPastDueTasks($user_id) {
        $stmt = $this->conn->prepare("SELECT title, due_date FROM tasks WHERE user_id = ? AND status != 'Completed' AND due_date <= CURDATE()");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignTask($task_id, $assigned_to) {
        $stmt = $this->conn->prepare("UPDATE tasks SET assigned_to = ? WHERE id = ?");
        return $stmt->execute([$assigned_to, $task_id]);
    }

    
}
?>
