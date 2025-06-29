<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: LOGIN.php');
    exit;
}

function handleFileUpload($fieldName, $uploadDir = 'img/')
{
    if (isset($_FILES[$fieldName])) {
        $file = $_FILES[$fieldName];
        if ($file['error'] === UPLOAD_ERR_OK) {
          
            $filename = uniqid() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $filename;

            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

           
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $filename;
            }
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['delete_user'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_POST['delete_user']]);
        $_SESSION['message'] = "User deleted successfully";
    }
   
    elseif (isset($_POST['reset_password'])) {
        $user_id = $_POST['reset_password'];
        $temp_password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $_SESSION['message'] = "Password reset. Temporary password: $temp_password";
    }
   
    elseif (isset($_POST['username']) && isset($_POST['role'])) {
        $id = $_POST['id'] ?? null;
        $username = $_POST['username'];
        $role = $_POST['role'];
        $password = $_POST['password'] ?? null;

   
        $photo = handleFileUpload('photo');
        $photoPath = $photo ? 'img/' . $photo : null;

        if ($id) {
        
            if ($password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                if ($photoPath) {
                
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, password = ?, photo = ? WHERE id = ?");
                    $stmt->execute([$username, $role, $hashed_password, $photoPath, $id]);
                } else {
               
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?");
                    $stmt->execute([$username, $role, $hashed_password, $id]);
                }
            } else {
                if ($photoPath) {
                
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, photo = ? WHERE id = ?");
                    $stmt->execute([$username, $role, $photoPath, $id]);
                } else {
                 
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
                    $stmt->execute([$username, $role, $id]);
                }
            }
            $_SESSION['message'] = "User updated successfully";
        } else {
      
            $temp_password = $password ?: bin2hex(random_bytes(4));
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

            if ($photoPath) {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, photo) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $role, $photoPath]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $role]);
            }
            $_SESSION['message'] = "User created. Password: $temp_password";
        }
    }

    elseif (isset($_POST['delete_product'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['delete_product']]);
        $_SESSION['message'] = "Product deleted successfully";
    }

    elseif (isset($_POST['product_name'])) {
        $product_id = $_POST['product_id'] ?? null;
        $name = $_POST['product_name'];
        $price = $_POST['product_price'];
        $category = $_POST['product_category'];
        $description = $_POST['product_description'] ?? '';
        $short_description = $_POST['product_short_description'] ?? '';

        $image = handleFileUpload('product_image');

        if ($product_id) {
           
            if ($image) {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ?, short_description = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $price, $category, $description, $short_description, $image, $product_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ?, short_description = ? WHERE id = ?");
                $stmt->execute([$name, $price, $category, $description, $short_description, $product_id]);
            }
            $_SESSION['message'] = "Product updated successfully";
        } else {
        
            $stmt = $pdo->prepare("INSERT INTO products (name, price, category, description, short_description, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $category, $description, $short_description, $image]);
            $_SESSION['message'] = "Product added successfully";
        }
    }
 
    elseif (isset($_POST['update_order_status'])) {
        $order_id = $_POST['order_id'];
        $status = $_POST['order_status'];

        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        $_SESSION['message'] = "Order status updated successfully";
    }
}

$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_sales = $pdo->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

$users = $pdo->query("SELECT * FROM users")->fetchAll();
$products = $pdo->query("SELECT * FROM products")->fetchAll();
$recent_orders = $pdo->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY order_date DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOTA Admin Dashboard</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: var(--gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .admin-tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 1.5rem;
        }

        .admin-tab {
            padding: 0.8rem 1.5rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .admin-tab:hover {
            color: var(--primary);
        }

        .admin-tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }

        .data-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-collapse: collapse;
        }

        .data-table th {
            background-color: var(--dark);
            color: white;
            padding: 1rem;
            text-align: left;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-light);
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: rgba(255, 99, 71, 0.05);
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

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: var(--warning);
            color: var(--dark);
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
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

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.7rem center;
            background-size: 1rem;
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

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mb-3 {
            margin-bottom: 1.5rem;
        }

        .user-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        @media (max-width: 992px) {
            .admin-container {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .data-table {
                display: block;
                overflow-x: auto;
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
            <div id="users" class="tab-content">
                <div class="flex-between mb-3">
                    <h1 class="dashboard-title">User Management</h1>
                </div>

                <div class="form-container">
                    <h2 class="form-title" id="form-title">Add New User</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label for="edit-username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="edit-username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role" class="form-label">Role</label>
                            <select class="form-control" name="role" id="edit-role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="edit-password" placeholder="Leave blank to generate random password">
                            <small class="text-muted">Passwords must be at least 8 characters</small>
                        </div>
                        <div class="form-group">
                            <label for="edit-photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" name="photo" id="edit-photo" accept="image/*">
                            <small class="text-muted">Recommended size: 300x300px</small>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary">
                                 Save User
                            </button>
                            <button type="button" class="btn btn-outline" onclick="resetUserForm()">
                                 Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <div class="stat-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <?php if (!empty($user['photo'])): ?>
                                            <img src="<?= htmlspecialchars($user['photo']) ?>" alt="User Photo" class="user-photo">
                                        <?php else: ?>
                                            <div style="width:40px;height:40px;border-radius:50%;background:#ddd;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td>
                                        <span style="padding: 0.3rem 0.6rem; border-radius: 20px; background-color: <?= $user['role'] === 'admin' ? '#d4edda' : '#e2e3e5' ?>; color: <?= $user['role'] === 'admin' ? '#155724' : '#383d41' ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <button class="btn btn-primary btn-sm" onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= $user['role'] ?>')">
                                             Edit
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="reset_password" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Reset password for <?= htmlspecialchars($user['username']) ?>?')">
                                                 Reset
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="delete_user" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete <?= htmlspecialchars($user['username']) ?>?')">
                                                 Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>



    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });

            document.getElementById(tabId).classList.add('active');

            document.querySelector(`.menu-item[href="#${tabId}"]`).classList.add('active');


            if (tabId !== 'users') resetUserForm();
            if (tabId !== 'products') resetProductForm();
        }

        function editUser(id, username, role) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-username').value = username;
            document.getElementById('edit-role').value = role;
            document.getElementById('edit-password').value = '';
            document.getElementById('form-title').textContent = 'Edit User';


            document.querySelector('.form-container').scrollIntoView({
                behavior: 'smooth'
            });
        }

        function resetUserForm() {
            document.getElementById('edit-id').value = '';
            document.getElementById('edit-username').value = '';
            document.getElementById('edit-role').value = 'user';
            document.getElementById('edit-password').value = '';
            document.getElementById('edit-photo').value = '';
            document.getElementById('form-title').textContent = 'Add New User';
        }

        function editProduct(id, name, price, category, shortDesc, description) {
            document.getElementById('edit-product-id').value = id;
            document.getElementById('edit-product-name').value = name;
            document.getElementById('edit-product-price').value = price;
            document.getElementById('edit-product-category').value = category;
            document.getElementById('edit-product-short-description').value = shortDesc;
            document.getElementById('edit-product-description').value = description;
            document.getElementById('product-form-title').textContent = 'Edit Product';


            document.querySelector('.form-container').scrollIntoView({
                behavior: 'smooth'
            });
        }
        function resetProductForm() {
            document.getElementById('edit-product-id').value = '';
            document.getElementById('edit-product-name').value = '';
            document.getElementById('edit-product-price').value = '';
            document.getElementById('edit-product-category').value = 'Laptop Gamer';
            document.getElementById('edit-product-short-description').value = '';
            document.getElementById('edit-product-description').value = '';
            document.getElementById('edit-product-image').value = '';
            document.getElementById('product-form-title').textContent = 'Add New Product';
        }

        window.addEventListener('load', function() {
            if (window.location.hash) {
                const tabId = window.location.hash.substring(1);
                showTab(tabId);
            }
        });
    </script>
</body>

</html>