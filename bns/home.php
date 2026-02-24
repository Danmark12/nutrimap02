<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Total submitted reports
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r 
    JOIN bns_reports b ON r.id = b.report_id 
    WHERE r.user_id = ? 
      AND r.is_submitted = 1
");
$totalStmt->execute([$userId]);
$totalReports = $totalStmt->fetchColumn();

// Approved submitted reports
$approvedStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r 
    JOIN bns_reports b ON r.id = b.report_id 
    WHERE r.user_id = ? 
      AND r.status = 'Approved' 
      AND r.is_submitted = 1
      AND NOT EXISTS (
          SELECT 1 FROM report_archives a 
          WHERE a.report_id = r.id AND a.is_archived = 1
      )
");
$approvedStmt->execute([$userId]);
$approvedReports = $approvedStmt->fetchColumn();

// Pending submitted reports
$pendingStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r 
    JOIN bns_reports b ON r.id = b.report_id 
    WHERE r.user_id = ? 
      AND r.status = 'Pending' 
      AND r.is_submitted = 1
      AND NOT EXISTS (
          SELECT 1 FROM report_archives a 
          WHERE a.report_id = r.id AND a.is_archived = 1
      )
");
$pendingStmt->execute([$userId]);
$pendingReports = $pendingStmt->fetchColumn();

// Rejected submitted reports
$rejectedStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reports r 
    JOIN bns_reports b ON r.id = b.report_id 
    WHERE r.user_id = ? 
      AND r.status = 'Rejected' 
      AND r.is_submitted = 1
      AND NOT EXISTS (
          SELECT 1 FROM report_archives a 
          WHERE a.report_id = r.id AND a.is_archived = 1
      )
");
$rejectedStmt->execute([$userId]);
$rejectedReports = $rejectedStmt->fetchColumn();

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$totalRowsStmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ?");
$totalRowsStmt->execute([$userId]);
$totalRows = $totalRowsStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$stmt = $pdo->prepare("SELECT r.id, u.profile_pic, u.username, b.title, r.status, r.report_time, r.report_date FROM reports r JOIN users u ON r.user_id = u.id JOIN bns_reports b ON r.id = b.report_id LEFT JOIN report_archives a ON r.id = a.report_id AND (a.is_deleted = 0 OR a.is_deleted IS NULL) AND (a.is_archived = 0 OR a.is_archived IS NULL) WHERE r.user_id = :userId AND (r.status = 'Pending' OR r.status = 'Rejected' OR r.status = 'Approved') AND r.is_submitted = 1 ORDER BY r.report_date DESC, r.report_time DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$myReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>BNS | Dashboard</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen">
<div class="flex flex-col h-screen">
  <?php include 'header.php'; ?>

  <div class="flex flex-1 overflow-hidden">

    <main class="flex-1 flex flex-col p-4 overflow-hidden">
      <div class="flex justify-between items-center mb-4">
  <h2 class="text-2xl font-bold">Dashboard</h2>
  <input 
    id="tableSearch"
    type="text" 
    placeholder="Search Reports" 
    class="px-3 py-2 w-60 border border-gray-300 rounded focus:ring-1 focus:ring-teal-500 focus:outline-none">
</div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#003d3c] text-white">
          <i class="fa fa-file-alt text-4xl"></i>
          <h3 class="font-semibold text-2xl">Total Reports: <?= $totalReports ?></h3>
        </div>
        <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#006d6a] text-white">
          <i class="fa fa-check-circle text-4xl"></i>
          <h3 class="font-semibold text-2xl">Approved: <?= $approvedReports ?></h3>
        </div>
        <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-[#009688] text-white">
          <i class="fa fa-clock text-4xl"></i>
          <h3 class="font-semibold text-2xl">Pending: <?= $pendingReports ?></h3>
        </div>
        <div class="flex items-center h-28 gap-4 p-5 rounded-lg shadow bg-red-500 text-white">
          <i class="fa fa-times-circle text-4xl"></i>
          <h3 class="font-semibold text-2xl">Rejected: <?= $rejectedReports ?></h3>
        </div>
      </div>

      <div class="flex flex-col bg-white rounded-lg shadow overflow-hidden flex-1">
        <div class="flex justify-between items-center py-3 px-4 font-bold border-b text-gray-700">
          <span>Reports</span>
          <a href="reports.php" class="text-blue-700 text-sm hover:underline">View All</a>
        </div>
        <div class="flex-1 overflow-y-auto">
          <table id="reportsTable" class="w-full text-sm border-collapse">
            <thead class="bg-[#009688] text-white">
              <tr>
                <th class="p-2 text-left">User</th>
                <th class="p-2 text-left">Title</th>
                <th class="p-2 text-left">Status</th>
                <th class="p-2 text-left">Time</th>
                <th class="p-2 text-left">Date</th>
                <th class="p-2 text-left">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($myReports): ?>
              <?php foreach ($myReports as $r): ?>
              <tr class="border-b">
                <td class="p-2 flex items-center gap-2">
                  <?php if (!empty($r['profile_pic']) && file_exists("../uploads/".$r['profile_pic'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($r['profile_pic']) ?>" class="w-7 h-7 rounded-full object-cover">
                  <?php else: ?>
                    <img src="../uploads/default.png" class="w-7 h-7 rounded-full object-cover">
                  <?php endif; ?>
                  <?= htmlspecialchars($r['username']) ?>
                </td>
                <td class="p-2"><?= htmlspecialchars($r['title']) ?></td>
                <td class="p-2">
                  <span class="px-3 py-1 rounded-full text-white text-xs
                    <?php if($r['status']==='Pending') echo 'bg-cyan-500'; ?>
                    <?php if($r['status']==='Approved') echo 'bg-green-600'; ?>
                    <?php if($r['status']==='Rejected') echo 'bg-red-500'; ?>
                  ">
                  <?= $r['status'] ?></span>
                </td>
                <td class="p-2"><?= htmlspecialchars($r['report_time']) ?></td>
                <td class="p-2"><?= htmlspecialchars($r['report_date']) ?></td>
                <td class="p-2">
                  <a href="view_report.php?id=<?= $r['id'] ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-xs">View</a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-gray-500 py-4">No reports found</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="flex justify-center gap-2 py-3">
          <?php
            $maxLinks = 5;
            $start = max(1, $page - floor($maxLinks / 2));
            $end = min($totalPages, $start + $maxLinks - 1);
            if ($end - $start < $maxLinks - 1) $start = max(1, $end - $maxLinks + 1);
          ?>

          <a href="?page=<?= $page-1 ?>" class="px-3 py-1 border rounded text-sm <?= $page>1?'':'pointer-events-none opacity-50' ?>">Prev</a>

          <?php if ($start > 1): ?>
            <a href="?page=1" class="px-3 py-1 border rounded text-sm">1</a>
            <?php if ($start > 2): ?><span class="px-2 text-gray-500">...</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($i=$start;$i<=$end;$i++): ?>
            <a href="?page=<?= $i ?>" class="px-3 py-1 border rounded text-sm <?= $i==$page?'bg-[#009688] text-white':'' ?>"><?= $i ?></a>
          <?php endfor; ?>

          <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="px-2 text-gray-500">...</span><?php endif; ?>
            <a href="?page=<?= $totalPages ?>" class="px-3 py-1 border rounded text-sm"><?= $totalPages ?></a>
          <?php endif; ?>

          <a href="?page=<?= $page+1 ?>" class="px-3 py-1 border rounded text-sm <?= $page<$totalPages?'':'pointer-events-none opacity-50' ?>">Next</a>
        </div>

      </div>
    </main>
  </div>
</div>
<script>
document.getElementById('tableSearch').addEventListener('keyup', function () {
  const keyword = this.value.toLowerCase();
  const rows = document.querySelectorAll('#reportsTable tbody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(keyword) ? '' : 'none';
  });
});
</script>
</body>
</html>
