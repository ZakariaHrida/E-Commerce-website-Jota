<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: LOGIN.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$product_id = $_GET['id'];

// Check if product exists
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$product_id]);
$product = $product->fetch();

if (!$product) {
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete product image if exists
    if (!empty($product['image']) && file_exists('img/' . $product['image'])) {
        unlink('img/' . $product['image']);
    }
    
    // Delete product from database
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    
    $_SESSION['message'] = "Product deleted successfully";
    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - JOTA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="flex-between mb-3">
                <h1 class="dashboard-title">Delete Product</h1>
                <a href="list.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="form-container">
                <form method="POST">
                    <div class="alert alert-danger">
                        <h3>Are you sure you want to delete this product?</h3>
                        <p><strong>Product Name:</strong> <?= htmlspecialchars($product['name']) ?></p>
                        <p><strong>Price:</strong> <?= number_format($product['price'], 2) ?> MAD</p>
                        <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
                        
                        <?php if (!empty($product['image'])): ?>
                            <div style="margin-top: 15px;">
                                <img src="img/<?= htmlspecialchars($product['image']) ?>" alt="Product Image" style="max-width: 200px; max-height: 200px;">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Confirm Delete
                        </button>
                        <a href="PRO_LIST.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>