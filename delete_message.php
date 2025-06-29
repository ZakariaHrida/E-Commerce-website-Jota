<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: MESSAGES.php');
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['message_deleted'] = true;
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to delete message.";
}

header('Location: MESSAGES.php');
exit;
?>