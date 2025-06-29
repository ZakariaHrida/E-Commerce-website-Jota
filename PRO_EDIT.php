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
$product = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$product->execute([$product_id]);
$product = $product->fetch();

if (!$product) {
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'] ?? '';
    $short_description = $_POST['short_description'] ?? '';
    
    // Handle file upload
    $image = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Delete old image if exists
        if (!empty($image) && file_exists($uploadDir . $image)) {
            unlink($uploadDir . $image);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ?, short_description = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $category, $description, $short_description, $image, $product_id]);
    
    $_SESSION['message'] = "Product updated successfully";
    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - JOTA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="flex-between mb-3">
                <h1 class="dashboard-title">Edit Product</h1>
                <a href="list.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="price" class="form-label">Price (MAD)</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="Laptop Gamer" <?= $product['category'] === 'Laptop Gamer' ? 'selected' : '' ?>>Laptop Gamer</option>
                            <option value="PC Gamer" <?= $product['category'] === 'PC Gamer' ? 'selected' : '' ?>>PC Gamer</option>
                            <option value="Accessories" <?= $product['category'] === 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control" name="short_description" id="short_description" rows="2"><?= htmlspecialchars($product['short_description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Full Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image" class="form-label">Product Image</label>
                        <?php if (!empty($product['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="img/<?= htmlspecialchars($product['image']) ?>" alt="Current Product Image" style="max-width: 200px; max-height: 200px;">
                                <br>
                                <label>
                                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                                </label>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <small class="text-muted">Recommended size: 800x800px</small>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
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