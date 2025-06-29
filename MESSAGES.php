<?php
session_start();
include 'DB.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: LOGIN.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="icon" href="img/icon.png" />
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .unread {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .unread-indicator {
            background-color: #3498db;
        }
        
        .read-indicator {
            background-color: #95a5a6;
        }
        
        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        
        .view-btn {
            background-color: #3498db;
            color: white;
        }
        
        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <main class="admin-main">
            <h1>Customer Messages</h1>
            
            <?php if (!empty($messages)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message Preview</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                    <tr class="<?= $message['is_read'] ? '' : 'unread' ?>">
                        <td>
                            <span class="status-indicator <?= $message['is_read'] ? 'read-indicator' : 'unread-indicator' ?>"></span>
                            <?= $message['is_read'] ? 'Read' : 'Unread' ?>
                        </td>
                        <td><?= htmlspecialchars($message['name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= htmlspecialchars(substr($message['message'], 0, 50)) . (strlen($message['message']) > 50 ? '...' : '') ?></td>
                        <td><?= date('M j, Y H:i', strtotime($message['created_at'])) ?></td>
                        <td>
                            <button class="action-btn view-btn" onclick="viewMessage(<?= $message['id'] ?>)">View</button>
                            <button class="action-btn delete-btn" onclick="deleteMessage(<?= $message['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="stat-card" style="text-align: center; padding: 40px;">
                <h3>No messages yet</h3>
                <p>Customer messages will appear here when they contact you through the contact form.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function viewMessage(id) {
            window.location.href = 'view_message.php?id=' + id;
        }
        
        function deleteMessage(id) {
            if (confirm('Are you sure you want to delete this message?')) {
                window.location.href = 'delete_message.php?id=' + id;
            }
        }
    </script>
</body>
</html>