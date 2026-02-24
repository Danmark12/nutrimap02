<?php
// barangay_data.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user info
$stmtUser = $pdo->prepare("SELECT barangay, user_type FROM users WHERE id = ?");
$stmtUser->execute([$userId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
$barangay = $user['barangay'];

// Sorting
$sort = $_GET['sort'] ?? 'new';
$orderSQL = ($sort === 'az') ? " ORDER BY b.title ASC " : " ORDER BY r.report_date DESC, r.report_time DESC ";

// Fetch available years
$stmtYears = $pdo->prepare("SELECT DISTINCT year FROM bns_reports WHERE barangay = ? ORDER BY year DESC");
$stmtYears->execute([$barangay]);
$availableYears = $stmtYears->fetchAll(PDO::FETCH_COLUMN);
$selectedYear = $_GET['year'] ?? ($availableYears[0] ?? null);

// Fetch reports for selected year
$reportsByQuarter = [
    'Q1' => [], 'Q2' => [], 'Q3' => [], 'Q4' => []
];

if ($selectedYear) {
    $stmt = $pdo->prepare("
        SELECT r.id, r.report_date, b.title AS report_title, QUARTER(r.report_date) AS quarter
        FROM bns_reports b
        JOIN reports r ON r.id = b.report_id
        LEFT JOIN report_archives ra ON r.id = ra.report_id AND ra.is_archived = 1
        WHERE r.status='Approved' AND r.user_id=? AND b.year=? AND ra.id IS NULL
        $orderSQL
    ");
    $stmt->execute([$userId, $selectedYear]);
    $allReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allReports as $rep) {
        $q = 'Q' . $rep['quarter'];
        $reportsByQuarter[$q][] = $rep;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>BNS | Barangay Reports</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { margin:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; }
.layout { display:flex; height:100vh; flex-direction:column; }
.body-layout { flex:1; display:flex; }
.content { flex:1; padding:15px; display:flex; flex-direction:column; }
.toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:15px; }
.toolbar-left input { padding:6px 8px; border:1px solid #ccc; border-radius:4px; width:220px; }
.toolbar-right { display:flex; align-items:center; gap:10px; }
.toolbar-right label { font-size:14px; color:#333; margin-right:4px; }
.toolbar-right select { padding:6px; border:1px solid #ccc; border-radius:4px; }
.add-btn { background:#009688; color:#fff; text-decoration:none; padding:8px 14px; border-radius:4px; font-size:14px; display:flex; align-items:center; gap:6px; }
.add-btn:hover { background:#00796b; }
h3.section-title { margin:0 0 10px 0; font-size:18px; }
.file-list { display:flex; flex-direction:column; gap:10px; }
.file-card { background:#fff; border:1px solid #ccc; border-radius:6px; padding:15px; display:flex; flex-direction:column; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
.file-title { font-size:16px; color:#333; font-weight:600; cursor:pointer; }
.file-meta { font-size:13px; color:#555; margin-bottom:5px; }
.file-actions { display:flex; align-items:center; gap:10px; font-size:14px; }
.file-link { color:#007bff; text-decoration:none; font-weight:500; }
.file-link:hover { text-decoration:underline; }
.quarter-reports { margin-left:15px; margin-top:5px; }
</style>
</head>
<body>
<div class="layout">
<?php include 'header.php'; ?>
<div class="body-layout">
<main class="content">
<div class="toolbar">
  <div class="toolbar-left">
    <h3 class="section-title">Barangay Data Year: <?= $selectedYear ?></h3>
  </div>
  <div class="toolbar-right">
    <label for="yearSelect">Year:</label>
    <select id="yearSelect">
      <?php foreach ($availableYears as $y): ?>
        <option value="<?= $y ?>" <?= ($y==$selectedYear)?'selected':'' ?>><?= $y ?></option>
      <?php endforeach; ?>
    </select>
    <label for="sortSelect">Sort by:</label>
    <select id="sortSelect">
      <option value="new" <?= ($sort === 'new') ? 'selected' : '' ?>>New → Old</option>
      <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>A → Z</option>
    </select>
    <a class="add-btn" href="add_report.php"><i class="fa fa-plus"></i> Add Report</a>
  </div>
</div>

<div class="file-list">
<?php
$defaultQuarterTitles = ['Q1'=>'Quarter January - March','Q2'=>'Quarter April - June','Q3'=>'Quarter July - September','Q4'=>'Quarter October - December'];
foreach ($defaultQuarterTitles as $q => $qTitle):
    $quarterReports = $reportsByQuarter[$q] ?? [];
?>
<div class="file-card">
    <div class="file-title" onclick="toggleQuarter(this)"><?= $qTitle ?> (<?= count($quarterReports) ?> report(s))</div>
    <div class="quarter-reports" style="display:none;">
        <?php if ($quarterReports): ?>
            <?php foreach ($quarterReports as $rep): ?>
                <div class="file-meta">
                    <strong><?= htmlspecialchars($rep['report_title']) ?></strong> — <?= date("M j, Y", strtotime($rep['report_date'])) ?>
                    <div class="file-actions">
                        <a class="file-link" href="view_report.php?id=<?= $rep['id'] ?>">View</a>
                        <a class="file-link" href="./export_report.php?id=<?= $rep['id'] ?>&format=pdf">PDF</a>
                        <a class="file-link" href="./export_report.php?id=<?= $rep['id'] ?>&format=csv">CSV</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#888;">No reports in this quarter</p>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

</main>
</div>
</div>

<script>
document.getElementById('sortSelect').addEventListener('change', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', this.value);
    window.location.href = url.toString();
});
document.getElementById('yearSelect').addEventListener('change', function() {
    const url = new URL(window.location.href);
    url.searchParams.set('year', this.value);
    window.location.href = url.toString();
});
function toggleQuarter(el) {
    const reportsDiv = el.nextElementSibling;
    reportsDiv.style.display = (reportsDiv.style.display === 'none') ? 'block' : 'none';
}
</script>
</body>
</html>
