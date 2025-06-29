<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'] ?? '';
    $site_email = $_POST['site_email'] ?? '';
    $currency = $_POST['currency'] ?? 'MAD';
    $shipping_cost = $_POST['shipping_cost'] ?? 0;
    
 
    $_SESSION['message'] = "Settings updated successfully";
    header('Location: settings.php');
    exit;
}


$settings = [
    'site_name' => 'JOTA Store',
    'site_email' => 'contact@jota.com',
    'currency' => 'MAD',
    'shipping_cost' => 50.00
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="icon" href="img/icon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Store Settings</h1>
            
            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST">
                    <div class="form-group">
                        <label for="site_name" class="form-label">Store Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="site_email" class="form-label">Store Email</label>
                        <input type="email" class="form-control" id="site_email" name="site_email" value="<?= htmlspecialchars($settings['site_email']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-control" id="currency" name="currency" required>
                            <option value="MAD" <?= $settings['currency'] === 'MAD' ? 'selected' : '' ?>>MAD</option>
                            <option value="USD" <?= $settings['currency'] === 'USD' ? 'selected' : '' ?>>USD</option>
                            <option value="EUR" <?= $settings['currency'] === 'EUR' ? 'selected' : '' ?>>EUR</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_cost" class="form-label">Shipping Cost (<?= $settings['currency'] ?>)</label>
                        <input type="number" step="0.01" class="form-control" id="shipping_cost" name="shipping_cost" value="<?= htmlspecialchars($settings['shipping_cost']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>