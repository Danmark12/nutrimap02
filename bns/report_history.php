<?php
session_start();
require '../db/config.php'; 

// ✅ Only allow BNS
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type']; // 'BNS' or 'CNO'

// --- Pagination setup ---
$limit = 10; // reports per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// --- Handle archive action ---
if (isset($_GET['archive_id']) && is_numeric($_GET['archive_id'])) {
    $reportId = (int)$_GET['archive_id'];

    // 🔹 Check if the record exists in report_archives
    $check = $pdo->prepare("
        SELECT * FROM report_archives 
        WHERE report_id = :rid AND user_id = :uid AND user_type = :utype
    ");
    $check->execute([
        'rid' => $reportId,
        'uid' => $userId,
        'utype' => $userType
    ]);
    $existing = $check->fetch();

    if ($existing) {
        // 🔹 Update existing record
        $update = $pdo->prepare("
            UPDATE report_archives 
            SET is_archived = 1, archived_at = NOW() 
            WHERE report_id = :rid AND user_id = :uid AND user_type = :utype
        ");
        $update->execute([
            'rid' => $reportId,
            'uid' => $userId,
            'utype' => $userType
        ]);
    } else {
        // 🔹 Insert new archive record
        $insert = $pdo->prepare("
            INSERT INTO report_archives (report_id, user_id, user_type, is_archived, archived_at) 
            VALUES (:rid, :uid, :utype, 1, NOW())
        ");
        $insert->execute([
            'rid' => $reportId,
            'uid' => $userId,
            'utype' => $userType
        ]);
    }

    // ✅ Redirect to same page
    header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
    exit();
}

// --- Sorting ---
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; // default New → Old
$orderSQL = '';
if ($sort === 'new') {
    $orderSQL = " ORDER BY r.report_date DESC, r.report_time DESC ";
} elseif ($sort === 'az') {
    $orderSQL = " ORDER BY b.title ASC ";
}

// --- Fetch approved reports for this user only (exclude archived) ---
$stmt = $pdo->prepare("
    SELECT r.*, u.username, b.title 
    FROM reports r
    JOIN users u ON r.user_id = u.id
    LEFT JOIN bns_reports b ON b.report_id = r.id
    WHERE r.status = 'Approved'
      AND r.user_id = :uid
      AND r.id NOT IN (
          SELECT report_id FROM report_archives 
          WHERE user_id = :uid2 AND user_type = :utype AND is_archived = 1
      )
      $orderSQL
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
$stmt->bindValue(':uid2', $userId, PDO::PARAM_INT);
$stmt->bindValue(':utype', $userType, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Count total approved reports for this user (exclude archived) ---
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) FROM reports 
    WHERE status = 'Approved' 
      AND user_id = :uid 
      AND id NOT IN (
          SELECT report_id FROM report_archives 
          WHERE user_id = :uid2 AND user_type = :utype AND is_archived = 1
      )
");
$totalStmt->execute([
    'uid' => $userId,
    'uid2' => $userId,
    'utype' => $userType
]);
$totalReports = $totalStmt->fetchColumn();
$totalPages = ceil($totalReports / $limit);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>BNS | History Reports</title>
  <link rel="icon" type="image/png" href="../img/CNO_Logo.png">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin:0; font-family: Arial, Helvetica, sans-serif; background:#f5f5f5; }
    .layout { display:flex; height:100vh; flex-direction:column; }
    .body-layout { flex:1; display:flex; }
    .content { flex:1; padding:15px; display:flex; flex-direction:column; }
    .toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
    .toolbar-left input {
      padding:6px 8px; border:1px solid #ccc; border-radius:4px; width:220px;
    }
    .toolbar-right { display:flex; align-items:center; gap:10px; }
    .toolbar-right label { font-size:14px; color:#333; margin-right:4px; }
    .toolbar-right select {
      padding:6px; border:1px solid #ccc; border-radius:4px;
    }
    .add-btn {
      background:#009688; color:#fff; text-decoration:none;
      padding:8px 14px; border-radius:4px; font-size:14px;
      display:flex; align-items:center; gap:6px;
    }
    .add-btn:hover { background:#00796b; }

    .report-panel { background:#fff; border:1px solid #ccc; border-radius:4px; flex:1; display:flex; flex-direction:column; }
    .report-header {
      display:flex; justify-content:space-between; align-items:center;
      padding:10px; background:#eee; border-bottom:1px solid #ccc;
    }
    .report-header h3 { margin:0; }
    .pagination { display:flex; align-items:center; gap:4px; flex-wrap:wrap; }
    .pagination a {
      border:1px solid #ccc; background:#fff; padding:5px 10px;
      cursor:pointer; border-radius:4px; font-size:14px; text-decoration:none; color:#333;
    }
    .pagination a.active { background:#009688; color:#fff; border:none; }

    table { width:100%; border-collapse:collapse; font-size:14px; }
    th, td { text-align:left; padding:10px; border-bottom:1px solid #eee; }
    th { background:#f5f5f5; font-weight:bold; }
    .status { padding:3px 8px; border-radius:10px; font-size:12px; color:#fff; background:#009688; }
    .actions a {
      display:inline-block; text-decoration:none; padding:5px 10px; border-radius:4px;
      font-size:12px; margin-right:4px; color:#fff;
    }
    .actions .view { background:#007bff; }
    .actions .edit { background:#ffc107; color:#000; }
    .actions .archive { background:#6c757d; }
  </style>
</head>
<body>
  <div class="layout">
    <?php include 'header.php'; ?>

    <div class="body-layout">
      <main class="content">
        <div class="toolbar">
          <div class="toolbar-left">
            <input type="text" id="reportSearch" placeholder="Search">
          </div>
          <div class="toolbar-right">
        <label for="sortSelect">Sort by:</label>
<select id="sortSelect" name="sort">
  <option value="new" <?= ($sort === 'new') ? 'selected' : '' ?>>New → Old</option>
  <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>A → Z</option>
</select>
            <a class="add-btn" href="add_report.php"><i class="fa fa-plus"></i> Add Report</a>
          </div>
        </div>

        <div class="report-panel">
          <div class="report-header">
            <h3>My Approved Report History</h3>
            <div class="pagination">
              <a href="?page=<?= max(1,$page-1) ?>">Prev</a>
              <?php for ($i=1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= $i==$page ? 'active':'' ?>"><?= $i ?></a>
              <?php endfor; ?>
              <a href="?page=<?= min($totalPages,$page+1) ?>">Next</a>
            </div>
          </div>

          <table id="reportsTable">
            <thead>
              <tr>
                <th>User</th>
                <th>Title</th>
                <th>Time</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($reports): ?>
                <?php foreach ($reports as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['username']) ?></td>
                    <td><?= htmlspecialchars($r['title']) ?></td>
                    <td><?= date("h:i a", strtotime($r['report_time'])) ?></td>
                    <td><?= date("m/d/Y", strtotime($r['report_date'])) ?></td>
                    <td><span class="status"><?= htmlspecialchars($r['status']) ?></span></td>
                    <td class="actions">
                      <a href="view_report.php?id=<?= $r['id'] ?>" class="view"><i class="fa fa-eye"></i> View</a>
                    <a href="edit_aproved_report.php?id=<?= $r['id'] ?>" class="edit"><i class="fa fa-pen"></i> Update</a>  
                      <a href="?archive_id=<?= $r['id'] ?>" class="archive" onclick="return confirm('Archive this approved report?');"><i class="fa fa-archive"></i> Archive</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" style="text-align:center; color:#888;">No approved reports available</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>

  <script>
document.getElementById('reportSearch').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#reportsTable tbody tr');
    rows.forEach(row => {
        // Check all cells in the row
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

document.getElementById('sortSelect').addEventListener('change', function() {
    const sortValue = this.value;
    const searchValue = document.getElementById('reportSearch').value;
    // reload page with sort and search preserved
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sortValue);
    params.set('search', searchValue);
    params.set('page', 1); // reset to page 1
    window.location.search = params.toString();
});
</script>

</body>
</html>
