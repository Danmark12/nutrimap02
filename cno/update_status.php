<?php
session_start();
require '../db/config.php';

// Only allow CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$currentUserId = $_SESSION['user_id']; // CNO performing the action

// Check inputs
if (!isset($_POST['report_id']) || !isset($_POST['action'])) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$report_id = $_POST['report_id'];
$action = strtolower(trim($_POST['action']));

if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['error' => 'Invalid action']);
    exit;
}

// Set status
$status = ($action === 'approve') ? 'Approved' : 'Rejected';

// Update report status
$stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
if ($stmt->execute([$status, $report_id])) {

    // Log activity
    $logStmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $logAction = ($action === 'approve') 
        ? "Approved report ID $report_id"
        : "Rejected report ID $report_id";
    $logStmt->execute([$currentUserId, $logAction]);

    // Fetch report owner
    $userStmt = $pdo->prepare("SELECT user_id FROM reports WHERE id = ?");
    $userStmt->execute([$report_id]);
    $report = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($report && isset($report['user_id'])) {
        $user_id = $report['user_id'];

        // Set notification message
        $message = ($action === 'approve') 
            ? "Your report has been approved!" 
            : ($_POST['message'] ?? "Your report has been rejected.");

        // Insert notification
        $notifyStmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $notifyStmt->execute([$user_id, $message]);
    }

    echo json_encode(['status' => $status, 'message' => $message]);

} else {
    echo json_encode(['error' => 'Failed to update report']);
}
?>