<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    require_once "connection.php";
    $conn->query("INSERT INTO lag (user_id, action) VALUES ($user_id, 'User logged out')");
}
$_SESSION = [];
session_destroy();
header('Location: login.php');
exit;
