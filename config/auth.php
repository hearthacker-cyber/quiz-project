<?php
// config/auth.php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "user/login.php");
        exit;
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: " . BASE_URL . "admin/login.php");
        exit;
    }
}

function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit;
}
?>
