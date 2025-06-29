<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

// Default to current month
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

// Get sales data
$stmt = $pdo->prepare("SELECT 
    DATE(order_date) as date, 
    COUNT(*) as orders, 
    SUM(total_amount) as revenue 
    FROM orders 
    WHERE order_date BETWEEN ? AND ? 
    GROUP BY DATE(order_date) 
    ORDER BY date");
$stmt->execute([$start_date, $end_date]);
$daily_sales = $stmt->fetchAll();


$top_products = $pdo->query("
    SELECT p.name, SUM(COALESCE(oi.quantity, 0)) as total_quantity, SUM(COALESCE(oi.unit_price, 0) * COALESCE(oi.quantity, 0)) as total_revenue 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    GROUP BY p.id 
    ORDER BY total_revenue DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Reports</title>
    <link rel="icon" href="img/icon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Sales Reports</h1>
            
            <div class="stat-card" style="margin-bottom: 20px;">
                <form method="GET">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div>
                            <label>Start Date</label>
                            <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control">
                        </div>
                        <div>
                            <label>End Date</label>
                            <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="value"><?= array_sum(array_column($daily_sales, 'orders')) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value"><?= number_format(array_sum(array_column($daily_sales, 'revenue')) ?: 0, 2) ?> MAD</div>
                </div>
                <div class="stat-card">
                    <h3>Average Order Value</h3>
                    <div class="value">
                        <?php 
                        $total_orders = array_sum(array_column($daily_sales, 'orders'));
                        $total_revenue = array_sum(array_column($daily_sales, 'revenue'));
                        echo $total_orders > 0 ? number_format($total_revenue / $total_orders, 2) : '0.00';
                        ?> MAD
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 20px;">
                <div class="stat-card">
                    <h3>Daily Sales</h3>
                    <canvas id="salesChart" height="300"></canvas>
                </div>
                
                <div class="stat-card">
                    <h3>Top Products</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['total_quantity'] ?></td>
                                <td><?= number_format($product['total_revenue'], 2) ?> MAD</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <script>
                const ctx = document.getElementById('salesChart').getContext('2d');
                const salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($daily_sales, 'date')) ?>,
                        datasets: [{
                            label: 'Revenue (MAD)',
                            data: <?= json_encode(array_column($daily_sales, 'revenue')) ?>,
                            backgroundColor: 'rgba(52, 152, 219, 0.7)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </main>
    </div>
</body>
</html>