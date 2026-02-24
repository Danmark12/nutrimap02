<?php
session_start();
require '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user_id'];
$success = false;

try {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Mark single notification as read
        $id = intval($_POST['id']);
        $stmt = $pdo->prepare("UPDATE notifications SET read_status = 1 WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $success = $stmt->rowCount() > 0;
    } else {
        // Mark all as read for the user
        $stmt = $pdo->prepare("UPDATE notifications SET read_status = 1 WHERE user_id = :user_id AND read_status = 0");
        $stmt->execute([':user_id' => $userId]);
        $success = true; // Always succeed if no error
    }
    
    echo json_encode(['status' => 'success', 'updated' => $success ? 'yes' : 'no']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>