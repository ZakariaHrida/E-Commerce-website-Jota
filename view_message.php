<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: MESSAGES.php');
    exit;
}

$id = $_GET['id'];

// Mark message as read
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$id]);

// Get the message
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    header('Location: MESSAGES.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }
        
        .admin-main {
            flex: 1;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .message-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .message-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .message-body {
            line-height: 1.6;
        }
        
        .back-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Message Details</h1>
            
            <div class="message-container">
                <div class="message-header">
                    <h2><?= htmlspecialchars($message['subject']) ?></h2>
                    <p><strong>From:</strong> <?= htmlspecialchars($message['name']) ?> &lt;<?= htmlspecialchars($message['email']) ?>&gt;</p>
                    <p><strong>Date:</strong> <?= date('M j, Y H:i', strtotime($message['created_at'])) ?></p>
                </div>
                
                <div class="message-body">
                    <?= nl2br(htmlspecialchars($message['message'])) ?>
                </div>
                
                <a href="MESSAGES.php" class="back-btn">Back to Messages</a>
            </div>
        </main>
    </div>
</body>
</html>