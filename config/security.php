<?php
// config/security.php

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("CSRF Validation Failed");
    }
}

function sanitize($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

function csrf_field() {
    echo '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}
?>
