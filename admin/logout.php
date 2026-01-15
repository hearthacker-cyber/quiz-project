<?php
require_once '../config/config.php';
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
session_destroy();
header("Location: " . BASE_URL . "admin/login.php");
exit;
?>
