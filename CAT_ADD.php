<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $slug = strtolower(str_replace(' ', '-', $name));
    $type = $_POST['type'] ?? 'product'; // New field

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, description, slug, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $slug, $type]);
        $_SESSION['message'] = "Category added successfully";
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
    <title>JOTA - Add Category</title>
    <link rel="icon" href="img/icon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #ff6347;
            --primary-dark: #e04b32;
            --dark: #1C1C1C;
            --light: #f8f9fa;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: var(--dark);
        }

        .admin-header {
            background-color: var(--dark);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .admin-header .brand {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .admin-header .brand span {
            color: var(--primary);
        }

        .admin-header .user-info {
            margin-left: auto;
            display: flex;
            align-items: center;
        }

        .admin-header .user-info img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .admin-header .logout {
            color: white;
            margin-left: 15px;
            font-size: 1.1rem;
        }

        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .admin-sidebar {
            width: 250px;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
        }

        .admin-sidebar .menu-item {
            padding: 0.8rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .admin-sidebar .menu-item i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
        }

        .admin-sidebar .menu-item:hover,
        .admin-sidebar .menu-item.active {
            background-color: rgba(255, 99, 71, 0.1);
            color: var(--primary);
            border-left: 3px solid var(--primary);
        }

        .admin-main {
            flex: 1;
            padding: 2rem;
            background-color: #f9f9f9;
        }

        .dashboard-title {
            margin-bottom: 1.5rem;
            color: var(--dark);
            font-size: 1.8rem;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-title {
            margin-bottom: 1rem;
            color: var(--dark);
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 99, 71, 0.2);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn i {
            font-size: 0.9rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--gray);
            color: var(--dark);
        }

        .btn-outline:hover {
            background-color: var(--gray-light);
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mb-3 {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) {
            .admin-container {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .admin-main {
                padding: 1rem;
            }

            .form-container {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <header class="admin-header">
        <div class="brand">JOTA <span>Admin</span></div>
        <div class="user-info">
            <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])): ?>
                <img src="<?= htmlspecialchars($_SESSION['photo']) ?>" alt="User Photo">
            <?php endif; ?>
            <?= htmlspecialchars($_SESSION['username']) ?>
            <a href="LOGOUT.php" class="logout" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <div class="admin-container">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <div class="flex-between mb-3">
                <h1 class="dashboard-title">Add New Category</h1>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="form-container">
                <h2 class="form-title">Category Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">Category Type</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="product">Product Category</option>
                            <option value="service">Service Category</option>
                            <option value="both">Both Product and Service</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Category
                        </button>
                        <a href="CAT_LIST.php" class="btn btn-outline">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
</body>

</html>