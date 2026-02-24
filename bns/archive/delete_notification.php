<?php
session_start();
require '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    exit('unauthorized');
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $notifId = intval($_POST['id']);

    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $notifId, ':user_id' => $userId]);

    echo $stmt->rowCount() > 0 ? 'success' : 'error';
} else {
    echo 'invalid_request';
}
?>
