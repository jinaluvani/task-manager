<?php
// session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && $_SESSION['role'] === 'admin';
?>

<nav class="navbar navbar-inverse">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">Task Management</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?= htmlspecialchars($_SESSION['name']) ?></a></li>
                        <li><a href="tasks.php"><span class="glyphicon glyphicon-tasks"></span> My Tasks</a></li>
                        <li><a href="task_create.php"><span class="glyphicon glyphicon-plus"></span> Add Task</a></li>
                        <?php if ($isAdmin): ?>
                            <li><a href="all_users.php">All Users</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                        <li><a href="register.php"><span class="glyphicon glyphicon-user"></span> Register</a></li>
                    <?php endif; ?>
                    <li>
                        <button id="dark-mode-toggle" class="btn btn-default navbar-btn">
                            <span id="theme-icon" class="glyphicon glyphicon-adjust"></span> Toggle Theme
                        </button>
                    </li>
                </ul>
            </div>
        </nav>

