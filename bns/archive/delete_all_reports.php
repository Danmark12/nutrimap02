<?php
session_start();
require '../../db/config.php';

function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// 🔹 Mark all archived reports as deleted for this user
$pdo->prepare("
    UPDATE report_archives 
    SET is_deleted=1, deleted_at=NOW() 
    WHERE user_id=? AND user_type=? AND is_archived=1
")->execute([$user_id, $user_type]);

// 🔹 Only permanently delete REJECTED reports if both sides deleted
$both = $pdo->query("
    SELECT ra.report_id
    FROM report_archives ra
    JOIN reports r ON r.id = ra.report_id
    WHERE r.status = 'Rejected'
    GROUP BY ra.report_id
    HAVING SUM(CASE WHEN ra.user_type='BNS' AND ra.is_deleted=1 THEN 1 ELSE 0 END)>0
       AND SUM(CASE WHEN ra.user_type='CNO' AND ra.is_deleted=1 THEN 1 ELSE 0 END)>0
")->fetchAll(PDO::FETCH_COLUMN);

if ($both) {
    $in = str_repeat('?,', count($both)-1) . '?';
    $pdo->prepare("DELETE FROM bns_reports WHERE report_id IN ($in)")->execute($both);
    $pdo->prepare("DELETE FROM reports WHERE id IN ($in)")->execute($both);
    $pdo->prepare("DELETE FROM report_archives WHERE report_id IN ($in)")->execute($both);
    logActivity($pdo, $user_id, "Permanently deleted REJECTED reports that both users removed");
} else {
    logActivity($pdo, $user_id, "Deleted all archived reports (BNS side only, waiting for CNO)");
}

header("Location: ../archive.php?msg=deleted_all");
exit();
?>
