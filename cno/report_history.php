<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../db/config.php';

// Only allow CNO
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'CNO') {
    header("Location: ../login.php");
    exit();
}
$userId   = $_SESSION['user_id'];
$userType = $_SESSION['user_type']; // 'CNO'

// ✅ Set Philippine Timezone
date_default_timezone_set('Asia/Manila');

// ✅ Handle archive action
if (isset($_GET['archive_id']) && is_numeric($_GET['archive_id'])) {
    $reportId = (int)$_GET['archive_id'];

    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ? AND status = 'Approved'");
    $stmt->execute([$reportId]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($report) {
        $check = $pdo->prepare("
            SELECT * FROM report_archives 
            WHERE report_id = ? AND user_id = ? AND user_type = ?
        ");
        $check->execute([$reportId, $userId, $userType]);
        $exists = $check->fetch(PDO::FETCH_ASSOC);

        if ($exists) {
            $update = $pdo->prepare("
                UPDATE report_archives 
                SET is_archived = 1, is_deleted = 0, archived_at = NOW() 
                WHERE report_id = ? AND user_id = ? AND user_type = ?
            ");
            $update->execute([$reportId, $userId, $userType]);
        } else {
            $insert = $pdo->prepare("
                INSERT INTO report_archives (report_id, user_id, user_type, is_archived, archived_at)
                VALUES (?, ?, ?, 1, NOW())
            ");
            $insert->execute([$reportId, $userId, $userType]);
        }
    }

    header("Location: report_history.php?msg=Report archived successfully");
    exit();
}

// --- Filters ---
$search = $_GET['search'] ?? '';
$barangay_filter = $_GET['barangay'] ?? '';
$year_filter = $_GET['year'] ?? '';
$quarter_filter = $_GET['quarter'] ?? '';
$sort = $_GET['sort'] ?? 'date';

// --- Build query ---
$sql = "
    SELECT r.id, r.report_date, r.report_time, b.title, b.barangay, b.year
    FROM reports r
    INNER JOIN bns_reports b ON b.report_id = r.id
    WHERE r.status = 'Approved'
    AND r.id NOT IN (
        SELECT report_id FROM report_archives 
        WHERE user_id = :uid AND user_type = :utype AND (is_archived = 1 OR is_deleted = 1)
    )
";

$params = [
    ':uid' => $userId,
    ':utype' => $userType
];

// search filter
if ($search) {
    $sql .= " AND b.title LIKE :search";
    $params[':search'] = "%$search%";
}

// barangay filter
if ($barangay_filter) {
    $sql .= " AND b.barangay = :barangay";
    $params[':barangay'] = $barangay_filter;
}

// year filter
if ($year_filter) {
    $sql .= " AND b.year = :year";
    $params[':year'] = $year_filter;
}

// quarter filter
if ($quarter_filter) {
    $sql .= " AND QUARTER(r.report_date) = :quarter";
    $params[':quarter'] = $quarter_filter;
}

// sorting
if ($sort === 'name') {
    $sql .= " ORDER BY b.title ASC";
} else {
    $sql .= " ORDER BY b.year DESC, r.report_date DESC, r.report_time DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Convert UTC → Asia/Manila when displaying
foreach ($reports as &$report) {
    $utc = new DateTime($report['report_date'] . ' ' . $report['report_time'], new DateTimeZone('UTC'));
    $utc->setTimezone(new DateTimeZone('Asia/Manila'));
    $report['formatted_datetime'] = $utc->format("M d, Y h:i A"); 
    $report['quarter'] = ceil((int)date('m', strtotime($report['report_date'])) / 3); // Calculate quarter
}
unset($report);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CNO | Barangay Files</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; color:#333; }
    .header { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#fff; border-bottom:1px solid #ddd; }
    .header-left { display:flex; align-items:center; gap:10px; font-weight:bold; }
    .header-left span { color:#009688; }
    .header-center { flex:1; display:flex; justify-content:center; }
    .header-center input { width:250px; padding:6px 8px; border:1px solid #ccc; border-radius:4px; }
    .header-right { display:flex; align-items:center; }
    .header-right i { font-size:18px; cursor:pointer; }
    .container { padding:15px; }
    h2 { margin:0 0 10px 0; font-size:18px; }
    .toolbar { display:flex; align-items:center; gap:10px; margin-bottom:15px; flex-wrap:wrap; }
    .toolbar input, .toolbar select { padding:6px 8px; border:1px solid #ccc; border-radius:4px; }
    .barangay-section { margin-top:25px; }
    .barangay-title { font-weight:bold; margin:15px 0 8px; font-size:16px; color:#009688; }
    .year-title { margin:10px 0; font-weight:bold; color:#444; }
    .quarter-title { margin-left:15px; font-weight:bold; color:#555; }
    .card { background:#fff; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.1); padding:12px 16px; margin-bottom:8px;
            display:flex; justify-content:space-between; align-items:center; font-size:14px; }
    .card-title { font-weight:500; }
    .card-right { display:flex; align-items:center; gap:15px; }
    .export-link { color:#009688; font-weight:bold; text-decoration:none; }
    .export-link:hover { text-decoration:underline; }
    .archive-link { color:#dc3545; font-weight:bold; text-decoration:none; }
    .archive-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
<div class="layout">
<?php include 'header.php'; ?>

  <div class="container">
    <h2>Barangay Files</h2>

    <form method="get" class="toolbar">
      <input type="text" id="reportSearch" name="search" placeholder="Search" value="<?= htmlspecialchars($search) ?>">

      <select name="barangay" onchange="this.form.submit()">
        <option value="">All Barangays</option>
        <?php
        $barangays = [
          'Amoros','Bolisong','Bolobolo','Calongonan','Cogon','Himaya',
          'Hinigdaan','Kalabaylabay','Molugan','Poblacion',
          'Kibonbon','Sambulawan','Sinaloc','Taytay','Ulaliman'    
        ];
        foreach ($barangays as $b) {
            $sel = ($barangay_filter == $b) ? "selected" : "";
            echo "<option value=\"$b\" $sel>$b</option>";
        }
        ?>
      </select>

      <select name="year" onchange="this.form.submit()">
        <option value="">All Years</option>
        <?php
        $years = $pdo->query("SELECT DISTINCT year FROM bns_reports ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($years as $y) {
            $sel = ($year_filter == $y) ? "selected" : "";
            echo "<option value=\"$y\" $sel>$y</option>";
        }
        ?>
      </select>

      <select name="quarter" onchange="this.form.submit()">
        <option value="">All Quarters</option>
        <option value="1" <?= ($quarter_filter=="1")?"selected":"" ?>>January - March</option>
        <option value="2" <?= ($quarter_filter=="2")?"selected":"" ?>>April - June</option>
        <option value="3" <?= ($quarter_filter=="3")?"selected":"" ?>>July - September</option>
        <option value="4" <?= ($quarter_filter=="4")?"selected":"" ?>>October - December</option>
      </select>

      <label for="sort">Sort by:</label>
      <select name="sort" id="sort" onchange="this.form.submit()">
        <option value="date" <?= $sort=="date"?"selected":"" ?>>New - Old</option>
        <option value="name" <?= $sort=="name"?"selected":"" ?>>A - Z</option>
      </select>
      <button type="submit" style="display:none;"></button>
    </form>

    <?php if ($reports): ?>
      <?php
        // Group by Barangay > Year > Quarter
        $grouped = [];
        foreach ($reports as $row) {
            $grouped[$row['barangay']][$row['year']][$row['quarter']][] = $row;
        }

        foreach ($grouped as $brgy => $years) {
            echo "<div class='barangay-section'>";
            echo "<div class='barangay-title'>" . htmlspecialchars($brgy) . "</div>";
            foreach ($years as $yr => $quarters) {
                echo "<div class='year-title'>Year $yr</div>";
                foreach ($quarters as $qtr => $rows) {
                    echo "<div class='quarter-title'>Quarter Q$qtr</div>";
                    foreach ($rows as $row) { 
                        $datetime = $row['formatted_datetime'];
                        ?>
                        <div class="card">
                            <div class="card-title"><?= htmlspecialchars($row['title']) ?></div>
                            <div class="card-right">
                                <div><?= $datetime ?></div>
                                <a href="view_report.php?id=<?= $row['id'] ?>" class="export-link">View</a>
                                <a href="export_barangay.php?id=<?= $row['id'] ?>&format=pdf" class="export-link"><i class="fa fa-file-export"></i> Export PDF</a>
                                <a href="export_barangay.php?id=<?= $row['id'] ?>&format=csv" class="export-link"><i class="fa fa-file-export"></i> Export CSV</a>
                                <a href="report_history.php?archive_id=<?= $row['id'] ?>" class="archive-link" onclick="return confirm('Are you sure you want to archive this file?')">
                                    <i class="fa fa-archive"></i> Archive
                                </a>
                            </div>
                        </div>
                    <?php
                    }
                }
            }
            echo "</div>";
        }
      ?>
    <?php else: ?>
      <p>No approved reports found.</p>
    <?php endif; ?>
  </div>

<script>
document.getElementById('reportSearch').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const cards = document.querySelectorAll('.card'); 
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(filter) ? '' : 'none';
    });

    document.querySelectorAll('.barangay-section').forEach(section => {
        const visibleCards = section.querySelectorAll('.card:not([style*="display: none"])');
        section.style.display = visibleCards.length ? '' : 'none';
    });
});
</script>

</body>
</html>
