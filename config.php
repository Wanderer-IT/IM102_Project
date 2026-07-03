<?php
// Database connection
// Update these values to match your local phpMyAdmin / MySQL setup
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'im102finals';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
?>
