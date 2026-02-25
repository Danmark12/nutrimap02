<?php
session_start();
require '../db/config.php';
require_once '../otp/mailer.php'; // ✅ include mailer

// ✅ Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$reportId = $_POST['report_id'] ?? 0;
if (!is_numeric($reportId) || $reportId <= 0) {
    die("Invalid report ID");
}

$userId = $_SESSION['user_id'];

// ✅ Activity log function
function logActivity($pdo, $user_id, $action, $details = null) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $details]);
}

try {
    $pdo->beginTransaction();

    // 1️⃣ Fetch old report
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = :id");
    $stmt->execute(['id' => $reportId]);
    $oldReport = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$oldReport) throw new Exception("Report not found");

    // 1️⃣ Fetch old BNS
    $stmt = $pdo->prepare("SELECT * FROM bns_reports WHERE report_id = :report_id");
    $stmt->execute(['report_id' => $reportId]);
    $oldBns = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$oldBns) throw new Exception("BNS data not found");

    // 2️⃣ Create new pending report
    $stmt = $pdo->prepare("
        INSERT INTO reports (user_id, report_time, report_date, status)
        VALUES (:user_id, :report_time, :report_date, 'Pending')
    ");
    $stmt->execute([
        'user_id' => $userId,
        'report_time' => date('H:i:s'),
        'report_date' => date('Y-m-d')
    ]);
    $newReportId = $pdo->lastInsertId();

    // ✅ Clean clone of old data (remove auto-increment + foreign mismatch)
    $bnsFields = $oldBns;
    unset($bnsFields['id']); // remove PK

    // ✅ Apply updated fields from form POST
    foreach ($_POST as $key => $value) {
        if (array_key_exists($key, $bnsFields) && $key !== 'id') {
            $bnsFields[$key] = $value;
        }
    }

    // ✅ Force correct links
    $bnsFields['report_id'] = $newReportId;
    $bnsFields['title'] = $_POST['title'] ?? ($oldBns['title'] ?? 'No Title');

    // ✅ Rebuild SQL safely
    $columns = array_keys($bnsFields);
    $placeholders = array_map(fn($c) => ':' . $c, $columns);
    $sql = "INSERT INTO bns_reports (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    
    $params = [];
    foreach ($bnsFields as $col => $val) {
        $params[":" . $col] = $val;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // ✅ Reset status to ensure notification triggers
    $stmt = $pdo->prepare("UPDATE reports SET prev_status = NULL WHERE id = :id");
    $stmt->execute(['id' => $newReportId]);

    $pdo->commit();

    // ✅ Log activity after success
    logActivity(
        $pdo,
        $userId,
        "Updated report (cloned as Pending)",
        "Old Report ID: $reportId → New Report ID: $newReportId"
    );

    // ✅ Email Notification to CNO
    $stmt = $pdo->prepare("
        SELECT email, first_name, last_name
        FROM users
        WHERE user_type = 'CNO'
        LIMIT 1
    ");
    $stmt->execute();
    $cnoUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cnoUser && !empty($cnoUser['email'])) {
        $to = $cnoUser['email'];
        $subject = "Report Updated - Pending Review";

        $reportTitle = htmlspecialchars($bnsFields['title']);
        $senderName = htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);

        $message = "
            Hello,<br><br>
            A report titled <strong>$reportTitle</strong> has been updated by <strong>$senderName</strong> 
            and is now pending your review.<br><br>
            <strong>Date:</strong> " . date('Y-m-d') . "<br><br>
            Please review it in the system.
        ";

        sendEmailNotification($to, $subject, $message);
    }

    header("Location: ../reports.php?id=$newReportId&msg=Report updated as Pending");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: " . $e->getMessage());
}
