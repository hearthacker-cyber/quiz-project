<?php
require_once '../config/config.php';
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
session_destroy();
header("Location: " . BASE_URL . "user/login.php");
exit;
?>
