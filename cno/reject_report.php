<?php
// reject_report.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $report_id = intval($_GET['id']);

    try {
        $pdo->prepare("UPDATE reports SET status = 'Rejected' WHERE id = ?")
            ->execute([$report_id]);

        $_SESSION['success'] = "Report has been rejected.";
        header("Location: cno_reports.php");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
