<?php
require_once 'config/config.php';
require_once 'classes/Task.php';
require_once 'classes/User.php';
require 'PHPMailer/PHPMailer/src/PHPMailer.php';
require 'PHPMailer/PHPMailer/src/SMTP.php';
require 'PHPMailer/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$task = new Task();
$user = new User();

$users = $user->getAllUsers();

foreach ($users as $user) {
    $userId = $user['id'];
    $userEmail = $user['email'];
    $userName = $user['name'];

    $tasks = $task->getDueAndPastDueTasks($userId);

    if (!empty($tasks)) {
        $subject = "Task Reminder: Pending Tasks Notification";
        $message = "Hello $userName,<br><br>Here are your pending tasks:<br><ul>";

        foreach ($tasks as $t) {
            $message .= "<li>{$t['title']} (Due: {$t['due_date']})</li>";
        }

        $message .= "</ul><br>Please complete your tasks as soon as possible.<br><br>Best Regards,<br>Task Management System";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'youremail@gmail.com'; // Your email
            $mail->Password = 'password'; // Your email password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('jinalluvani@gmail.com', 'Task Management System');
            $mail->addAddress($userEmail, $userName);
            
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            echo "Email sent successfully to $userEmail <br>";
        } catch (Exception $e) {
            echo "Email could not be sent to $userEmail. Error: {$mail->ErrorInfo}<br>";
        }
    }
}
?>