<?php
require_once 'classes/User.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User();
    if ($user->register($_POST['name'], $_POST['email'], $_POST['password'])) {
        header("Location: login.php?registered=success");
    } else {
        echo "Registration failed!";
    }
}
?>
<form method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Register</button>
</form>
