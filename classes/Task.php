<?php
require_once __DIR__ .'/../config/config.php';

class Task {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
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

    
}
?>
