<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    requireLogin(); // must be logged in first
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function getUsername() {
    return $_SESSION["username"] ?? "";
}

function getRole() {
    return $_SESSION["role"] ?? "";
}
?>