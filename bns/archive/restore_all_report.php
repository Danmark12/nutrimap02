<?php
session_start();
require '../../db/config.php';

// ✅ Require login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type']; // 'BNS' or 'CNO'

// ✅ Activity log function
function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

// ✅ Step 1: Check if user has archived reports
$check = $pdo->prepare("
    SELECT report_id 
    FROM report_archives
    WHERE user_id = ? 
      AND user_type = ? 
      AND is_archived = 1 
      AND is_deleted = 0
");
$check->execute([$user_id, $user_type]);
$reportIds = $check->fetchAll(PDO::FETCH_COLUMN);

if (count($reportIds) > 0) {

    // ✅ Step 2: Restore the user's archived reports
    $restore = $pdo->prepare("
        UPDATE report_archives 
        SET is_archived = 0, archived_at = NULL 
        WHERE user_id = ? 
          AND user_type = ? 
          AND is_archived = 1 
          AND is_deleted = 0
    ");
    $restore->execute([$user_id, $user_type]);

    // 🚫 Step 3: Do NOT touch the `reports` table!
    // The CNO’s data must remain unchanged.
    // BNS will automatically see the reports again because they're no longer archived.

    // ✅ Step 4: Log activity
    logActivity($pdo, $user_id, "Restored all archived reports");

    // ✅ Step 5: Redirect to reports page
    header("Location: ../archive.php?msg=restored_all");
    exit();

} else {
    header("Location: ../archive.php?msg=no_archive");
    exit();
}
?>
