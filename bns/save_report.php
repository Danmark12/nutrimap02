<?php
session_start();
require '../../db/config.php';
require_once '../../otp/mailer.php'; // ✅ include mailer

// ✅ Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = (int)$_POST['report_id'];

    // 🔹 Fetch the existing report
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = :id");
    $stmt->execute(['id' => $reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$report) {
        die("Report not found.");
    }

    // 🔹 Only allow editing if Pending or Rejected
    if (!in_array($report['status'], ['Pending', 'Rejected'])) {
        die("This report cannot be edited.");
    }

    // ✅ Collect all BNS inputs (UPDATED LIST)
    $fields = [
        'title' => $_POST['title'] ?? null,
        'barangay' => $_POST['barangay'] ?? null,
        'year' => $_POST['year'] ?? null,

        'ind1' => $_POST['ind1'] ?? null,
        'ind_male' => $_POST['ind_male'] ?? null,
        'ind_female' => $_POST['ind_female'] ?? null,
        'ind2' => $_POST['ind2'] ?? null,
        'ind3' => $_POST['ind3'] ?? null,
        'ind4' => $_POST['ind4'] ?? null,
        'ind5' => $_POST['ind5'] ?? null,

        'ind6a' => $_POST['ind6a'] ?? null,
        'ind6b' => $_POST['ind6b'] ?? null,
        'ind7' => $_POST['ind7'] ?? null,
        'ind8' => $_POST['ind8'] ?? null,
        'ind9' => $_POST['ind9'] ?? null,
        'ind9a' => $_POST['ind9a'] ?? null,
    ];

    // ✅ ind9b 1-9 NO + PCT
    for ($i = 1; $i <= 9; $i++) {
        $fields["ind9b{$i}_no"]  = $_POST["ind9b{$i}_no"]  ?? null;
        $fields["ind9b{$i}_pct"] = $_POST["ind9b{$i}_pct"] ?? null;
    }

    // ✅ Continue collecting fields
    $extraFields = [
        'ind10','ind11','ind12','ind13','ind14','ind15','ind16',
        'ind18','ind19','ind20','ind21',
        'ind23','ind24','ind25','ind26',
        'ind38',
        'ind37a','ind37b'
    ];

    foreach ($extraFields as $f) {
        $fields[$f] = $_POST[$f] ?? null;
    }

    // ✅ ind17a,b (public/private)
    foreach (['a','b'] as $letter) {
        $fields["ind17{$letter}_public"] = $_POST["ind17{$letter}_public"] ?? null;
        $fields["ind17{$letter}_private"] = $_POST["ind17{$letter}_private"] ?? null;
    }

    // ✅ ind22 a-g
    foreach (['a','b','c','d','e','f','g'] as $letter) {
        $fields["ind22{$letter}_no"] = $_POST["ind22{$letter}_no"] ?? null;
        $fields["ind22{$letter}_pct"] = $_POST["ind22{$letter}_pct"] ?? null;
    }

    // ✅ ind27 a-e
    foreach (['a','b','c','d','e'] as $letter) {
        $fields["ind27{$letter}_no"] = $_POST["ind27{$letter}_no"] ?? null;
        $fields["ind27{$letter}_pct"] = $_POST["ind27{$letter}_pct"] ?? null;
    }

    // ✅ ind28 a-d
    foreach (['a','b','c','d'] as $letter) {
        $fields["ind28{$letter}_no"] = $_POST["ind28{$letter}_no"] ?? null;
        $fields["ind28{$letter}_pct"] = $_POST["ind28{$letter}_pct"] ?? null;
    }

    // ✅ ind29 a-g
    foreach (['a','b','c','d','e','f','g'] as $letter) {
        $fields["ind29{$letter}_no"] = $_POST["ind29{$letter}_no"] ?? null;
        $fields["ind29{$letter}_pct"] = $_POST["ind29{$letter}_pct"] ?? null;
    }

    // ✅ ind30 a-d
    foreach (['a','b','c','d'] as $letter) {
        $fields["ind30{$letter}_no"] = $_POST["ind30{$letter}_no"] ?? null;
        $fields["ind30{$letter}_pct"] = $_POST["ind30{$letter}_pct"] ?? null;
    }

    // ✅ ind31 a-f
    foreach (['a','b','c','d','e','f'] as $letter) {
        $fields["ind31{$letter}_no"] = $_POST["ind31{$letter}_no"] ?? null;
        $fields["ind31{$letter}_pct"] = $_POST["ind31{$letter}_pct"] ?? null;
    }

    // ✅ NEW – ind32 to ind36 (NO percentage)
foreach (['32','33','34','35','36'] as $num) {
    $fields["ind{$num}"] = $_POST["ind{$num}"] ?? 0;
}

    // ✅ Update bns_reports
    $updateFields = [];
    foreach ($fields as $k => $v) {
        $updateFields[] = "$k = :$k";
    }
    $updateFieldsStr = implode(", ", $updateFields);

    $sql = "UPDATE bns_reports SET $updateFieldsStr WHERE report_id = :report_id";
    $stmt = $pdo->prepare($sql);
    $fields['report_id'] = $reportId;
    $stmt->execute($fields);

    // ✅ Update status (notify CNO)
    $stmt = $pdo->prepare("UPDATE reports SET status = 'Pending', prev_status = NULL WHERE id = :id");
    $stmt->execute(['id' => $reportId]);

    // ✅ Log activity
    $logStmt = $pdo->prepare("
        INSERT INTO activity_logs (user_id, action, details, created_at)
        VALUES (:user_id, :action, :details, NOW())
    ");
    $logStmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':action'  => 'Report Updated / Saved Changes',
        ':details' => "Report ID {$reportId} was edited and reset to Pending"
    ]);

    // ✅ Email CNO
    $stmt = $pdo->prepare("
        SELECT u.email, u.first_name, u.last_name
        FROM users u
        WHERE u.user_type = 'CNO'
        LIMIT 1
    ");
    $stmt->execute();
    $cnoUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cnoUser && !empty($cnoUser['email'])) {
        $to = $cnoUser['email'];
        $subject = "Report Updated - Pending Review";
        $reportTitle = htmlspecialchars($fields['title']);

        $userStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :id LIMIT 1");
        $userStmt->execute(['id' => $_SESSION['user_id']]);
        $sender = $userStmt->fetch(PDO::FETCH_ASSOC);
        $senderName = $sender ? htmlspecialchars($sender['first_name'] . ' ' . $sender['last_name']) : 'Unknown';

        $message = "
            Hello,<br><br>
            A report titled <strong>$reportTitle</strong> has been updated by <strong>$senderName</strong>
            and is now pending your review.<br><br>
            <strong>Date:</strong> " . date('Y-m-d') . "<br><br>
            Please review it in the system.
        ";

        sendEmailNotification($to, $subject, $message);
    }

    // ✅ Redirect
    header("Location: ../reports.php?id=$reportId&msg=updated");
    exit();
}
?>
