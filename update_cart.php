<?php
session_start();
include 'DB.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: LOGIN.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['id'];
    $action = $_POST['action'];
    
    if ($action === 'increase') {
        $_SESSION['cart'][$product_id]['quantity']++;
    } elseif ($action === 'decrease') {
        if ($_SESSION['cart'][$product_id]['quantity'] > 1) {
            $_SESSION['cart'][$product_id]['quantity']--;
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
}

header('Location: CART.php');
exit;
?>