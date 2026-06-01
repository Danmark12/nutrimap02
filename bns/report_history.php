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
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body { 
      margin:0; 
      font-family: Arial, Helvetica, sans-serif; 
      background:#f5f5f5; 
    }
    
    .layout { 
      display:flex; 
      height:100vh; 
      flex-direction:column; 
    }
    
    .body-layout { 
      flex:1; 
      display:flex; 
      overflow: hidden;
    }
    
    .content { 
      flex:1; 
      padding:20px; 
      display:flex; 
      flex-direction:column;
      overflow-y: auto;
    }
    
    .toolbar { 
      display:flex; 
      align-items:center; 
      justify-content:space-between; 
      margin-bottom:20px;
      background: white;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      flex-wrap: wrap;
      gap: 15px;
    }
    
    .toolbar-left input {
      padding:8px 12px; 
      border:1px solid #ccc; 
      border-radius:4px; 
      width:250px;
      font-size: 14px;
    }
    
    .toolbar-left input:focus {
      outline: none;
      border-color: #009688;
    }
    
    .toolbar-right { 
      display:flex; 
      align-items:center; 
      gap:10px;
      flex-wrap: wrap;
    }
    
    .toolbar-right label { 
      font-size:14px; 
      color:#333; 
      margin-right:4px; 
    }
    
    .toolbar-right select {
      padding:8px 12px; 
      border:1px solid #ccc; 
      border-radius:4px;
      font-size: 14px;
      background: white;
      cursor: pointer;
    }
    
    .toolbar-right select:focus {
      outline: none;
      border-color: #009688;
    }
    
    .add-btn {
      background:#009688; 
      color:#fff; 
      text-decoration:none;
      padding:8px 16px; 
      border-radius:4px; 
      font-size:14px;
      display:inline-flex; 
      align-items:center; 
      gap:6px;
      transition: background 0.3s;
    }
    
    .add-btn:hover { 
      background:#00796b; 
    }

    .report-panel { 
      background:#fff; 
      border:1px solid #e0e0e0; 
      border-radius:8px; 
      flex:1; 
      display:flex; 
      flex-direction:column;
      overflow: hidden;
    }
    
    .report-header {
      display:flex; 
      justify-content:space-between; 
      align-items:center;
      padding:15px 20px; 
      background:#f8f9fa; 
      border-bottom:1px solid #e0e0e0;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .report-header h3 { 
      margin:0; 
      font-size: 18px;
      color: #333;
    }
    
    .report-header h3 i {
      color: #009688;
      margin-right: 8px;
    }
    
    .pagination { 
      display:flex; 
      align-items:center; 
      gap:5px; 
      flex-wrap:wrap; 
    }
    
    .pagination a {
      border:1px solid #ddd; 
      background:#fff; 
      padding:6px 12px;
      cursor:pointer; 
      border-radius:4px; 
      font-size:13px; 
      text-decoration:none; 
      color:#333;
      transition: all 0.2s;
    }
    
    .pagination a:hover {
      background:#f0f0f0;
      border-color: #009688;
    }
    
    .pagination a.active { 
      background:#009688; 
      color:#fff; 
      border-color:#009688;
    }

    .table-wrapper {
      overflow-x: auto;
      flex: 1;
    }
    
    table { 
      width:100%; 
      border-collapse:collapse; 
      font-size:14px; 
    }
    
    th, td { 
      text-align:left; 
      padding:12px 15px; 
      border-bottom:1px solid #f0f0f0; 
    }
    
    th { 
      background:#fafafa; 
      font-weight:600;
      color: #555;
      border-bottom: 2px solid #e0e0e0;
    }
    
    tr:hover {
      background: #f9f9f9;
    }
    
    .status { 
      padding:4px 10px; 
      border-radius:20px; 
      font-size:12px; 
      font-weight: 600;
      color:#fff; 
      background:#009688; 
      display: inline-block;
    }
    
    .actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }
    
    .actions a {
      display:inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration:none; 
      padding:5px 10px; 
      border-radius:4px;
      font-size:12px; 
      transition: all 0.2s;
    }
    
    .actions .view { 
      background:#007bff; 
      color:#fff;
    }
    
    .actions .view:hover { 
      background:#0056b3; 
    }
    
    .actions .edit { 
      background:#ffc107; 
      color:#000;
    }
    
    .actions .edit:hover { 
      background:#e0a800; 
    }
    
    .actions .archive { 
      background:#6c757d; 
      color:#fff;
    }
    
    .actions .archive:hover { 
      background:#5a6268; 
    }
    
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #999;
    }
    
    .empty-state i {
      font-size: 48px;
      margin-bottom: 15px;
      display: block;
    }
    
    @media (max-width: 768px) {
      .content {
        padding: 15px;
      }
      
      .toolbar {
        flex-direction: column;
        align-items: stretch;
      }
      
      .toolbar-right {
        justify-content: space-between;
      }
      
      th, td {
        padding: 8px 10px;
      }
      
      .actions {
        flex-direction: column;
      }
      
      .report-header {
        flex-direction: column;
        align-items: stretch;
      }
      
      .pagination {
        justify-content: center;
      }
    }
    
    /* Scrollbar styling */
    .content::-webkit-scrollbar {
      width: 8px;
    }
    
    .content::-webkit-scrollbar-track {
      background: #f1f1f1;
    }
    
    .content::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 4px;
    }
    
    .content::-webkit-scrollbar-thumb:hover {
      background: #aaa;
    }
  </style>
</head>
<body>
  <div class="layout">
    <?php include 'header.php'; ?>

    <div class="body-layout">
      <main class="content">
        <!-- Simple Top Bar like barangay_data.php -->
        <div class="toolbar">
          <div class="toolbar-left">
            <input type="text" id="reportSearch" placeholder="🔍 Search reports...">
          </div>
          <div class="toolbar-right">
            <label>Sort:</label>
            <select id="sortSelect">
              <option value="new" <?= ($sort === 'new') ? 'selected' : '' ?>>Newest First</option>
              <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>A to Z</option>
            </select>
            <a href="add_report.php" class="add-btn"><i class="fa fa-plus"></i> Add Report</a>
          </div>
        </div>

        <!-- Report Panel -->
        <div class="report-panel">
          <div class="report-header">
            <h3><i class="fa fa-history"></i> My Approved Report History</h3>
            <div class="pagination">
              <a href="?page=<?= max(1,$page-1) ?>&sort=<?= $sort ?>"><i class="fa fa-chevron-left"></i> Prev</a>
              <?php for ($i=1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&sort=<?= $sort ?>" class="<?= $i==$page ? 'active':'' ?>"><?= $i ?></a>
              <?php endfor; ?>
              <a href="?page=<?= min($totalPages,$page+1) ?>&sort=<?= $sort ?>">Next <i class="fa fa-chevron-right"></i></a>
            </div>
          </div>
          
          <div class="table-wrapper">
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
                      <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                      <td><?= date("h:i A", strtotime($r['report_time'])) ?></td>
                      <td><?= date("F d, Y", strtotime($r['report_date'])) ?></td>
                      <td><span class="status"><?= htmlspecialchars($r['status']) ?></span></td>
                      <td class="actions">
                        <a href="view_approved_report.php?id=<?= $r['id'] ?>" class="view"><i class="fa fa-eye"></i> View</a>
                        <a href="edit_approved_report.php?id=<?= $r['id'] ?>" class="edit"><i class="fa fa-pen"></i> Update</a>  
                        <a href="?archive_id=<?= $r['id'] ?>&page=<?= $page ?>&sort=<?= $sort ?>" class="archive" onclick="return confirm('Archive this approved report?');"><i class="fa fa-archive"></i> Archive</a>
                        <!-- Links to export_report.php - same as barangay_data.php -->
                        <a href="export_report.php?id=<?= $r['id'] ?>&format=pdf" class="view" style="background:#dc3545;"><i class="fa fa-file-pdf"></i> PDF</a>
                        <a href="export_report.php?id=<?= $r['id'] ?>&format=csv" class="add-btn" style="background:#28a745;"><i class="fa fa-table"></i> CSV</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6">
                      <div class="empty-state">
                        <i class="fa fa-inbox"></i>
                        <p>No approved reports available</p>
                        <a href="add_report.php" class="add-btn" style="margin-top: 10px; display: inline-flex;">Create Your First Report</a>
                      </div>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Search functionality
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#reportsTable tbody tr');
        let hasResults = false;
        
        rows.forEach(row => {
            // Skip empty state row
            if (row.querySelector('.empty-state')) return;
            
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(filter);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) hasResults = true;
        });
        
        // Show/hide empty message for search
        let emptySearchMsg = document.getElementById('emptySearchMsg');
        if (!hasResults && filter !== '') {
            if (!emptySearchMsg) {
                const tbody = document.querySelector('#reportsTable tbody');
                emptySearchMsg = document.createElement('tr');
                emptySearchMsg.id = 'emptySearchMsg';
                emptySearchMsg.innerHTML = '<td colspan="6"><div class="empty-state"><i class="fa fa-search"></i><p>No matching reports found</p></div></td>';
                tbody.appendChild(emptySearchMsg);
            }
        } else if (emptySearchMsg) {
            emptySearchMsg.remove();
        }
    });

    // Sort functionality - same as barangay_data.php
    document.getElementById('sortSelect').addEventListener('change', function() {
        let url = new URL(window.location.href);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });
    
    // Open first section automatically (if you add quarter sections later)
    document.addEventListener('DOMContentLoaded', function() {
        // Any initialization code
    });
  </script>
</body>
</html>