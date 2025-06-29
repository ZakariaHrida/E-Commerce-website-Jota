<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: LOGIN.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'] ?? '';
    $short_description = $_POST['short_description'] ?? '';
    

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'img';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $filename;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category, description, short_description, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $category, $description, $short_description, $image]);
    
    $_SESSION['message'] = "Product added successfully";
    header('Location: list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - JOTA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="flex-between mb-3">
                <h1 class="dashboard-title">Add New Product</h1>
                <a href="PRO_LIST.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <div class="form-container">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter product name" required>
                    </div>
                    <div class="form-group">
                        <label for="price" class="form-label">Price (MAD)</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="price" placeholder="Enter price" required>
                    </div>
                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="Laptop Gamer">Laptop Gamer</option>
                            <option value="PC Gamer">PC Gamer</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control" name="short_description" id="short_description" rows="2" placeholder="Brief description for listings"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Full Description</label>
                        <textarea class="form-control" name="description" id="description" rows="4" placeholder="Detailed product description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        <small class="text-muted">Recommended size: 800x800px</small>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Product
                        </button>
                        <button type="reset" class="btn btn-outline">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>