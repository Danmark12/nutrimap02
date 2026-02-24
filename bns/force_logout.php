<?php
session_start();
require '../db/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "Invalid request";
    exit();
}

$user_id = $_SESSION['user_id'];
$login_id = (int) $_GET['id'];

// Fetch the login record for that session
$stmt = $pdo->prepare("SELECT session_id, device_token, ip_address FROM login_history WHERE id = ? AND user_id = ?");
$stmt->execute([$login_id, $user_id]);
$login = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$login) {
    $_SESSION['flash_message'] = "Device/session not found.";
    header("Location: security.php");
    exit();
}

// Delete only the selected session
$deleteStmt = $pdo->prepare("DELETE FROM login_history WHERE id = ? AND user_id = ?");
$deleteStmt->execute([$login_id, $user_id]);

// Log the forced logout action
$log = $pdo->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
$log->execute([$user_id, "Force logged out session with ID: $login_id and device token: {$login['device_token']}"]);

// Redirect back
$_SESSION['flash_message'] = "Selected session has been force logged out. Device token removed, OTP will be required next login.";
header("Location: security.php");
exit();
?>
