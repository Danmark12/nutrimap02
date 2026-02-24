<?php
session_start();
require '../db/config.php'; 

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ Only allow BNS
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'BNS') {
    header("Location: ../login.php");
    exit();
}
$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type']; // BNS or CNO

function logActivity($pdo, $user_id, $action) {
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->execute([$user_id, $action]);
}

/* ✅ Handle archive */
if (isset($_POST['archive_id']) && is_numeric($_POST['archive_id'])) {
    $reportId = (int)$_POST['archive_id'];
    $check = $pdo->prepare("
        SELECT * FROM report_archives 
        WHERE report_id = :rid AND user_id = :uid AND user_type = :utype
    ");
    $check->execute([':rid'=>$reportId, ':uid'=>$userId, ':utype'=>$userType]);
    $archive = $check->fetch();

    if ($archive) {
        $update = $pdo->prepare("
            UPDATE report_archives 
            SET is_archived = 1, is_deleted = 0, archived_at = NOW()
            WHERE report_id = :rid AND user_id = :uid AND user_type = :utype
        ");
        $update->execute([':rid'=>$reportId, ':uid'=>$userId, ':utype'=>$userType]);
    } else {
        $insert = $pdo->prepare("
            INSERT INTO report_archives (report_id, user_id, user_type, is_archived, is_deleted, archived_at)
            VALUES (:rid, :uid, :utype, 1, 0, NOW())
        ");
        $insert->execute([':rid'=>$reportId, ':uid'=>$userId, ':utype'=>$userType]);
    }

    logActivity($pdo, $userId, "Archived report (ID: $reportId) as $userType");
    echo json_encode(['success'=>true]);
    exit();
}

/* ✅ Handle submit/unsubmit */
if (isset($_POST['submit_action']) && isset($_POST['report_id'])) {
    $reportId = (int)$_POST['report_id'];
    $action = $_POST['submit_action'];

    if ($action === 'submit') {
        $stmt = $pdo->prepare("UPDATE reports SET is_submitted = 1 WHERE id = :id");
        $stmt->execute([':id' => $reportId]);
        logActivity($pdo, $userId, "Resubmitted report ID $reportId");
        echo json_encode(['success'=>true, 'new_state'=>'submitted']);
        exit();
    } elseif ($action === 'unsubmit') {
        $stmt = $pdo->prepare("UPDATE reports SET is_submitted = 0 WHERE id = :id");
        $stmt->execute([':id' => $reportId]);
        logActivity($pdo, $userId, "Unsubmitted report ID $reportId");
        echo json_encode(['success'=>true, 'new_state'=>'unsubmitted']);
        exit();
    }
}

/* --- Pagination --- */
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/* ✅ Search Filter */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchSQL = '';
if ($search !== '') {
    $searchSQL = " AND b.title LIKE :search ";
}
// --- Sorting ---
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'new'; // default New → Old
$orderSQL = '';
if ($sort === 'new') {
    $orderSQL = " ORDER BY r.report_date DESC, r.report_time DESC ";
} elseif ($sort === 'az') {
    $orderSQL = " ORDER BY b.title ASC ";
}

/* ✅ Fetch reports */
if ($userType === 'BNS') {
 $stmt = $pdo->prepare("
    SELECT DISTINCT r.id, r.report_time, r.report_date, r.status, r.is_submitted,
           u.username,
           COALESCE(b.title, '') AS report_title,
           COALESCE(b.barangay, '') AS barangay
    FROM reports r
    INNER JOIN users u ON r.user_id = u.id
    LEFT JOIN bns_reports b ON b.report_id = r.id
    LEFT JOIN report_archives a 
      ON a.report_id = r.id 
      AND a.user_id = :user_id 
      AND a.user_type = :user_type
    WHERE r.user_id = :user_id2
      AND (r.status = 'Pending' OR r.status = 'Rejected')
      AND (a.is_deleted = 0 OR a.is_deleted IS NULL)
      AND (a.is_archived = 0 OR a.is_archived IS NULL)
      AND b.report_id IS NOT NULL     -- ✅ Only show reports with BNS data
      $searchSQL
      $orderSQL
    LIMIT :limit OFFSET :offset
");
} else {
    $stmt = $pdo->prepare("
        SELECT r.id, r.report_time, r.report_date, r.status, r.is_submitted,
               u.username,
               b.title AS report_title,
               b.barangay
        FROM reports r
        JOIN users u ON r.user_id = u.id
        LEFT JOIN bns_reports b ON b.report_id = r.id
        LEFT JOIN report_archives a 
          ON a.report_id = r.id 
          AND a.user_id = :user_id 
          AND a.user_type = :user_type
        WHERE r.status IN ('Pending', 'Rejected')
          AND r.is_submitted = 1
          AND (a.is_deleted = 0 OR a.is_deleted IS NULL)
          AND (a.is_archived = 0 OR a.is_archived IS NULL)
          $searchSQL
          $orderSQL
        LIMIT :limit OFFSET :offset
    ");
}

$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->bindValue(':user_type', $userType, PDO::PARAM_STR);
if ($userType === 'BNS') {
    $stmt->bindValue(':user_id2', $userId, PDO::PARAM_INT);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
if ($search !== '') {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->execute();
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ✅ Count total */
if ($userType === 'BNS') {
    $totalStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reports r
        LEFT JOIN bns_reports b ON b.report_id = r.id
        LEFT JOIN report_archives a 
          ON a.report_id = r.id 
          AND a.user_id = ? 
          AND a.user_type = ?
        WHERE r.user_id = ?
          AND (r.status = 'Pending' OR r.status = 'Rejected')
          AND (a.is_deleted = 0 OR a.is_deleted IS NULL)
          AND (a.is_archived = 0 OR a.is_archived IS NULL)
          " . ($search !== '' ? "AND b.title LIKE ?" : "") . "
    ");
    $params = [$userId, $userType, $userId];
    if ($search !== '') $params[] = "%$search%";
    $totalStmt->execute($params);
} else {
    $totalStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reports r
        LEFT JOIN bns_reports b ON b.report_id = r.id
        LEFT JOIN report_archives a 
          ON a.report_id = r.id 
          AND a.user_id = ? 
          AND a.user_type = ?
        WHERE r.status IN ('Pending', 'Rejected')
          AND r.is_submitted = 1
          AND (a.is_deleted = 0 OR a.is_deleted IS NULL)
          AND (a.is_archived = 0 OR a.is_archived IS NULL)
          " . ($search !== '' ? "AND b.title LIKE ?" : "") . "
    ");
    $params = [$userId, $userType];
    if ($search !== '') $params[] = "%$search%";
    $totalStmt->execute($params);
}
$totalReports = $totalStmt->fetchColumn();
$totalPages = ceil($totalReports / $limit);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>BNS | Reports</title>
<link rel="icon" type="image/png" href="../img/CNO_Logo.png">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
function archiveReport(reportId) {
  if (confirm('Are you sure you want to move this report to Archive?')) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "reports.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
      if(xhr.readyState === 4 && xhr.status === 200){
        const response = JSON.parse(xhr.responseText);
        if(response.success){
          const row = document.getElementById('report-' + reportId);
          if(row) row.remove();
        } else alert('Failed to archive report');
      }
    };
    xhr.send("archive_id=" + reportId);
  }
}

function toggleSubmit(reportId, action) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "reports.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if(xhr.readyState === 4 && xhr.status === 200){
      try {
        const res = JSON.parse(xhr.responseText);
        if(res.success){
          const btnCell = document.querySelector(`#report-${reportId} .actions`);
          if(res.new_state === 'submitted') {
            btnCell.innerHTML = `
              <a href="view_report.php?id=${reportId}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-blue-600 hover:bg-blue-800 text-sm font-medium"><i class="fa fa-eye"></i> View</a>
              <a href="#" onclick="toggleSubmit(${reportId},'unsubmit')" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-red-600 hover:bg-red-800 text-sm font-medium"><i class="fa fa-undo"></i> Unsubmit</a>
            `;
          } else {
            btnCell.innerHTML = `
              <a href="view_report.php?id=${reportId}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-blue-600 hover:bg-blue-800 text-sm font-medium"><i class="fa fa-eye"></i> View</a>
              <a href="report/edit_report.php?id=${reportId}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-green-600 hover:bg-green-800 text-sm font-medium"><i class="fa fa-edit"></i> Edit</a>
              <a href="#" onclick="archiveReport(${reportId})" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-red-600 hover:bg-red-800 text-sm font-medium"><i class="fa fa-archive"></i> Archive</a>
              <a href="#" onclick="toggleSubmit(${reportId},'submit')" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-teal-600 hover:bg-teal-800 text-sm font-medium"><i class="fa fa-paper-plane"></i> Submit</a>
            `;
          }
        }
      } catch(e) { console.error('Invalid response', e); }
    }
  };
  xhr.send("report_id=" + reportId + "&submit_action=" + action);
}
</script>
</head>
<body class="bg-gray-100 m-0 font-sans">
<div class="flex flex-col h-screen">
<?php include 'header.php'; ?>

<div class="flex flex-1">
<main class="flex-1 p-4 flex flex-col">

<div class="flex justify-between items-center mb-2">
  <div class="flex">
    <form method="get" class="inline-flex">
      <input type="text" id="reportSearch" name="search" placeholder="Search Title" class="border border-gray-300 rounded px-2 py-1 w-56">
      <input type="hidden" name="page" value="1">
    </form>
  </div>
  <div class="flex items-center gap-2">
    <label for="sortSelect" class="text-sm text-gray-700 mr-1">Sort by:</label>
    <select id="sortSelect" name="sort" class="border border-gray-300 rounded px-2 py-1">
      <option value="new" <?= ($sort === 'new') ? 'selected' : '' ?>>New → Old</option>
      <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>A → Z</option>
    </select>
    <a class="inline-flex items-center gap-1 bg-teal-600 hover:bg-teal-800 text-white px-3 py-1 rounded text-sm font-medium" href="add_report.php"><i class="fa fa-plus"></i> Add Report</a>
  </div>
</div>

<div class="flex flex-col bg-white border border-gray-300 rounded flex-1">
    <div class="flex justify-between items-center p-2 bg-gray-200 border-b border-gray-300">
        <h3 class="text-base font-medium m-0">Reports</h3>
        <div class="flex gap-2 mt-2">
            <span class="tab active px-2 py-1 rounded cursor-pointer bg-teal-600 text-white font-medium" data-tab="Pending">Pending</span>
            <span class="tab px-2 py-1 rounded cursor-pointer bg-gray-200 text-gray-800 font-medium" data-tab="Rejected">Rejected</span>
        </div>

        <div class="flex items-center gap-1">
          <a href="?page=<?= max(1, $page-1) ?>" class="border border-gray-300 bg-white px-2 py-1 rounded text-sm">Prev</a>
          <?php
            $maxVisible = 5;
            $startPage = max(1, $page - floor($maxVisible / 2));
            $endPage = min($totalPages, $startPage + $maxVisible - 1);
            if ($endPage - $startPage + 1 < $maxVisible) $startPage = max(1, $endPage - $maxVisible + 1);
            
            if ($startPage > 1) {
                echo '<a href="?page=1" class="border border-gray-300 bg-white px-2 py-1 rounded text-sm">1</a>';
                if ($startPage > 2) echo '<span class="px-2">...</span>';
            }

            for ($i=$startPage; $i <= $endPage; $i++): ?>
                <a href="?page=<?= $i ?>" class="border border-gray-300 px-2 py-1 rounded text-sm <?= $i==$page ? 'bg-teal-600 text-white border-none':'' ?>"><?= $i ?></a>
            <?php endfor;

            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) echo '<span class="px-2">...</span>';
                echo '<a href="?page='.$totalPages.'" class="border border-gray-300 bg-white px-2 py-1 rounded text-sm">'.$totalPages.'</a>';
            }
          ?>
          <a href="?page=<?= min($totalPages, $page+1) ?>" class="border border-gray-300 bg-white px-2 py-1 rounded text-sm">Next</a>
        </div>
    </div>

    <!-- Tab Contents -->
    <?php 
    $tabStatuses = ['Pending', 'Rejected'];
    foreach($tabStatuses as $tabStatus):
        $tabReports = array_filter($reports, function($r) use($tabStatus){ return $r['status']==$tabStatus; });
    ?>
    <div id="<?= $tabStatus ?>-tab" class="tab-content <?= $tabStatus=='Pending'?'block':'hidden' ?>">
        <table class="w-full border-collapse text-sm">
        <thead class="bg-gray-100 font-bold">
        <tr>
          <th class="text-left p-2 border-b border-gray-200">User</th>
          <th class="text-left p-2 border-b border-gray-200">Title</th>
          <th class="text-left p-2 border-b border-gray-200">Barangay</th>
          <th class="text-left p-2 border-b border-gray-200">Time</th>
          <th class="text-left p-2 border-b border-gray-200">Date</th>
          <th class="text-left p-2 border-b border-gray-200">Status</th>
          <th class="text-left p-2 border-b border-gray-200">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($tabReports): ?>
          <?php foreach ($tabReports as $r): ?>
            <tr id="report-<?= $r['id'] ?>">
              <td class="p-2"><?= htmlspecialchars($r['username']) ?></td>
              <td class="p-2"><?= htmlspecialchars($r['report_title'] ?? '-') ?></td>
              <td class="p-2"><?= htmlspecialchars($r['barangay'] ?? '-') ?></td>
              <td class="p-2"><?= date("h:i a", strtotime($r['report_time'])) ?></td>
              <td class="p-2"><?= date("m/d/Y", strtotime($r['report_date'])) ?></td>
              <?php
            $statusLabel = $r['status'];
            if ($r['status'] === 'Rejected' && $r['is_submitted'] == 1) $statusLabel = 'Resubmitted';
            $statusColors = [
                'Pending'=>'bg-yellow-400 text-black',
                'Approved'=>'bg-green-600 text-white',
                'Rejected'=>'bg-red-600 text-white',
                'Archived'=>'bg-gray-500 text-white',
                'Resubmitted'=>'bg-red-400 text-white'
            ];
            ?>
            <td>
              <span class="px-2 py-1 rounded-full text-xs <?= $statusColors[$statusLabel] ?? 'bg-gray-300 text-black' ?>">
                <?= htmlspecialchars($statusLabel) ?>
              </span>
            </td>
              <td class="actions p-2 flex flex-wrap gap-1">
                <a href="view_report.php?id=<?= $r['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-blue-600 hover:bg-blue-800 text-sm font-medium"><i class="fa fa-eye"></i> View</a>
                <?php if ($userType === 'BNS'): ?>
                  <?php if ($r['is_submitted'] == 1): ?>
                    <a href="#" onclick="toggleSubmit(<?= $r['id'] ?>,'unsubmit')" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-red-600 hover:bg-red-800 text-sm font-medium"><i class="fa fa-undo"></i> Unsubmit</a>
                  <?php else: ?>
                    <a href="report/edit_report.php?id=<?= $r['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-green-600 hover:bg-green-800 text-sm font-medium"><i class="fa fa-edit"></i> Edit</a>
                    <a href="#" onclick="archiveReport(<?= $r['id'] ?>)" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-red-600 hover:bg-red-800 text-sm font-medium"><i class="fa fa-archive"></i> Archive</a>
                    <a href="#" onclick="toggleSubmit(<?= $r['id'] ?>,'submit')" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-white bg-teal-600 hover:bg-teal-800 text-sm font-medium"><i class="fa fa-paper-plane"></i> Submit</a>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-gray-400 p-2">No <?= $tabStatus ?> reports available</td></tr>
        <?php endif; ?>
        </tbody>
        </table>
    </div>
    <?php endforeach; ?>
</div>

<script>
const tabs = document.querySelectorAll('.tab');
const contents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active', 'bg-teal-600', 'text-white'));
        tabs.forEach(t => t.classList.add('bg-gray-200', 'text-gray-800'));
        contents.forEach(c => c.classList.add('hidden'));
        contents.forEach(c => c.classList.remove('block'));
        tab.classList.add('active','bg-teal-600','text-white');
        document.getElementById(tab.dataset.tab + '-tab').classList.remove('hidden');
        document.getElementById(tab.dataset.tab + '-tab').classList.add('block');
    });
});

// Auto-search as you type
document.getElementById('reportSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const activeTab = document.querySelector('.tab.active').dataset.tab + '-tab';
    const rows = document.querySelectorAll(`#${activeTab} tbody tr`);
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Optional: sort
document.getElementById('sortSelect').addEventListener('change', function() {
    const sortValue = this.value;
    const searchValue = document.getElementById('reportSearch').value;
    const params = new URLSearchParams(window.location.search);
    params.set('sort', sortValue);
    params.set('search', searchValue);
    params.set('page', 1);
    window.location.search = params.toString();
});
</script>
</body>
</html>

