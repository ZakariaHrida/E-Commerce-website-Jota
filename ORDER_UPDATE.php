<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

$order_id = $_GET['id'] ?? 0;
$order = $pdo->query("SELECT * FROM orders WHERE id = $order_id")->fetch();

if (!$order) {
    header('Location: ORDERS_LIST.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $tracking_number = $_POST['tracking_number'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid order status";
        header("Location: update.php?id=$order_id");
        exit;
    }
    
    // Update order
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, tracking_number = ?, admin_notes = ? WHERE id = ?");
    $stmt->execute([$status, $tracking_number, $notes, $order_id]);
    
    $_SESSION['message'] = "Order #$order_id updated successfully";
    header("Location: view.php?id=$order_id");
    exit;
}

// Get order items
$items = $pdo->query("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .order-update-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-info {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
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
        
        select.form-control {
            height: 40px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #ddd;
        }
        
        .item-row {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 4px;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-price {
            width: 100px;
            text-align: right;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-processing { background-color: #cce5ff; color: #004085; }
        .status-shipped { background-color: #d1ecf1; color: #0c5460; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="order-update-container">
                <h1>Update Order #<?= $order_id ?></h1>
                
                <?php if (!empty($_SESSION['message'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['message']) ?>
                        <?php unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="order-info">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <div>
                            <h3>Order Information</h3>
                            <p><strong>Date:</strong> <?= date('M j, Y H:i', strtotime($order['order_date'])) ?></p>
                            <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                        </div>
                        
                        <div>
                            <h3>Shipping Information</h3>
                            <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                            <?php if (!empty($order['tracking_number'])): ?>
                                <p><strong>Tracking #:</strong> <?= htmlspecialchars($order['tracking_number']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h3>Order Items</h3>
                    <?php foreach ($items as $item): ?>
                    <div class="item-row">
                        <img src="../../img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                        <div class="item-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p>Quantity: <?= $item['quantity'] ?></p>
                        </div>
                        <div class="item-price">
                            <?= number_format($item['price'], 2) ?> MAD
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div style="text-align: right; margin-top: 20px; font-size: 18px;">
                        <p><strong>Subtotal:</strong> <?= number_format($order['total'] - $order['shipping_cost'], 2) ?> MAD</p>
                        <p><strong>Shipping:</strong> <?= number_format($order['shipping_cost'], 2) ?> MAD</p>
                        <p><strong>Total:</strong> <?= number_format($order['total'], 2) ?> MAD</p>
                    </div>
                </div>
                
                <div class="order-info">
                    <h2>Update Order Status</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="tracking_number" class="form-label">Tracking Number</label>
                            <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Admin Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"><?= htmlspecialchars($order['admin_notes'] ?? '') ?></textarea>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" class="btn btn-primary">Update Order</button>
                            <a href="view.php?id=<?= $order_id ?>" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>