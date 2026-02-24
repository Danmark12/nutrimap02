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
$reportId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($reportId <= 0) die("Invalid request");

// 🔹 Get report status
$statusStmt = $pdo->prepare("SELECT status FROM reports WHERE id = ?");
$statusStmt->execute([$reportId]);
$status = $statusStmt->fetchColumn();

if (!$status) {
    die("Report not found");
}

// 🔹 Mark this report as deleted for this specific user
$exists = $pdo->prepare("SELECT id FROM report_archives WHERE report_id=? AND user_id=? AND user_type=?");
$exists->execute([$reportId, $user_id, $user_type]);

if ($exists->fetch()) {
    $pdo->prepare("
        UPDATE report_archives 
        SET is_deleted=1, deleted_at=NOW() 
        WHERE report_id=? AND user_id=? AND user_type=?
    ")->execute([$reportId, $user_id, $user_type]);
} else {
    $pdo->prepare("
        INSERT INTO report_archives (report_id,user_id,user_type,is_archived,is_deleted,deleted_at)
        VALUES (?,?,?,?,?,NOW())
    ")->execute([$reportId, $user_id, $user_type, 1, 1]);
}

logActivity($pdo, $user_id, "Deleted report (ID: $reportId) from archive");

// 🔹 For Pending reports → only hide for BNS, never delete from DB
if (strtolower($status) === 'pending') {
    header("Location: ../archive.php?msg=deleted_pending");
    exit();
}

// 🔹 For Rejected reports → check if both deleted
if (strtolower($status) === 'rejected') {
    $check = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN user_type='BNS' AND is_deleted=1 THEN 1 ELSE 0 END) AS bns_deleted,
            SUM(CASE WHEN user_type='CNO' AND is_deleted=1 THEN 1 ELSE 0 END) AS cno_deleted
        FROM report_archives
        WHERE report_id = ?
    ");
    $check->execute([$reportId]);
    $res = $check->fetch(PDO::FETCH_ASSOC);

    // ✅ Delete from DB only if both sides deleted
    if ($res['bns_deleted'] > 0 && $res['cno_deleted'] > 0) {
        $pdo->prepare("DELETE FROM bns_reports WHERE report_id=?")->execute([$reportId]);
        $pdo->prepare("DELETE FROM reports WHERE id=?")->execute([$reportId]);
        $pdo->prepare("DELETE FROM report_archives WHERE report_id=?")->execute([$reportId]);
        logActivity($pdo, $user_id, "Permanently deleted REJECTED report (ID: $reportId) after both users deleted");
    }
}

header("Location: ../archive.php?msg=deleted");
exit();
?>
