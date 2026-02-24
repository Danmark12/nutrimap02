<?php
session_start();
require '../../db/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['totalCount' => 0, 'totalUnread' => 0, 'notifications' => []]);
    exit;
}

$userId = $_SESSION['user_id'];

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = isset($_GET['size']) ? intval($_GET['size']) : 5;
$offset = ($page - 1) * $size;

// Optional: unread only
$unreadOnly = isset($_GET['unread_only']) && $_GET['unread_only'] == 1;

// Total unread notifications (including old ones without sender_id)
$stmtUnread = $pdo->prepare("
    SELECT COUNT(*) 
    FROM notifications n
    LEFT JOIN users u ON n.sender_id = u.id
    WHERE n.user_id = :user_id AND n.read_status = 0
      AND (u.user_type = 'CNO' OR n.sender_id IS NULL)
");
$stmtUnread->execute([':user_id' => $userId]);
$totalUnread = $stmtUnread->fetchColumn();

// Total count
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) 
    FROM notifications n
    LEFT JOIN users u ON n.sender_id = u.id
    WHERE n.user_id = :user_id
      AND (u.user_type = 'CNO' OR n.sender_id IS NULL)
");
$stmtCount->execute([':user_id' => $userId]);
$totalCount = $stmtCount->fetchColumn();

// Fetch notifications with pagination
$whereClause = "WHERE n.user_id = :user_id AND (u.user_type = 'CNO' OR n.sender_id IS NULL)";
if ($unreadOnly) {
    $whereClause .= " AND n.read_status = 0";
    $totalCount = $totalUnread;
}

$stmt = $pdo->prepare("
    SELECT n.id, n.message, DATE_FORMAT(n.date, '%Y-%m-%d %H:%i:%s') AS date, n.read_status
    FROM notifications n
    LEFT JOIN users u ON n.sender_id = u.id
    $whereClause
    ORDER BY n.date DESC
    LIMIT :size OFFSET :offset
");

$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':size', $size, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$notifications = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $notifications[] = [
        'id' => $row['id'],
        'message' => $row['message'],
        'date' => $row['date'],
        'read' => (bool)$row['read_status']
    ];
}

echo json_encode([
    'totalCount' => $totalCount,
    'totalUnread' => $totalUnread,
    'notifications' => $notifications
]);
?>
