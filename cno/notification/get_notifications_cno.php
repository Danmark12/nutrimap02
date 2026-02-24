<?php
session_start();
require '../../db/config.php'; // adjust path if needed

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode(['notifications' => [], 'totalCount' => 0, 'totalUnread' => 0]);
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$size = isset($_GET['size']) ? (int)$_GET['size'] : 5;
$offset = ($page - 1) * $size;
$unreadOnly = isset($_GET['unread_only']);

// ✅ Get unread count for this user
$stmtUnread = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND `read_status` = 0");
$stmtUnread->execute([$userId]);
$totalUnread = (int)$stmtUnread->fetchColumn();

// ✅ Get total count for this user
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$stmtTotal->execute([$userId]);
$totalCount = (int)$stmtTotal->fetchColumn();

// ✅ Fetch notifications
$sql = "SELECT n.id, n.message, n.date, n.read_status, u.barangay
        FROM notifications n
        JOIN users u ON n.user_id = u.id
        WHERE n.user_id = :user_id";

if ($unreadOnly) {
    $sql .= " AND n.read_status = 0";
}

$sql .= " ORDER BY n.id DESC LIMIT :size OFFSET :offset";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':size', $size, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Output JSON
echo json_encode([
    'notifications' => $notifications,
    'totalCount' => $totalCount,
    'totalUnread' => $totalUnread
]);
