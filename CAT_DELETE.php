<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

$category_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$category_id]);
    $_SESSION['message'] = "Category deleted successfully";
} else {
    $_SESSION['error'] = "Category not found";
}
header('Location: CAT_LIST.php');
exit;
?>