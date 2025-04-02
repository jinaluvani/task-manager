<?php
require_once __DIR__ .'/../config/config.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($name, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $hashedPassword, $role]);
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: login.php");
    }

}
?>
