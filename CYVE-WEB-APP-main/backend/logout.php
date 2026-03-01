<?php
include 'config.php';

if (isset($_SESSION['user_id'])) {
    log_activity($_SESSION['user_id'], 'logout', 'User logged out');
}

session_destroy();
header('Location: ../public/index.php');
exit();
?>
