<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

$category_id = $_GET['id'] ?? 0;
$category = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$category->execute([$category_id]);
$category = $category->fetch();

if (!$category) {
    $_SESSION['error'] = "Category not found";
    header('Location: CAT_LIST.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $slug = strtolower(str_replace(' ', '-', $name));
    $type = $_POST['type'] ?? 'product';

    if (!empty($name)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, slug = ?, type = ? WHERE id = ?");
        $stmt->execute([$name, $description, $slug, $type, $category_id]);
        $_SESSION['message'] = "Category updated successfully";
        header('Location: CAT_LIST.php');
        exit;
    } else {
        $_SESSION['error'] = "Category name is required";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Same styles as CAT_ADD.php */
        :root {
            --primary: #3498db;
            --danger: #e74c3c;
        }

        .admin-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .admin-main {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 20px auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .alert {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <h1>Edit Category</h1>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">Category Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="product" <?= $category['type'] === 'product' ? 'selected' : '' ?>>Product Category</option>
                            <option value="service" <?= $category['type'] === 'service' ? 'selected' : '' ?>>Service Category</option>
                            <option value="both" <?= $category['type'] === 'both' ? 'selected' : '' ?>>Both Product and Service</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Category</button>
                        <a href="CAT_LIST.php" class="btn">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
</body>

</html>